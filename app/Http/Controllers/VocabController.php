<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Word;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

const SEPARATOR = "|";

class VocabController extends Controller
{
    public function uploadWords(Request $request) {
        try {
            $changes = json_decode($request->input('payload'), true)["change"];
            Log::info($request->all());
            $image = $request->file('media.images') ?? [];
            $usAudio = $request->file('media.usAudio') ?? [];
            $ukAudio = $request->file('media.ukAudio') ?? [];
            $invalidWords = [];
            foreach ($changes as $index => $change) {
                $validator = Validator::make($change, [
                    'word_id' => 'required|string',
                    'is_deleted' => 'required|boolean',
                    'word' => 'string',
                    'part_of_speech' => 'string',
                    'definition' => 'string',
                    'example' => 'array',
                    'example.*' => 'string',
                    'us_ipa' => 'string|nullable',
                    'uk_ipa' => 'string|nullable',
                    'synonyms' => 'array',
                    'antonyms' => 'array',
                    'family' => 'array',
                    'phrases' => 'array',
                    'synonyms.*' => 'int',
                    'antonyms.*' => 'int',
                    'family.*' => 'int',
                    'phrases.*' => 'int',
                ]);
                $validator->after(function ($validator) use ($image, $usAudio, $ukAudio, $index) {
                    if (array_key_exists($index, $image)) {
                        if ($image[$index]->extension() != 'jpg') {
                            $validator->errors()->add('image', 'Image format must be jpg');
                        } elseif ($image[$index]->getSize() > 1024 * 64) {
                            $validator->errors()->add('image', 'Image size must be less than 64KB');
                        }
                    }
                    if (array_key_exists($index, $usAudio)) {
                        if ($usAudio[$index]->extension() != 'mp3') {
                            $validator->errors()->add('usAudio', 'Audio format must be mp3');
                        } elseif ($usAudio[$index]->getSize() > 1024 * 64) {
                            $validator->errors()->add('usAudio', 'Audio size must be less than 64KB');
                        }
                    }
                    if (array_key_exists($index, $ukAudio)) {
                        if ($ukAudio[$index]->extension() != 'mp3') {
                            $validator->errors()->add('ukAudio', 'Audio format must be mp3');
                        } elseif ($ukAudio[$index]->getSize() > 1024 * 64) {
                            $validator->errors()->add('ukAudio', 'Audio size must be less than 64KB');
                        }
                    }
                });
                if ($validator->fails()) {
                    $invalidWords[] = [
                        'word_id' => $change["word_id"],
                        'errors' => $validator->errors(),
                    ];
                } else {
                    $word = Word::withTrashed()->where('user_id', $request->user()->id)
                        ->where('word_id', $change["word_id"])
                        ->first();
                    if ($change['is_deleted']) {
                        if ($word != null) {
                            $word->delete();
                            $word->updated_at = $change['updated_at'];
                            $word->save();
                        }
                    } else {
                        if ($word != null) {
                            $oldPaths = array($word->us_audio, $word->uk_audio, $word->image);
                            foreach($oldPaths as $oldPath) {
                                if ($oldPath != null && Storage::disk('local')->exists($oldPath)) {
                                    Storage::disk('local')->delete($oldPath);
                                }
                            }
                        }
                        $usPath = null;
                        $ukPath = null;
                        $imgPath = null;
                        if ($usAudio[$change['word_id']] ?? null != null) {
                            $usPath = $usAudio[$change['word_id']]->store('usAudio', 'local');
                            Log::info('US' . $usPath);
                        }
                        if ($ukAudio[$change['word_id']] ?? null != null) {
                            $ukPath = $ukAudio[$change['word_id']]->store('ukAudio', 'local');
                            Log::info('UK' . $ukPath);
                        }
                        if ($image[$change['word_id']] ?? null != null) {
                            $imgPath = $image[$change['word_id']]->store('image', 'local');
                            Log::info('IMG' . $imgPath);
                        }

                        $word = Word::updateOrCreate(
                            [
                                'user_id' => $request->user()->id,
                                'word_id' => $change["word_id"],
                                'created_at' => $change["created_at"],
                            ],
                            [
                                'word' => $change["word"],
                                'type' => $change["part_of_speech"],
                                'definition' => $change["definition"],
                                'example' => implode(SEPARATOR, $change["example"]),
                                'us_ipa' => $change["us_ipa"],
                                'uk_ipa' => $change["uk_ipa"],
                                'us_audio' => $usPath,
                                'uk_audio' => $ukPath,
                                'image' => $imgPath,
                                'synonyms' => implode(SEPARATOR, $change['synonyms']),
                                'antonyms' => implode(SEPARATOR, $change['antonyms']),
                                'family' => implode(SEPARATOR, $change['family']),
                                'phrases' => implode(SEPARATOR, $change['phrases']),
                            ]
                        );
                    }
                }
            }
            Log::info($invalidWords);
            return response()->json([
                "message" => "Sync finished",
                "errors" => $invalidWords,
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                "message" => "Error occured while syncing data.",
            ], 500);
        }
    }

    public function downloadWords(Request $request) {
        try {
            $words = Word::withTrashed()->where('user_id', $request->user()->id)
                ->where('updated_at', '>', $request->timestamp)
                ->get();
            $response = array();
            foreach($words as $word) {
                if ($word->deleted_at == NULL) {
                    $response[] = array(
                        'word_id' => $word->word_id,
                        'deleted' => false,
                        'word' => $word->word,
                        'part_of_speech' => $word->type,
                        'definitions' => $word->definition,
                        'example' => explode(SEPARATOR, $word->example),
                        'us_ipa' => $word->us_ipa,
                        'uk_ipa' => $word->uk_ipa,
                        'us_audio' => $word->us_audio != null,
                        'uk_audio' => $word->uk_audio != null,
                        'image' => $word->image != null,
                        'synonyms' => explode(SEPARATOR, $word->synonyms),
                        'antonyms' => explode(SEPARATOR, $word->antonyms),
                        'family' => explode(SEPARATOR, $word->family),
                        'phrases' => explode(SEPARATOR, $word->phrases),
                        'created_at' => $word->created_at,
                        'updated_at' => $word->updated_at,
                    );
                } else {
                    $response[] = array(
                        'word_id' => $word->word_id,
                        'updated_at' => $word->updated_at,
                        'created_at' => $word->created_at,
                        'deleted' => true,
                    );
                }
            }
            return response()->json($response);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                "message" => "Internal error",
            ], 500);
        }
    }
}
