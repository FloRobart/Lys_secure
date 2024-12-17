{{--
 * Ce fichier fait partie du projet Account manager
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
--}}

<dialog id="password_modal" class="modal rounded-xl p-6">
    <div class="modal-box">
        <!-- Bouton de fermeture de la modal -->
        <form method="dialog" class="flex justify-end items-center w-full">
            <button class="cursor-pointer">
                <svg class="fontSizeIcons" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </form>

        <form id="password_modal_form" method="POST" action="" class="smallColCenterContainer w-full">
            @csrf
            <!-- Données utile pour le formulaire -->
            <input type="hidden" id="account_id"      name="account_id">
            <input type="hidden" id="download_param"  name="download_param">
            <input type="hidden" id="param_separator" name="param_separator">

            <!-- Clé de sécurité -->
            <div>
                @include('components.password-input', ['confirmation' => false, 'newPassword' => false])

                <!-- lien vers la page de changement de clé -->
                <div class="smallRowEndContainer">
                    <a href="{{ route('key.change') }}" class="font fontSizeSmall colorFontBleuLogo font-bold hover:underline" title="Cliquez si vous voulez changer votre clé de sécurité">Changer ma clé de sécurité</a>
                </div>
            </div>

            <!-- Bouton de validation -->
            <button type="submit" class="buttonForm mt-6">Valider</button>
        </form>
    </div>
</dialog>