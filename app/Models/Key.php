<?php
namespace App\Models;

/*
 * Ce fichier fait partie du projet Account Manager
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
 */

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
    use HasFactory;

    protected $connection = 'account_manager';
    protected $table = 'keys';

    protected $fillable = [
        'user_id',
        'key',
    ];
}
