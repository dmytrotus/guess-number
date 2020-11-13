<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = ['playerName', 'from', 'to', 'attempts', 'correct_number'];

    protected $casts = [
        'numbers' => 'array',
    ];
}
