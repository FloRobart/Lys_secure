<?php
namespace App\Http\Controllers;

/*
 * Ce fichier fait partie du projet Lys secure
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
 */

use Illuminate\Http\Request;


class PublicController extends Controller
{
    private $lowercase = 'abcdefghjkmnpqrstuvwxyz';
    private $uppercase = 'ABCDEFGHJKMNPQRSTUVWXYZ';
    private $numbers = '23456789';
    private $specialChars = '!@#$%&*()-_=+{};:|<>?/';
    private $ambigusChars = 'iIlL1o0O,.';

    /**
     * Affiche la page d'accueil
     * @return \Illuminate\View\View accueil
     * @method GET
     */
    public function generatorPassword()
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        $password = $this->generatePassword();
        return view('public.passwordGenerator', ['password' => $password, 'length' => '14', 'lowercase' => $this->lowercase, 'uppercase' => $this->uppercase, 'numbers' => $this->numbers, 'specialChars' => $this->specialChars, 'ambigusChars' => $this->ambigusChars]);
    }

    /**
     * Génère un mot de passe aléatoire sécurisé
     * @return \Illuminate\View\View accueil avec le mot de passe généré
     * @method POST
     */
    public function generatorPasswordPost(Request $request)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        $request->validate([
            'length' => 'required|integer|min:1|max:512',
        ]);

        $password = $this->generatePassword($request->length, $request->lower ?? false, $request->upper ?? false, $request->number ?? false, $request->spe ?? false, $request->amb ?? false);
        return view('public.passwordGenerator', ['password' => $password, 'length' => $request->length, 'lowercase' => $this->lowercase, 'uppercase' => $this->uppercase, 'numbers' => $this->numbers, 'specialChars' => $this->specialChars, 'ambigusChars' => $this->ambigusChars]);
    }

    /**
     * Permet de générer un mot de passe aléatoire sécurisé
     * @param ?int $length Longueur du mot de passe
     * @param ?bool $lower Autoriser les minuscules
     * @param ?bool $upper Autoriser les majuscules
     * @param ?bool $number Autoriser les chiffres
     * @param ?bool $spe Autoriser les caractères spéciaux
     * @param ?bool $amb Autoriser les caractères ambigus
     * @return string Mot de passe généré
     */
    public function generatePassword(?int $length = 14, ?bool $lower = true, ?bool $upper = true, ?bool $number = true, ?bool $spe = true, ?bool $amb = false): string
    {
        /* Définit les ensembles de caractères autorisés */
        $allChars = ($lower ? $this->lowercase : '') . ($upper ? $this->uppercase : '') . ($number ? $this->numbers : '') . ($spe ? $this->specialChars : '') . ($amb ? $this->ambigusChars : '');
        for ($i=0; $i < random_int(5, 50); $i++) { $allChars = str_shuffle($allChars); }

        /* Construction du mot de passe avec des caractères aléatoires sécurisés */
        $password = '';
        $lower  ? ($password .= $this->lowercase   [random_int(0, strlen($this->lowercase   ) - 1)]) : null;
        $upper  ? ($password .= $this->uppercase   [random_int(0, strlen($this->uppercase   ) - 1)]) : null;
        $number ? ($password .= $this->numbers     [random_int(0, strlen($this->numbers     ) - 1)]) : null;
        $spe    ? ($password .= $this->specialChars[random_int(0, strlen($this->specialChars) - 1)]) : null;
        $amb    ? ($password .= $this->ambigusChars[random_int(0, strlen($this->ambigusChars) - 1)]) : null;

        /* Ajout des caractères aléatoires restants */
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        /* Mélanger les caractères pour éviter un ordre prévisible */
        for ($i=0; $i < random_int(1, 100); $i++) {
            $password = str_shuffle($password);
        }

        /* Retourne le mot de passe */
        return $password;
    }
}