<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Word;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function downloadFiles(Request $request) {
        try {
            $word = Word::where('user_id', $request->user()->id)
                ->where('word_id', $request->word_id)
                ->firstOrFail();
            $path = null;
            if ($request->type == 'usAudio') {
                $path = $word->us_audio;
            } elseif ($request->type == 'ukAudio') {
                $path = $word->uk_audio;
            } elseif ($request->type == 'image') {
                $path = $word->image;
            }
            return response()->download(storage_path('app/private/' . $path));
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                "message" => "Internal error",
            ], 500);
        }
    }

    public function downloadAvatar(Request $request) {
        try {
            $user = $request->user();
            return response()->download(storage_path('app/private/' . $user->avatar));
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                "message" => "Internal error",
            ], 500);
        }
    }

    public function downloadLogs(Request $request) {
        try {
            return response()->download(storage_path('logs/laravel.log'));
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                "message" => "Internal error",
            ], 500);
        }
    }
}
