{{--
 * Ce fichier fait partie du projet compte Manager
 * Copyright (C) 2024 Floris Robart <florobart.github.com>
--}}

<!-- Page d'accueil -->
@extends('layouts.page_template')
@section('title')
    Gestionnaire de comptes
@endsection

@section('content')
<!-- Titre de la page -->
<livewire:page-title :title="'Bienvenue sur votre gestionnaire de comptes !'" />

<!-- Messages d'erreur et de succès -->
<div class="colCenterContainer mt-8 px-4">
    @if ($errors->any())
        <div class="rowCenterContainer">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="normalTextError text-center">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <livewire:information-message />
</div>


<!-- Contenu de la page -->
<section class="colCenterContainer space-y-12 mt-4 px-6 mb-32 bgPage">
    @if (session()->has('key_exist'))
        <div class="rowCenterContainer">
            <form action="{{ route('key.check') }}" method="POST" class="colCenterContainer">
                @csrf
                <div class="colCenterContainer">
                    <livewire:password-input :confirmation="'false'" :newPassword="'false'" />
                </div>
                <div class="rowCenterContainer mt-6">
                    <button type="submit" class="buttonForm">Valider</button>
                </div>
            </form>
        </div>
    @else
        <div class="colCenterContainer">
            <span class="normalText text-center">Pour commencer, veuillez créer un mot de passe pour votre gestionnaire de comptes.</span>
            <span class="normalTextAlert text-center font-bold">ATTENTION : Ce mot de passe est obligatoire pour accéder à vos comptes et ne pourra en aucun cas être récupéré.</span>
            <span class="normalTextAlert text-center font-bold">Si vous le perdez, vous ne pourrez plus accéder à vos comptes.</span>
        </div>

        <div class="rowCenterContainer">
            <form action="{{ route('key.save') }}" method="POST" class="colCenterContainer space-y-6">
                @csrf
                <div class="colCenterContainer">
                    <livewire:password-input :confirmation="'false'" :newPassword="'true'" />
                </div>
                <div class="rowCenterContainer">
                    <button type="submit" class="buttonForm">Créer le mot de passe</button>
                </div>
            </form>
        </div>
    @endif
</section>
@endsection

@section('scripts')
    <script src="{{ asset('js/showPassword.js') }}"></script>
@endsection
