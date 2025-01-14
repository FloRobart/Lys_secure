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
@include('components.page-title', ['title' => 'Lys secure'])

<!-- Messages d'erreur et de succès -->
<div class="colCenterContainer mt-8 px-4">
    @include('components.information-message')
</div>


<!-- Contenu de la page -->
<section class="colCenterContainer space-y-12 mt-4 px-6 mb-32 bgPage">
    <!-- Information générale -->
    <div class="colCenterContainer">
        <h2 class="w-full bigTextBleuLogo text-center mb-3">Information générale</h2>

        <!-- Nombre de compte -->
        <div class="rowCenterContainer">
            <span class="normalText">Nombre de compte : <span class="normalTextBleuLogo font-bold">{{ $comptes->count() }}</span></span>
        </div>

        <!-- Nombre de compte différents -->
        <div class="rowCenterContainer">
            @php
                // Supprime les comptes le nom contient un autre nom (ex: "instagram" et "instagram secondaire" compte pour 1, donc seul "instagram" est compté)
                $nameComptes = $comptes->filter(function($compte) use ($comptes) {
                    return $comptes->where('name', '!=', $compte->name)->filter(function($compte2) use ($compte) {
                        return str_contains(strtolower($compte2->name), strtolower($compte->name));
                    })->count() == 0;
                });
            @endphp
            <span class="normalText">Nombre de compte différents : <span class="normalTextBleuLogo font-bold">{{ $nameComptes->count() }}</span></span>
        </div>

        <!-- Nombre d'email différents -->
        <div class="rowCenterContainer">
            <span class="normalText">Nombre d'identifiant différents : <span class="normalTextBleuLogo font-bold">{{ $comptes->unique('email')->count() }}</span></span>
        </div>

        <!-- Nombre d'email différents -->
        <div class="rowCenterContainer">
            @php
                // Récupération des comptes dont le nom contient "mail" et dont l'email contient "@gmail"
                $mailComptes = $comptes->filter(function($compte) {
                    return str_contains(strtolower($compte->name), 'mail') && str_contains(strtolower($compte->email), '@gmail');
                });
            @endphp
            <span class="normalText">Nombre de compte Gmail différents : <span class="normalTextBleuLogo font-bold">{{ $mailComptes->count() }}</span></span>
        </div>

        <!-- Nombre de pseudo différents -->
        <div class="rowCenterContainer">
            @php
                $pseudoCount = $comptes->unique('pseudo')->count();
                if ($comptes->where('pseudo', '-')->count() > 0) {
                    $pseudoCount--;
                }
            @endphp
            <span class="normalText">Nombre de pseudo différents : <span class="normalTextBleuLogo font-bold">{{ $pseudoCount }}</span></span>
        </div>
    </div>

    <!-- Barre de séparation -->
    @include('components.horizontal-separation')

    <div class="colCenterContainer">
        <!-- Titre du tableau -->
        <h2 class="w-full bigTextBleuLogo text-center mb-3">Mes différents comptes</h2>

        <!-- Barre de recherche -->
        <div class="rowStartContainer px-8 space-x-6 mt-4 mb-6">
            <input id="inputSearch" type="text" class="inputForm" placeholder="Rechercher un compte" value="{{ request()->get('search') }}" onkeypress="inputSearch()">
            <button onclick="search('{{  URL::current() . '?search=' }}')" class="buttonForm">Rechercher</button>
        </div>

        <!-- Tableau des comptes -->
        <table class="w-full mt-2">
            <!-- Entête du tableau -->
            <thead class="w-full">
                <tr class="tableRow smallText text-center font-bold">
                    @php request()->get('order') == 'asc' ? $order = 'desc' : $order = 'asc'; @endphp
                    <th class="tableCell" title="Trier par ordre @if ($order == 'asc') alphabétique @else anti-alphabétique @endif du nom"><a href="{{ URL::current() . '?sort=name&order=' . $order . '&search=' . request()->get('search') }}">Nom du compte</a></th>
                    <th class="tableCell" title="Trier par ordre @if ($order == 'asc') alphabétique @else anti-alphabétique @endif de l'email"><a href="{{ URL::current() . '?sort=email&order=' . $order . '&search=' . request()->get('search') }}">Identifiant / Email</a></th>
                    <th class="tableCell">Mot de passe</th>
                    <th class="tableCell" title="Trier par ordre @if ($order == 'asc') alphabétique @else anti-alphabétique @endif du pseudo"><a href="{{ URL::current() . '?sort=pseudo&order=' . $order . '&search=' . request()->get('search') }}">Pseudo</a></th>
                    <th class="tableCell max-md:hidden" title="Trier par ordre chronologique"><a href="{{ URL::current() . '?sort=id&order=' . $order . '&search=' . request()->get('search') }}">Actions</a></th>
                </tr>
            </thead>

            <!-- Contenue du tableau -->
            <tbody class="w-full normalText">
                @if (isset($comptes))
                    @foreach ($comptes as $compte)
                        <tr class="tableRow smallText text-center" id="row_{{ $compte->id }}">
                            <!-- Nom du compte -->
                            @if (str_contains(strtolower(URL::current()), 'name'))
                                <td class="tableCell"><a title="Afficher les comptes {{ $compte->name }}" href="{{ route('comptes.name', ['name' => $compte->name]) }}" class="link">{{ $compte->name }}</a></td>
                            @else
                                @if (str_contains(strtolower(URL::current()), 'email'))
                                    @if (str_contains(strtolower(URL::current()), 'pseudo'))
                                        <td class="tableCell" title="Afficher les comptes {{ $compte->name }} lié au mail {{ $compte->email }} avec le pseudo {{ $compte->pseudo }}"><a href="{{ route('comptes.name.email.pseudo', ['name' => $compte->name, 'email' => $compte->email, 'pseudo' => $compte->pseudo]) }}" class="link">{{ $compte->name }}</a></td>
                                    @else
                                        <td class="tableCell" title="Afficher les comptes {{ $compte->name }} lié au mail {{ $compte->email }}"><a href="{{ route('comptes.name.email', ['name' => $compte->name, 'email' => $compte->email]) }}" class="link">{{ $compte->name }}</a></td>
                                    @endif
                                @else
                                    @if (str_contains(strtolower(URL::current()), 'pseudo'))
                                        <td class="tableCell" title="Afficher les comptes {{ $compte->name }} avec le pseudo {{ $compte->pseudo }}"><a href="{{ route('comptes.name.pseudo', ['name' => $compte->name, 'pseudo' => $compte->pseudo]) }}" class="link">{{ $compte->name }}</a></td>
                                    @else
                                        <td class="tableCell" title="Afficher les comptes {{ $compte->name }}"><a href="{{ route('comptes.name', ['name' => $compte->name]) }}" class="link">{{ $compte->name }}</a></td>
                                    @endif
                                @endif
                            @endif
                            
                            <!-- Email -->
                            @if (str_contains(strtolower(URL::current()), 'email'))
                                <td class="tableCell"><a title="Afficher les comptes lié au mail {{ $compte->email }}" href="{{ route('comptes.email', ['email' => $compte->email]) }}" class="link">{{ $compte->email }}</a></td>
                            @else
                                @if (str_contains(strtolower(URL::current()), 'name'))
                                    @if (str_contains(strtolower(URL::current()), 'pseudo'))
                                        <td class="tableCell" title="Afficher les comptes {{ $compte->name }} lié au mail {{ $compte->email }} avec le pseudo {{ $compte->pseudo }}"><a href="{{ route('comptes.name.email.pseudo', ['name' => $compte->name, 'email' => $compte->email, 'pseudo' => $compte->pseudo]) }}" class="link">{{ $compte->email }}</a></td>
                                    @else
                                        <td class="tableCell" title="Afficher les comptes {{ $compte->name }} lié au mail {{ $compte->email }}"><a href="{{ route('comptes.name.email', ['name' => $compte->name, 'email' => $compte->email]) }}" class="link">{{ $compte->email }}</a></td>
                                    @endif
                                @else
                                    @if (str_contains(strtolower(URL::current()), 'pseudo'))
                                        <td class="tableCell" title="Afficher les comptes lié au mail {{ $compte->email }} et avec le pseudo {{ $compte->pseudo }}"><a href="{{ route('comptes.email.pseudo', ['email' => $compte->email, 'pseudo' => $compte->pseudo]) }}" class="link">{{ $compte->email }}</a></td>
                                    @else
                                        <td class="tableCell" title="Afficher les comptes lié au mail {{ $compte->email }}"><a href="{{ route('comptes.email', ['email' => $compte->email]) }}" class="link">{{ $compte->email }}</a></td>
                                    @endif
                                @endif
                            @endif
                            
                            <!-- Mot de passe -->
                            @if (session()->has('account_id') && session()->get('account_id') == $compte->id)
                                <!-- Affichage du mot de passe -->
                                <td id="visible_password" class="tableCell tooltip md:mt-[-18px] lg:mt-[-25px] xl:mt-[-30px]">
                                    <button title="copier le mot de passe" class="link text-black" onclick="copyToClipboard(this, '{{ str_replace('\'', '\\\'', session()->get('account_password')) }}', '{{ session()->pull('account_id') }}')" onmouseout="tooltip({{ $compte->id }})">
                                        <span id="myTooltip_{{ $compte->id }}" class="tooltiptext">Copier le mot de passe</span>
                                        {{ session()->pull('account_password') }}
                                    </button>
                                </td>
                            @else
                                <!-- SVG à cliquer pour afficher le mot de passe -->
                                <td>
                                    <div class="tableCell rowCenterContainer">
                                        <div class="w-fit link" title="Afficher le mot de passe" onclick="password_modal('{{ route('get.password') }}', '{{ $compte->id }}', null, null)">
                                            <div class="smallRowCenterContainer">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tinySizeIcons">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            @endif

                            <!-- Pseudo -->
                            @if (str_contains(strtolower(URL::current()), 'pseudo'))
                                <td class="tableCell"><a title="Afficher les comptes avec le pseudo {{ $compte->pseudo }}" href="{{ route('comptes.pseudo', ['pseudo' => $compte->pseudo]) }}" class="link">{{ $compte->pseudo }}</a></td>
                            @else
                                @if (str_contains(strtolower(URL::current()), 'email'))
                                    @if (str_contains(strtolower(URL::current()), 'name'))
                                        <td class="tableCell" title="Afficher les comptes {{ $compte->name }} lié au mail {{ $compte->email }} avec le pseudo {{ $compte->pseudo }}"><a href="{{ route('comptes.name.email.pseudo', ['name' => $compte->name, 'email' => $compte->email, 'pseudo' => $compte->pseudo]) }}" class="link">{{ $compte->pseudo }}</a></td>
                                    @else
                                        <td class="tableCell" title="Afficher les comptes lié au mail {{ $compte->email }} et avec le pseudo {{ $compte->pseudo }}"><a href="{{ route('comptes.email.pseudo', ['email' => $compte->email, 'pseudo' => $compte->pseudo]) }}" class="link">{{ $compte->pseudo }}</a></td>
                                    @endif
                                @else
                                    @if (str_contains(strtolower(URL::current()), 'name'))
                                        <td class="tableCell" title="Afficher les comptes {{ $compte->name }} avec le pseudo {{ $compte->pseudo }}"><a href="{{ route('comptes.name.pseudo', ['name' => $compte->name, 'pseudo' => $compte->pseudo]) }}" class="link">{{  $compte->pseudo}}</a></td>
                                    @else
                                        <td class="tableCell" title="Afficher les comptes avec le pseudo {{ $compte->pseudo }}"><a href="{{ route('comptes.pseudo', ['pseudo' => $compte->pseudo]) }}" class="link">{{ $compte->pseudo }}</a></td>
                                    @endif
                                @endif
                            @endif

                            <!-- Actions -->
                            <td class="smallRowCenterContainer px-1 min-[460px]:px-2 min-[500px]:px-4 py-2 max-md:hidden">
                                <!-- Modifier -->
                                <button onclick="editCompte('{{ str_replace('\'', '\\\'', $compte->name) }}', '{{ str_replace('\'', '\\\'', $compte->email) }}', '{{ str_replace('\'', '\\\'', $compte->password) }}', '{{ str_replace('\'', '\\\'', $compte->pseudo) }}', '{{ $compte->id }}')" title="Modifier ce compte" class="smallRowCenterContainer w-fit smallTextReverse font-bold bgBleuLogo hover:bgBleuFonce focus:normalScale rounded-lg min-[500px]:rounded-xl py-1 px-1 min-[500px]:px-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tinySizeIcons">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>

                                <!-- Supprimer -->
                                <button onclick="password_modal('{{ route('compte.remove') }}', '{{ $compte->id }}', null, null)" title="Supprimer ce compte" class="smallRowCenterContainer w-fit smallTextReverse font-bold bgError hover:bgErrorFonce focus:normalScale rounded-lg min-[500px]:rounded-xl py-1 px-1 min-[500px]:px-2 ml-1 min-[500px]:ml-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tinySizeIcons">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach

                    @if ($comptes->count() == 0)
                        @if (request()->get('search') != '')
                            <tr class="tableRow bigText text-center">
                                <td class="tableCell" colspan="5"><b>Aucun compte</b> ne contient le terme "{{ request()->get('search') }}"</td>
                            </tr>
                        @else
                            <tr class="tableRow bigText text-center">
                                <td class="tableCell" colspan="5"><b>Vous n'avez aucun compte pour le moment</td>
                            </tr>
                        @endif
                    @endif
                @endif
            </tbody>
        </table>

        <!-- Formulaire pour ajouter une compte -->
        <form id="form" action="{{ route('compte.add') }}" method="POST" class="rowStartContainer hidden">
            @csrf
            <div class="colCenterContainer">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-x-4">
                    <input id="name"            name="name"     required type="text" placeholder="Nom du compte" class="w-full my-2 text-center inputForm smallText" autofocus>
                    <input id="email"           name="email"    required type="text" placeholder="Identifiant"   class="w-full my-2 text-center inputForm smallText">
                    <input id="accountPassword" name="password"          type="text" placeholder="Mot de passe"  class="w-full my-2 text-center inputForm smallText">
                    <input id="pseudo"          name="pseudo"            type="text" placeholder="Pseudo"        class="w-full my-2 text-center inputForm smallText order-2 lg:order-none">
                    <div class="flex justify-center lg:grid lg:grid-cols-subgrid lg:col-span-1 lg:col-span-4 order-1 lg:order-none">
                        <button type="button" class="col-start-3 text-center colorFontBleuLogo link" onclick="passwordGenerator()">Générer un mot de passe</button>
                    </div>
                </div>

                <!-- Clé de sécurité -->
                <div class="relative w-[80%] lg:w-[55%] my-6">
                    <input type="password" name="key" id="key" class="inputForm text-center smallText" autocomplete="current-password" placeholder="Clé de sécurité" required>
                    <button type="button" class="absolute top-0 end-0 p-1 min-[380px]:p-2 rounded-e-md" title="Afficher la clé de sécurité" onclick="showKey()">
                        <!-- Icône eye fermé -->
                        <svg id="key_svgEyeClose1" class="colorFont fontSizeIcons" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>

                        <!-- Icône eye ouvert -->
                        <svg id="key_svgEyeOpen1" class="hidden colorFont fontSizeIcons" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                </div>

                <button type="submit" id="formButton" class="buttonForm mx-2 min-[500px]:mx-4 my-2">Ajouter</button>
                <div class="w-full tableRowTop"></div>
            </div>
        </form>

        <!-- Bouton pour ajouter un compte -->
        <button onclick="showForm('Ajouter un compte', 'Ajouter', '{{ route('compte.add') }}')" id="button" class="buttonForm mt-8">Ajouter un compte</a>
    </div>

    <!-- Options supplémentaires -->
    <div class="colCenterContainer pt-32">
        @php $param = []; @endphp
        @if (str_contains(strtolower(URL::current()), 'name'  ))
            @php
                $url = parse_url(URL::current())['path'] ?? null;
                if ($url != null) {
                    $urlArray = explode('/', $url);
                    $url_name = array_slice($urlArray, 0, (array_search('name', $urlArray) == false ? count($urlArray) : (array_search('name', $urlArray) + 2)))[3];
                }

                $param = ['name' => $url_name];
            @endphp
        @else
            @php $param = ['name' => 'null']; @endphp
        @endif
        @php
            $param += ['email'  => str_contains(strtolower(URL::current()), 'email' ) ? $comptes->first()->email : 'null' ];
            $param += ['pseudo' => str_contains(strtolower(URL::current()), 'pseudo') ? $comptes->first()->pseudo : 'null'];
            $param += ['search' => request()->get('search') ?? 'null'];
            $param += ['sort'   => request()->get('sort') ?? 'null'];
            $param += ['order'  => request()->get('order') ?? 'null'];
        @endphp

        <!-- Changement de tout les mots de passe -->
        <button type="button" onclick="password_modal('{{ route('modify.password') }}', null, '{{ implode('*****', $param) }}', '*****')" class="buttonForm">Changer tout les mots de passe qui sont affiché</button>

        <!-- Sauvegarder les comptes dans un fichier texte (Markdown) -->
        <button type="button" onclick="password_modal('{{ route('comptes.download') }}', null, '{{ implode('*****', $param) }}', '*****')" class="buttonForm mt-8">Sauvegarder les comptes dans un fichier texte</button>

        <!-- Charger les comptes depuis un fichier texte -->
        @include('components.password-file-modal')
    </div>
</section>

<!-- Modal -->
@include('components.password-modal')
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
<script src="{{ asset('js/showForm.js') }}"></script>
<script src="{{ asset('js/showPassword.js') }}"></script>
<script src="{{ asset('js/showKey.js') }}"></script>
<script>
    oldId = 0;

    /**
     * Permet de scroll dans la page
     */
    onload = function() {
        /* Scroll jusqu'à la barre de recherche */
        if ('{{ request()->get('search') }}' != '' || '{{ request()->get('sort') }}' != '') {
            document.getElementById('inputSearch').scrollIntoView();
        }

        /* Scroll jusqu'au compte avec le mot de passe visible */
        if ('{{ session()->get('account_id') }}' != '' || '{{ session()->get('account_id') }}' != null) {
            visible_password = document.getElementById('visible_password');
            if (visible_password != null) { visible_password.scrollIntoView(); }
        }
    };



    /*===================================*/
    /* Ajout et modification d'un compte */
    /*===================================*/
    /**
     * Permet de modifier un compte
     */
    function editCompte(name, email, password, pseudo, id) {
        /* Affichage du formulaire */
        hidden = document.getElementById('form').classList.contains('hidden');
        if (hidden || oldId == id) {
            showForm('Ajouter un compte', 'Modifier', '{{ route('compte.edit') }}');
        } else {
            document.getElementById('formButton').innerText = 'Modifier';
            document.getElementById('form').action = '{{ route('compte.edit') }}';
        }

        /* Remplissage du formulaire */
        document.getElementById('name').value = name;
        document.getElementById('email').value = email;
        document.getElementById('pseudo').value = pseudo;

        if (document.getElementById('id') != null) {
            document.getElementById('id').remove();
        }
        document.getElementById('form').insertAdjacentHTML('beforeend', '<input type="hidden" id="id" name="id" value="' + id + '">');
        document.getElementById('form').scrollIntoView();

        oldId = id;
    }

    /**
     * Permet de générer un mot de passe aléatoire
     */
    async function passwordGenerator() {
        try {
            const response = await fetch('{{ route('get.new.password') }}');
            if (!response.ok) throw new Error('Erreur lors de la récupération');
            document.getElementById('accountPassword').value = await response.text();
        } catch (error) {
            console.error('Erreur : ', error);
            console.log('Si vous vouyez ce message, prenez une capture d\'écran et contactez l\'administrateur');
        }
    }



    /*======================*/
    /* Affichage des modals */
    /*======================*/
    /**
     * Permet d'afficher la modal pour rentrer la clé de sécurité
     * @param {string} route : route vers laquelle on veut envoyer le formulaire
     * @param {int|null} account_id : id du compte pour lequel on veut afficher le mot de passe
     * @param {string|null} download_param : paramètres pour le téléchargement du fichier texte
     * @param {string|null} param_separator : séparateur pour les paramètres
     */
    function password_modal(route, account_id, download_param, param_separator) {
        document.getElementById('password_modal').showModal();
        password_modal_form = document.getElementById('password_modal_form');

        /* Remplissage des champs pour l'affichage du mot de passe d'un compte */
        if (account_id != null) {
            document.getElementById('account_id').value = account_id;
            
        }

        /* Remplissage des champs pour le téléchargement du fichier texte */
        if (download_param != null && param_separator != null) {
            document.getElementById('download_param').value = download_param;
            document.getElementById('param_separator').value = param_separator;
        }

        password_modal_form.action = route;
    }

    /**
     * Permet d'afficher la modal pour rentrer la clé de sécurité après avoir téléchargé un fichier
     */
    function password_file_modal() {
        document.getElementById('password_file_modal').showModal();
    }

    /**
     * Permet de fermer la modal pour rentrer la clé de sécurité après avoir téléchargé un fichier
     */
    function close_password_file_modal() {
        document.getElementById('password_file_modal').close();
    }

    /**
      * Affichage du mot de passe
      */
    function show_password2() {
        /* Input password */
        var passwordInput1 = document.getElementById("password_file_key");

        /* SVG eyes open */
        var svgEyeOpen1 = document.getElementById("svgEyeOpen0");

        /* SVG eyes close */
        var svgEyeClose1 = document.getElementById("svgEyeClose0");

        /* Affichage du mot de passe + modification de l'icône */
        if (passwordInput1.type === "password")
        {
            /* Affichage du mot de passe */
            passwordInput1.type = "text";
            svgEyeOpen1.classList.remove("hidden");
            svgEyeClose1.classList.add("hidden");
        }
        else
        {
            /* Masquage du mot de passe */
            passwordInput1.type = "password";
            svgEyeOpen1.classList.add("hidden");
            svgEyeClose1.classList.remove("hidden");
        }
    }



    /*=======================*/
    /* Copie du mot de passe */
    /*=======================*/
    /**
     * Copie le texte passé en paramètre dans le presse-papier du système quand il n'y a pas de connexion sécurisée (HTTPS)
     */
    const unsecuredCopyToClipboard = (text, id) => {
        const textArea = document.createElement('textarea');
        textArea.value=text;
        document.getElementById("row_" + id).appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy')
        } catch(err) {
            console.error('Unable to copy to clipboard',err)
        }
        document.getElementById("row_" + id).removeChild(textArea);
        document.getElementById("myTooltip_" + id).innerHTML = "Mot de passe copié";
    };

    /**
     * Copies the text passed as param to the system clipboard
     * Check if using HTTPS and navigator.clipboard is available
     * Then uses standard clipboard API, otherwise uses fallback
    */
    const copyToClipboard = (button, content, id) => {
        if (window.isSecureContext && navigator.clipboard) {
            navigator.clipboard.writeText(content);
        } else {
            unsecuredCopyToClipboard(content, id);
        }

        document.getElementById("myTooltip_" + id).innerHTML = "Mot de passe copié";
        button.classList.remove('text-black');
        button.classList.add('fontColorValid');
    };

    /**
     * Permet de modifier le message lors de la copie du mot de passe
     */
    function tooltip(id) {
        document.getElementById("myTooltip_" + id).innerHTML = "Copier le mot de passe";
    }



    /*===========*/
    /* Recherche */
    /*===========*/
    /**
     * Permet de rechercher un compte
     */
    function search(url) {
        inputSearchValue = document.getElementById('inputSearch').value;
        window.location.href = url + inputSearchValue;
    }

    /**
     * Permet de rechercher un compte lors de l'appui sur la touche "Entrée"
     */
    function inputSearch() {
        if (event.key === 'Enter') {
            search('{{ URL::current() . '?search=' }}');
        }
    }



    /*=========*/
    /* Fichier */
    /*=========*/
    /**
     * Permet de charger un fichier texte
     * Redirige vers la route de charger de fichier texte (comptes.upload) avec le fichier texte en paramètre (avec la méthode POST)
     */
    function validFileForm() {
        document.getElementById('fileForm').submit();
    }
</script>
@endsection
