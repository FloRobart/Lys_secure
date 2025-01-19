<?php
namespace App\Models;

/*
 * Ce fichier fait partie du projet Lys secure
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
 */

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Account extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $connection = 'lys_secure';
    protected $table = 'accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'password',
        'pseudo',
    ];

    /**
     * Permet de convertir le modèle en tableau
     * @return array<string, string> Tableau contenant les attributs du modèle
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'pseudo' => $this->pseudo,
        ];
    }
}
