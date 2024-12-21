<?php
namespace App\Models;

/*
 * Ce fichier fait partie du projet Lys secure
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
 */

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Key extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $connection = 'lys_secure';
    protected $table = 'keys';

    protected $fillable = [
        'user_id',
        'key',
    ];
}
