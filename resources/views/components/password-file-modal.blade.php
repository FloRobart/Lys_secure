{{--
 * Ce fichier fait partie du projet Lys secure
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
--}}

<form id="fileForm" action="{{ route('comptes.upload') }}" method="POST" class="colCenterContainer" enctype="multipart/form-data">
    @csrf
    <label for="file" class="buttonForm mt-8 cursor-pointer">Importer</label>
    <input type="file" id="file" name="file" class="hidden" accept=".txt,.md" onchange="password_file_modal()">

    <!-- Modal -->
    <dialog id="password_file_modal" class="rounded-xl p-6">
        <div class="modal-box">
            <!-- Bouton de fermeture de la modal -->
            <div method="dialog" class="flex justify-end items-center w-full">
                <button class="cursor-pointer" onclick="close_password_file_modal()">
                    <svg class="fontSizeIcons" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Clé de sécurité -->
            <div>
                <div>
                    <label for="password_file_key" class="labelForm">Clé de sécurité @include('components.asterisque')</label>
                    <div class="relative">
                        <input name="password_file_key" id="password_file_key" type="password" minlength="4" autocomplete="current-password" class="inputForm" placeholder="Entrez votre clé de sécurité" value="{{ old('key') }}" required autofocus>
                        <button type="button" class="absolute top-0 end-0 p-1 min-[380px]:p-2 rounded-e-md" title="Afficher la clé de sécurité" onclick="show_password2()">
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

                <!-- lien vers la page de changement de clé -->
                <div class="smallRowEndContainer">
                    <a href="{{ route('key.change') }}" class="font fontSizeSmall colorFontBleuLogo font-bold hover:underline" title="Cliquez si vous voulez changer votre clé de sécurité">Changer ma clé de sécurité</a>
                </div>
            </div>

            <!-- Bouton de validation -->
            <div class="rowCenterContainer">
                <button type="submit" class="buttonForm mt-6">Valider</button>
            </div>
        </div>
    </dialog>
</form>