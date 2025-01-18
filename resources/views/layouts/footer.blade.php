{{--
 * Ce fichier fait partie du projet Lys secure
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
--}}

<footer class="colCenterContainer bgBleuSombre w-full">
    <!-- Lien de retour en haut de la page -->
    <div class="colCenterContainer">
        <div onclick="scrollToTop()" class="colCenterContainer cursor-pointer">
            <svg class="normalIcons normalTextReverse" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd" d="M11.47 7.72a.75.75 0 0 1 1.06 0l7.5 7.5a.75.75 0 1 1-1.06 1.06L12 9.31l-6.97 6.97a.75.75 0 0 1-1.06-1.06l7.5-7.5Z" clip-rule="evenodd" />
            </svg>
            <span class="normalTextReverse -mt-2 lg:-mt-4">Retour en haut de la page</span>
        </div>
    </div>

    <!-- Copiright -->
    <div class="rowCenterContainer space-x-1 mb-4 mt-8">
        <span class="tinyTextReverse">Copyright © 2024 - <script>document.write(new Date().getFullYear())</script>
            <a href="https://florobart.github.io/" target="_blank"><b>Floris Robart</b></a> |
            <a class="link" href="{{ route('cgu') }}">Mentions légales et CGU</a> |
            <a class="link" href="{{ route('contact') }}">Me contacter</a> |
            <a class="link" href="{{ route('bug.report') }}">Signaler un bug</a> |
            <a class="link" href="{{ route('tools.information') }}">Présentation des outils</a>
        </span>
    </div>
</footer>