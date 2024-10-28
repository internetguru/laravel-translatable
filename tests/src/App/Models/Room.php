<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InternetGuru\LaravelTranslatable\Traits\Translatable;

class Room extends Model
{
    use HasFactory;
    use Translatable;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $translatable = [
        'description',
    ];
}
