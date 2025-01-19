<?php
/*
 * Ce fichier fait partie du projet Lys secure
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
 */

use App\Http\Controllers\PrivateController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifIP;



/*=============================*/
/* Route pour les utilisateurs */
/*      PrivateController      */
/*=============================*/
Route::middleware(['auth', VerifIP::class])->group(function () {
    /*---------*/
    /* Accueil */
    /*---------*/
    /* Route vers l'accueil de Lys secure */
    Route::get('/', [PrivateController::class, 'accueil'])->name('accueil');

    /* Route vers l'accueil général du serveur */
    Route::get('/accueil/general', function () { return redirect(env('HOME_SERVER_MAISON') . '/private/accueil'); })->name('accueil.general');


    /*--------*/
    /* Profil */
    /*--------*/
    Route::get('/profil', function () { return redirect(env('HOME_SERVER_MAISON') . '/profil'); })->name('profil');


    /*-----------------*/
    /* Clé de sécurité */
    /*-----------------*/
    /* Enregistrement de la clé de sécurité */
    Route::post('/key/save', [PrivateController::class, 'saveKey'])->name('key.save');

    /* Changement de la clé de sécurité */
    Route::get('/key/change', [PrivateController::class, 'changeKey'])->name('key.change');
    Route::post('/key/change', [PrivateController::class, 'changeKeySave'])->name('key.change.save');


    /*---------------------*/
    /* Gestion des comptes */
    /*---------------------*/
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
    Route::post('/compte/remove', [PrivateController::class, 'removeCompte'])->name('compte.remove');
    Route::post('/compte/share', [PrivateController::class, 'shareCompte'])->name('compte.share');

    /* Route liée au téléchargement des comptes */
    Route::post('/comptes/mes_comptes.md', [PrivateController::class, 'downloadComptes'])->name('comptes.download');
    Route::post('/comptes/upload', [PrivateController::class, 'uploadComptes'])->name('comptes.upload');

    /* Récupération des mots de passe */
    Route::post('/get/password', [PrivateController::class, 'getPassword'])->name('get.password');
    Route::get('/get/new/password', [PrivateController::class, 'getNewPassword'])->name('get.new.password');
    Route::post('/modify/password', [PrivateController::class, 'modifyPassword'])->name('modify.password');
});



/*========================*/
/* Route pour les invités */
/*========================*/
/* Route pour la redirection en cas de mauvaise authentification */
Route::get('/redirection', function () { return redirect(env('HOME_SERVER_MAISON')); })->name('login');

/* CGU, Mentions légales et autres documents */
Route::get('/cgu', function () { return redirect(env('HOME_SERVER_MAISON') . '/cgu'); })->name('cgu');
Route::get('/contact', function () { return redirect(env('HOME_SERVER_MAISON') . '/contact'); })->name('contact');
Route::get('/bug/report', function () { return redirect(env('HOME_SERVER_MAISON') . '/bug/report'); })->name('bug.report');
Route::get('/information', function () { return redirect(env('HOME_SERVER_MAISON') . '/information'); })->name('tools.information');