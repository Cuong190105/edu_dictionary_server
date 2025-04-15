<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FlashcardSet;
use App\Models\Flashcard;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class FlashcardController extends Controller
{
    public function uploadCardSets(Request $request) {
        try {
            Log::info($request->all());
            $user_id = $request->user()->id;
            $payload = $request->payload;
            $invalidCards = [];
            foreach ($payload as $i => $set) {
                $validator = Validator::make($set, [
                    'set_id' => 'required|integer',
                    'name' => 'string|required',
                    'description' => 'string|required',
                    'cards.*.front' => 'string|required',
                    'cards.*.back' => 'string|required',
                ]);
                if ($validator->fails()) {
                    $invalidCards[] = [
                        'index' => $i,
                        'errors' => $validator->errors()->all(),
                    ];
                    continue;
                }
                FlashcardSet::updateOrCreate(
                    [
                        'user_id' => $user_id,
                        'set_id' => $set['set_id'],
                    ],
                    [
                        'name' => $set['name'],
                        'description' => $set['description'],
                        'cards' => json_encode($set['cards']),
                    ]
                );
            }

            return response()->json([
                'message' => 'Flashcard set uploaded successfully',
                'error' => $invalidCards, 
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => 'Internal error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function downloadCardSets(Request $request) {
        try {
            $user_id = $request->user()->id;
            $sets = FlashcardSet::where('user_id', $user_id)
                ->where('updated_at', '>', $request->timestamp)
                ->get()->map(function ($set) {
                return [
                    'set_id' => $set->set_id,
                    'name' => $set->name,
                    'description' => $set->description,
                    'cards' => json_decode($set->cards, true),
                ];
            });
            return response()->json($sets);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
