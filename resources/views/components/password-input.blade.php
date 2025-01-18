{{--
 * Ce fichier fait partie du projet Lys secure
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
--}}

<div class="w-full">
    <label for="password" class="labelForm">@if ($confirmation) Confirmation de la @endif Clé de sécurité @include('components.asterisque')</label>
    <div class="relative">
        <input @if ($confirmation) name="password_confirmation" id="password_confirmation" @else name="password" id="password" @endif type="password" minlength="4" @if ($newPassword) autocomplete="new-password" @else autocomplete="current-password" @endif class="inputForm" placeholder="Entrez votre clé de sécurité" value="{{ old('password') }}" autofocus required>
        <button type="button" class="absolute top-0 end-0 p-1 min-[380px]:p-2 rounded-e-md" title="Afficher la clé de sécurité" onclick="showPassword()">
            <!-- Icône eye fermé -->
            <svg @if ($confirmation) id="svgEyeClose2" @else id="svgEyeClose1" @endif class="colorFont fontSizeIcons" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
            </svg>

            <!-- Icône eye ouvert -->
            <svg @if ($confirmation) id="svgEyeOpen2" @else id="svgEyeOpen1" @endif class="hidden colorFont fontSizeIcons" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
        </button>
    </div>
</div>