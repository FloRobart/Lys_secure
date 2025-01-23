{{--
 * Ce fichier fait partie du projet Lys secure
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
--}}

<!-- Page d'accueil -->
@extends('layouts.page_template')
@section('title')
    Générateur de mot de passe
@endsection

@section('content')
<!-- Titre de la page -->
@include('components.page-title', ['title' => 'Bienvenue sur le générateur de mot de passe de Lys secure !'])

<!-- Messages d'erreur et de succès -->
<div class="colCenterContainer mt-8 px-4">
    @include('components.information-message')
</div>


<!-- Contenu de la page -->
<section class="colCenterContainer space-y-12 mt-4 px-6 mb-32 bgPage">
    <div class="colCenterContainer">
        <span class="font fontSizeBig colorFontBleuLogo font-bold">{{ $password }}</span>
    </div>

    <div class="rowCenterContainer">
        <form action="{{ route('generator.password.post') }}" method="POST" class="colCenterContainer space-y-6">
            @csrf
            <!-- Nombre de caractères -->
            <div class="colStartContainer items-start w-[95%] sm:w-[60%]">
                <label for="length" class="labelForm ml-1">Nombre de caractères @include('components.asterisque')</label>
                <input type="number" id="length" name="length" required class="inputForm" value="{{ $length }}" min="1" max="512">
            </div>

            <div class="rowStartContainer w-[95%] sm:w-[60%]">
                <input type="checkbox" id="lower" name="lower" class="w-5 h-5 cursor-pointer" checked>
                <label for="lower" class="labelForm ml-4 cursor-pointer link">Lettres <b>minuscules</b> <span class="smallText italic">({{ $lowercase }})</span></label>
            </div>

            <div class="rowStartContainer w-[95%] sm:w-[60%]">
                <input type="checkbox" id="upper" name="upper" class="w-5 h-5 cursor-pointer" checked>
                <label for="upper" class="labelForm ml-4 cursor-pointer link">Lettres <b>majuscules</b> <span class="smallText italic">({{ $uppercase }})</span></label>
            </div>

            <div class="rowStartContainer w-[95%] sm:w-[60%]">
                <input type="checkbox" id="number" name="number" class="w-5 h-5 cursor-pointer" checked>
                <label for="number" class="labelForm ml-4 cursor-pointer link"><b>Chiffres</b> <span class="smallText italic">({{ $numbers }})</span></label>
            </div>

            <div class="rowStartContainer w-[95%] sm:w-[60%]">
                <input type="checkbox" id="spe" name="spe" class="w-5 h-5 cursor-pointer" checked>
                <label for="spe" class="labelForm ml-4 cursor-pointer link">Caractères <b>spéciaux</b> <span class="smallText italic">({{ $specialChars }})</span></label>
            </div>

            <div class="rowStartContainer w-[95%] sm:w-[60%]">
                <input type="checkbox" id="amb" name="amb" class="w-5 h-5 cursor-pointer">
                <label for="amb" class="labelForm ml-4 cursor-pointer link">Caractères <b>ambigus</b> <span class="smallText italic">({{ $ambigusChars }})</span></label>
            </div>

            <div class="rowCenterContainer pt-16">
                <button type="submit" class="buttonForm">Générer un mot de passe</button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('styles')
<style>
    /* Style pour le message lors de la copie du mot de passe */
    .tooltip {
        position: relative;
        display: inline-block;
    }

    .tooltip .tooltiptext {
        visibility: hidden;
        width: 100px;
        margin-left: -50px;
        @media (min-width: 768px) { width: 300px; margin-left: -150px; }
        background-color: #555;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        bottom: 150%;
        left: 50%;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .tooltip .tooltiptext::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #555 transparent transparent transparent;
    }

    .tooltip:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }
</style>
@endsection

@section('scripts')
<script>
    /*=======================*/
    /* Copie du mot de passe */
    /*=======================*/
    /**
     * Copie le texte passé en paramètre dans le presse-papier du système quand il n'y a pas de connexion sécurisée (HTTPS)
     */
    const unsecuredCopyToClipboard = (text) => {
        const textArea = document.createElement('textarea');
        textArea.value=text;
        document.getElementById("row").appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy')
        } catch(err) {
            console.error('Unable to copy to clipboard', err)
        }
        document.getElementById("row").removeChild(textArea);
        document.getElementById("myTooltip").innerHTML = "Mot de passe copié";
    };

    /**
     * Copies the text passed as param to the system clipboard
     * Check if using HTTPS and navigator.clipboard is available
     * Then uses standard clipboard API, otherwise uses fallback
    */
    const copyToClipboard = (button, content) => {
        if (window.isSecureContext && navigator.clipboard) {
            navigator.clipboard.writeText(content);
        } else {
            unsecuredCopyToClipboard(content);
        }

        document.getElementById("myTooltip").innerHTML = "Mot de passe copié";
        button.classList.remove('colorFontBleuLogo');
        button.classList.add('fontColorValid');
    };

    /**
     * Permet de modifier le message lors de la copie du mot de passe
     */
    function tooltip() {
        document.getElementById("myTooltip").innerHTML = "Copier le mot de passe";
    }
</script>
@endsection