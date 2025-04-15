<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Word extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'word_id',
        'word',
        'definition',
        'example',
        'us_ipa',
        'uk_ipa',
        'us_audio',
        'uk_audio',
        'image',
        'synonyms',
        'antonyms',
        'family',
        'phrases'
    ];
}
