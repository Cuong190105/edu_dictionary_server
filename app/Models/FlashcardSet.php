<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlashcardSet extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'set_id',
        'name',
        'description',
        'cards',
    ];
}
