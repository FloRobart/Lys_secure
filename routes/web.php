<?php
/*
 * Ce fichier fait partie du projet Account Manager
 * Copyright (C) 2024 Floris Robart <florobart.github.com>
 */

 use App\Http\Controllers\PrivateController;
use Illuminate\Support\Facades\Route;

/*
 * Ce fichier fait partie du projet Finance Dashboard
 * Copyright (C) 2024 Floris Robart <florobart.github.com>
 */


/*-----------------------------*/
/* Route pour les utilisateurs */
/*      PrivateController      */
/*-----------------------------*/
Route::middleware(['auth'])->group(function () {
    /*=========*/
    /* Accueil */
    /*=========*/
    /* Route vers l'accueil du dashboard */
    Route::get('/', [PrivateController::class, 'comptes'])->name('accueil');
    Route::get('/accueil', [PrivateController::class, 'comptes'])->name('accueil');

    /* Route vers l'accueil général du serveur */
    Route::get('/accueil/general', function () { return redirect('http://192.168.1.250:2000/private/accueil'); })->name('accueil.general');


    /*--------*/
    /* Profil */
    /*--------*/
    Route::get('/profil', function () { return redirect('http://192.168.1.250:2000/profil'); })->name('profil');



    /*=========================*/
    /* Gestionnaire de comptes */
    /*=========================*/
    /* Affiche des comptes */
    Route::get('/comptes', [PrivateController::class, 'comptes'])->name('comptes');
    Route::get('/comptes/name/{name}', [PrivateController::class, 'comptes'])->name('comptes.name');
    Route::get('/comptes/email/{email}', [PrivateController::class, 'comptes'])->name('comptes.email');
    Route::get('/comptes/pseudo/{pseudo}', [PrivateController::class, 'comptes'])->name('comptes.pseudo');
    Route::get('/comptes/name/{name}/email/{email}', [PrivateController::class, 'comptes'])->name('comptes.name.email');
    Route::get('/comptes/name/{name}/pseudo/{pseudo}', [PrivateController::class, 'comptes'])->name('comptes.name.pseudo');
    Route::get('/comptes/email/{email}/pseudo/{pseudo}', [PrivateController::class, 'comptes'])->name('comptes.email.pseudo');
    Route::get('/comptes/name/{name}/email/{email}/pseudo/{pseudo}', [PrivateController::class, 'comptes'])->name('comptes.name.email.pseudo');

    /* Ajoute, modifie et supprime des comptes */
    Route::post('/compte/add', [PrivateController::class, 'addCompte'])->name('compte.add');
    Route::post('/compte/edit', [PrivateController::class, 'editCompte'])->name('compte.edit');
    Route::get('/compte/remove/{id}', [PrivateController::class, 'removeCompte'])->name('compte.remove');
});

/* Route pour la redirection en cas de mauvaise authentification */
Route::get('/redirection', function () {
    dd(auth());
    // return redirect('http://192.168.1.250:2000/');
})->name('login');