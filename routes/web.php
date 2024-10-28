<?php
/*
 * Ce fichier fait partie du projet Account Manager
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
 */

use App\Http\Controllers\PrivateController;
use Illuminate\Support\Facades\Route;



/*-----------------------------*/
/* Route pour les utilisateurs */
/*      PrivateController      */
/*-----------------------------*/
Route::middleware(['auth'])->group(function () {
    /*=========*/
    /* Accueil */
    /*=========*/
    /* Route vers l'accueil du gestionnaire */
    Route::get('/', [PrivateController::class, 'accueil'])->name('accueil');
    Route::get('/accueil', [PrivateController::class, 'accueil'])->name('accueil');

    /* Route pour la clé de cryptage */
    Route::post('/key/save', [PrivateController::class, 'saveKey'])->name('key.save');
    Route::post('/key/check', [PrivateController::class, 'checkKey'])->name('key.check');

    /* Route vers l'accueil général du serveur */
    Route::get('/accueil/general', function () { return redirect(env('HOME_SERVER_MAISON') . '/private/accueil'); })->name('accueil.general');


    /*--------*/
    /* Profil */
    /*--------*/
    Route::get('/profil', function () { return redirect(env('HOME_SERVER_MAISON') . '/profil'); })->name('profil');


    /*----------------------------------*/
    /* Changement de la clé de cryptage */
    /*----------------------------------*/
    Route::get('/key/change', [PrivateController::class, 'changeKey'])->name('key.change');
    Route::post('/key/change', [PrivateController::class, 'changeKeySave'])->name('key.change.save');



    /*=========================*/
    /* Gestionnaire de comptes */
    /*=========================*/
    /* Affiche des comptes */
    Route::get('/comptes', [PrivateController::class, 'comptes'])->name('comptes');
    Route::get('/comptes/name/{name}', [PrivateController::class, 'comptesName'])->name('comptes.name');
    Route::get('/comptes/email/{email}', [PrivateController::class, 'comptesEmail'])->name('comptes.email');
    Route::get('/comptes/pseudo/{pseudo}', [PrivateController::class, 'comptesPseudo'])->name('comptes.pseudo');
    Route::get('/comptes/name/{name}/email/{email}', [PrivateController::class, 'comptesNameEmail'])->name('comptes.name.email');
    Route::get('/comptes/name/{name}/pseudo/{pseudo}', [PrivateController::class, 'comptesNamePseudo'])->name('comptes.name.pseudo');
    Route::get('/comptes/email/{email}/pseudo/{pseudo}', [PrivateController::class, 'comptesEmailPseudo'])->name('comptes.email.pseudo');
    Route::get('/comptes/name/{name}/email/{email}/pseudo/{pseudo}', [PrivateController::class, 'comptesNameEmailPseudo'])->name('comptes.name.email.pseudo');

    /* Ajoute, modifie et supprime des comptes */
    Route::post('/compte/add', [PrivateController::class, 'addCompte'])->name('compte.add');
    Route::post('/compte/edit', [PrivateController::class, 'editCompte'])->name('compte.edit');
    Route::get('/compte/remove/{id}', [PrivateController::class, 'removeCompte'])->name('compte.remove');

    /* Route liée au téléchargement des comptes */
    Route::get('/comptes/mes_comptes.md', [PrivateController::class, 'downloadComptes'])->name('comptes.download');
    Route::post('/comptes/upload', [PrivateController::class, 'uploadComptes'])->name('comptes.upload');
});

/* Route pour la redirection en cas de mauvaise authentification */
Route::get('/redirection', function () { return redirect(env('HOME_SERVER_MAISON')); })->name('login');