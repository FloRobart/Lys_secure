{{--
 * Ce fichier fait partie du projet Lys secure
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
--}}

<header id="top" class="colCenterContainer space-y-6 pt-4 bgBleuSombre">
    <!-- Partie haute du header -->
    <div class="rowBetweenContainer">
        <!-- Logo + lien vers la page d'accueil -->
        <div class="smallRowCenterContainer mx-6 min-[400px]:mx-14">
            <a href="{{ route('accueil') }}" class="logo">
                <img class="w-20 sm:w-28" src="{{ asset('favicon.ico') }}" alt="Logo">
            </a>
            <a href="{{ route('accueil') }}" class="logo">
                <span class="normalTextReverse ml-4 font-bold">{{ env('APP_NAME_REAL') }}</span>
            </a>
        </div>

        <!-- Profil -->
        <div class="smallRowCenterContainer mx-6 min-[400px]:mx-14">
            @if (auth()->check())
                <a href="{{ route('profil') }}" class="colorFontReverse hoverText" title="Profil">
            @endif

            <div class="smallRowCenterContainer">
                <!-- Icône du profil -->
                @if (auth()->check())
                    @if (auth()->user()->imgProfil !== null)
                        <img class="w-12 h-12 lg:w-16 lg:h-16 xl:w-20 xl:h-20 overflow-hidden rounded-full" src="data:image/png;base64,{{ auth()->user()->imgProfil }}" alt="Image de profil">
                    @else
                        <svg class="normalIcons colorFontBleuLogo" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    @endif
                @endif

                @if (auth()->check())
                    <span class="normalTextReverse ml-2 flex">
                        {{ auth()->user()->name }}
                    </span>
                @else
                    <!-- Connexion -->
                    <div class="smallRowCenterContainer">
                        <a href="{{ route('accueil') }}" class="hover:underline colorFontReverse" title="Connexion">
                            <span class="smallTextReverse ml-2 flex">Connexion</span>
                        </a>
                    </div>
                @endif
            </div>

            @if (auth()->check())
                </a>
            @endif
        </div>
    </div>

    <!-- Partie basse du header -->
    <div class="rowBetweenContainer bgBleuFonce2 py-3">
        <!-- Fil d'ariane -->
        @php
            $urlInfo = parse_url(URL::current());
            $urlPath = $urlInfo['path'] ?? null;
            $breadcrumb = [];

            if ($urlPath !== null)
            {
                $breadcrumb = [
                    /* Niveau 1 */
                    'comptes' => route('comptes'),

                    /* Niveau 2 */
                    'name' => parseUrl($urlPath, 'name'),

                    /* Niveau 3 */
                    'email' => parseUrl($urlPath, 'email'),

                    /* Niveau 4 */
                    'pseudo' => parseUrl($urlPath, 'pseudo'),
                ];
            }


            function parseUrl(string $url, string $limit)
            {
                $urlArray = explode('/', $url);
                $limitNumber = array_search($limit, $urlArray) == false ? count($urlArray) : (array_search($limit, $urlArray) + 2);

                $urlArray = array_slice($urlArray, 0, $limitNumber);
                $urlArray = implode('/', $urlArray);

                return $urlArray;
            }
        @endphp

        <div id="breadcrumb" class="rowStartContainer px-4 min-[550px]:px-6 sm:px-14">
            <!-- Accueil -->
            <a href="{{ route('accueil.general') }}" class="tinyTextReverse sm:smallTextReverse">{{ env('FLORACCESS_NAME') }}</a>
            @include('components.breadcrumb-link', ['name' => 'Lys secure', 'link' => route('accueil')])

            @foreach ($breadcrumb as $key => $value)
                @if (str_contains(strtolower($urlPath), $key))
                    @include('components.breadcrumb-link', ['name' => ucfirst($key), 'link' => $value])
                @endif
            @endforeach
        </div>
    </div>
</header>
