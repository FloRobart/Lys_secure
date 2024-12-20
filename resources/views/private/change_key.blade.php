{{--
 * Ce fichier fait partie du projet Account manager
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
--}}

@extends('layouts.page_template')
@section('title')
    Changement de clé de sécurité
@endsection

@section('content')
<!-- Titre de la page -->
@include('components.page-title', ['title' => 'Changement de clé de sécurité'])

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
    @include('components.information-message')
</div>

<!-- Formulaire de connexion -->
<section class="bgPage py-6 lg:py-12 px-4 mx-auto max-w-screen-md">
    <form action="{{ route('key.change.save') }}" method="POST" class="space-y-10">
        @csrf
        <!-- clé de sécurité actuel -->
        <div>
            <label for="current_password" class="labelForm">Clé de sécurité actuel @include('components.asterisque')</label>
            <div class="relative">
                <input name="current_password" id="current_password" type="password" minlength="4" maxlength="20" autocomplete="current-password" class="inputForm" placeholder="Entrez votre clé de sécurité" value="" required>
                <button type="button" class="absolute top-0 end-0 p-1 min-[380px]:p-2 rounded-e-md" title="Afficher la clé de sécurité" onclick="showCurrentPassword()">
                    <!-- Icône eye fermé -->
                    <svg id="svgEyeClose0" class="colorFont fontSizeIcons" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>

                    <!-- Icône eye ouvert -->
                    <svg id="svgEyeOpen0" class="hidden colorFont fontSizeIcons" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Nouvelle clé de sécurité -->
        <h2 class="bigText text-center pt-6">Nouvelle clé de sécurité</h2>
        <div class="pb-8">
            <!-- Nouvelle clé de sécurité -->
            @include('components.password-input', ['confirmation' => false, 'newPassword' => true])

            <div class="mt-4"></div>

            <!-- confirmation de la clé de sécurité -->
            @include('components.password-input', ['confirmation' => true, 'newPassword' => true])

            <!-- Générer une clé de sécurité -->
            <div class="flex items-center justify-end">
                <span class="font fontSizeSmall colorFontBleuLogo font-bold hover:underline cursor-pointer" onclick="passwordGenerator()">Suggestion de clé de sécurité sécurisé</a>
            </div>
        </div>

        <!-- bouton de validation -->
        <div class="smallRowStartContainer">
            <button type="submit" class="buttonForm">Changer la clé de sécurité</button>
        </div>
    </form>

    <!-- précision -->
    <div class="smallRowStartContainer mt-3">
        @include('components.asterisque')
        <span class="smallText ml-1">Champs obligatoires</span>
    </div>
</section>
@endsection

@section('scripts')
<script src="{{ asset('js/passwordGenerator.js') }}"></script>
<script src="{{ asset('js/showPassword.js') }}"></script>
<script>
    function showCurrentPassword()
    {
        var password = document.getElementById('current_password');
        var svgEyeClose = document.getElementById('svgEyeClose0');
        var svgEyeOpen = document.getElementById('svgEyeOpen0');

        if (password.type === 'password')
        {
            password.type = 'text';
            svgEyeClose.classList.add('hidden');
            svgEyeOpen.classList.remove('hidden');
        }
        else
        {
            password.type = 'password';
            svgEyeClose.classList.remove('hidden');
            svgEyeOpen.classList.add('hidden');
        }
    }
</script>
@endsection
