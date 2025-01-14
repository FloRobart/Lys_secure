{{--
 * Ce fichier fait partie du projet Lys secure
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
--}}

<!-- Page d'accueil -->
@extends('layouts.page_template')
@section('title')
    Lys secure
@endsection

@section('content')
<!-- Titre de la page -->
@include('components.page-title', ['title' => 'Bienvenue sur Lys secure !'])

<!-- Messages d'erreur et de succès -->
<div class="colCenterContainer mt-8 px-4">
    @include('components.information-message')
</div>


<!-- Contenu de la page -->
<section class="colCenterContainer space-y-12 mt-4 px-6 mb-32 bgPage">
    <div class="colCenterContainer">
        <span class="bigText text-center">Pour commencer, veuillez créer une clé de sécurité pour acceder à vos compte enregistrer dans Lys secure.</span>
        <span class="bigTextAlert text-center font-bold">ATTENTION : Cette clé est obligatoire pour accéder à vos comptes et ne pourra en aucun cas être récupéré.</span>
        <span class="bigTextAlert text-center font-bold">Si vous la perdez, vous ne pourrez plus accéder à vos comptes.</span>
        <span class="normalTextAlert text-center font-bold">Il est donc fortement recommandé de la noter dans un endroit sûr.</span>
    </div>

    <div class="rowCenterContainer">
        <form action="{{ route('key.save') }}" method="POST" class="colCenterContainer space-y-6">
            @csrf
            <div class="colCenterContainer">
                @include('components.password-input', ['confirmation' => false, 'newPassword' => true])
            </div>

            <div class="colCenterContainer">
                @include('components.password-input', ['confirmation' => true, 'newPassword' => true])
            </div>

            <div class="rowCenterContainer">
                <button type="submit" class="buttonForm">Créer la clé de sécurité</button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('scripts')
    <script src="{{ asset('js/showPassword.js') }}"></script>
@endsection
