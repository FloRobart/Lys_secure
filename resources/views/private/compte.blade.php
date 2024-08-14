{{--
 * Ce fichier fait partie du projet Account manager
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
--}}

<!-- Page d'accueil -->
@extends('layouts.page_template')
@section('title')
    Gestionnaire de comptes
@endsection

@section('content')
<!-- Titre de la page -->
<livewire:page-title :title="'Gestionnaire de comptes'" />

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
                $pseudoCount = $comptes->unique('pseudo')->count()
                if ($comptes->where('pseudo', '-')->count() > 0) {
                    $pseudoCount--;
                }
            @endphp
            <span class="normalText">Nombre de pseudo différents : <span class="normalTextBleuLogo font-bold">{{ $pseudoCount }}</span></span>
        </div>
    </div>

    <!-- Barre de séparation -->
    <livewire:horizontal-separation />

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
                    <th class="tableCell max-md:hidden" title="Trier par ordre @if ($order == 'asc') alphabétique @else anti-alphabétique @endif du pseudo"><a href="{{ URL::current() . '?sort=pseudo&order=' . $order . '&search=' . request()->get('search') }}">Pseudo</a></th>
                    <th class="tableCell max-md:hidden" title="Trier par ordre chronologique"><a href="{{ URL::current() . '?sort=created_at&order=' . $order . '&search=' . request()->get('search') }}">Actions</a></th>
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
                            <td class="tableCell tooltip md:mt-[-18px] lg:mt-[-25px] xl:mt-[-30px]">
                                <button title="copier le mot de passe" class="link" onclick="copyToClipboard('{{ str_replace('\'', '\\\'', $compte->password) }}', '{{ $compte->id }}')" onmouseout="tooltip({{ $compte->id }})">
                                    <span class="tooltiptext" id="myTooltip_{{ $compte->id }}">Copier le mot de passe</span>
                                    {{ $compte->password }}
                                </button>
                            </td>
                            
                            <!-- Pseudo -->
                            @if (str_contains(strtolower(URL::current()), 'pseudo'))
                                <td class="tableCell max-md:hidden"><a title="Afficher les comptes avec le pseudo {{ $compte->pseudo }}" href="{{ route('comptes.pseudo', ['pseudo' => $compte->pseudo]) }}" class="link">{{ $compte->pseudo }}</a></td>
                            @else
                                @if (str_contains(strtolower(URL::current()), 'email'))
                                    @if (str_contains(strtolower(URL::current()), 'name'))
                                        <td class="tableCell max-md:hidden" title="Afficher les comptes {{ $compte->name }} lié au mail {{ $compte->email }} avec le pseudo {{ $compte->pseudo }}"><a href="{{ route('comptes.name.email.pseudo', ['name' => $compte->name, 'email' => $compte->email, 'pseudo' => $compte->pseudo]) }}" class="link">{{ $compte->pseudo }}</a></td>
                                    @else
                                        <td class="tableCell max-md:hidden" title="Afficher les comptes lié au mail {{ $compte->email }} et avec le pseudo {{ $compte->pseudo }}"><a href="{{ route('comptes.email.pseudo', ['email' => $compte->email, 'pseudo' => $compte->pseudo]) }}" class="link">{{ $compte->pseudo }}</a></td>
                                    @endif
                                @else
                                    @if (str_contains(strtolower(URL::current()), 'name'))
                                        <td class="tableCell max-md:hidden" title="Afficher les comptes {{ $compte->name }} avec le pseudo {{ $compte->pseudo }}"><a href="{{ route('comptes.name.pseudo', ['name' => $compte->name, 'pseudo' => $compte->pseudo]) }}" class="link">{{  $compte->pseudo}}</a></td>
                                    @else
                                        <td class="tableCell max-md:hidden" title="Afficher les comptes avec le pseudo {{ $compte->pseudo }}"><a href="{{ route('comptes.pseudo', ['pseudo' => $compte->pseudo]) }}" class="link">{{ $compte->pseudo }}</a></td>
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
                                <a href="{{ route('compte.remove', $compte->id) }}" onclick="return confirm('Êtes-vous sûr de vouloir supprimer le compte {{ str_replace('\'', '\\\'', $compte->name) }} associé à l\'email {{ str_replace('\'', '\\\'', $compte->email) }} ? Cette action est irréversible.')" title="Supprimer ce compte" class="smallRowCenterContainer w-fit smallTextReverse font-bold bgError hover:bgErrorFonce focus:normalScale rounded-lg min-[500px]:rounded-xl py-1 px-1 min-[500px]:px-2 ml-1 min-[500px]:ml-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tinySizeIcons">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </a>
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
                <div class="colStartContainer lg:rowStartContainer">
                    <input id="name"     name="name"     required type="text" placeholder="Nom du compte" class="w-[55%] mx-2 min-[500px]:mx-4 my-2 text-center inputForm smallText">
                    <input id="email"    name="email"    required type="text" placeholder="Email"         class="w-[55%] mx-2 min-[500px]:mx-4 my-2 text-center inputForm smallText">
                    <input id="password" name="password" required type="text" placeholder="Mot de passe"  class="w-[55%] mx-2 min-[500px]:mx-4 my-2 text-center inputForm smallText">
                    <input id="pseudo"   name="pseudo"            type="text" placeholder="Pseudo"        class="w-[55%] mx-2 min-[500px]:mx-4 my-2 text-center inputForm smallText">
                </div>
                <button type="button" class="buttonForm" onclick="passwordGenerator()">Générer un mot de passe</button>
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
        @endif
        @if (str_contains(strtolower(URL::current()), 'email' )) @php $param += ['email'  => $comptes->first()->email ]; @endphp @endif
        @if (str_contains(strtolower(URL::current()), 'pseudo')) @php $param += ['pseudo' => $comptes->first()->pseudo]; @endphp @endif
        @php
            $param += ['search' => request()->get('search')];
            $param += ['sort'   => request()->get('sort')];
            $param += ['order'  => request()->get('order')];
        @endphp
        
        <!-- sauvegarder les comptes dans un fichier texte (Markdown) -->
        <a href="{{ route('comptes.download', $param) }}" class="buttonForm">Sauvegarder les comptes dans un fichier texte</a>

        <!-- Charger les comptes depuis un fichier texte -->
        <form id="fileForm" action="{{ route('comptes.upload') }}" method="POST" class="colCenterContainer" enctype="multipart/form-data">
            @csrf
            <label for="file" class="buttonForm mt-8 cursor-pointer">Charger les comptes depuis un fichier texte</label>
            <input type="file" id="file" name="file" class="hidden" accept=".txt,.md" onchange="validFileForm()">
        </form>
    </div>
</section>
@endsection

@section('scripts')
<script src="{{ asset('js/showForm.js') }}"></script>
<script src="{{ asset('js/passwordGenerator.js') }}"></script>
<script>
    oldId = 0;

    /**
     * Permet de scroll jusqu'à la barre de recherche
     */
    onload = function() {
        if ('{{ request()->get('search') }}' != '' || '{{ request()->get('sort') }}' != '') {
            document.getElementById('inputSearch').scrollIntoView();
        }
    }

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
        document.getElementById('password').value = password;
        document.getElementById('pseudo').value = pseudo;

        if (document.getElementById('id') != null) {
            document.getElementById('id').remove();
        }
        document.getElementById('form').insertAdjacentHTML('beforeend', '<input type="hidden" id="id" name="id" value="' + id + '">');
        document.getElementById('form').scrollIntoView();

        oldId = id;
    }

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
    const copyToClipboard = (content, id) => {
        if (window.isSecureContext && navigator.clipboard) {
            navigator.clipboard.writeText(content);
        } else {
            unsecuredCopyToClipboard(content, id);
        }

        document.getElementById("myTooltip_" + id).innerHTML = "Mot de passe copié";
    };

    /**
     * Permet de modifier le message lors de la copie du mot de passe
     */
    function tooltip(id)
    {
        document.getElementById("myTooltip_" + id).innerHTML = "Copier le mot de passe";
    }

    /**
     * Permet de rechercher un compte
     */
    function search(url)
    {
        inputSearchValue = document.getElementById('inputSearch').value;
        window.location.href = url + inputSearchValue;
    }

    /**
     * Permet de rechercher un compte lors de l'appui sur la touche "Entrée"
     */
    function inputSearch()
    {
        if (event.key === 'Enter') {
            search('{{ URL::current() . '?search=' }}');
        }
    }

    /**
     * Permet de charger un fichier texte
     * Redirige vers la route de charger de fichier texte (comptes.upload) avec le fichier texte en paramètre (avec la méthode POST)
     */
    function validFileForm()
    {
        document.getElementById('fileForm').submit();
    }
</script>
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
