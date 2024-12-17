<?php
namespace App\Http\Controllers;

/*
 * Ce fichier fait partie du projet Account Manager
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
 */

use App\Models\Account;
use App\Models\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class PrivateController extends Controller
{
    /*=========*/
    /* Accueil */
    /*=========*/
    /**
     * Affiche l'accueil
     * @return \Illuminate\View\View private.accueil | pour la première connexion
     * @return \Illuminate\Http\RedirectResponse comptes | pour les autres connexions
     */
    public function accueil()
    {
        $key = Key::where('user_id', Auth::user()->id)->first();
        return $key == null ? view('private.accueil') : redirect()->route('comptes');
    }



    /*------------------*/
    /* Gestion des clés */
    /*------------------*/
    /**
     * Sauvegarde la clé de sécurité
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse Retourne la page précédente
     */
    public function saveKey(Request $request)
    {
        /* Validation des données */
        $request->validate([
            'password' => 'required|string|min:' . env('KEY_MIN_LENGTH', 12) . '|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).*$/',
            'password_confirmation' => 'required|string|min:' . env('KEY_MIN_LENGTH', 12) . '|same:password',
        ], [
            'password.required' => 'La clé de sécurité est obligatoire.',
            'password.string' => 'La clé de sécurité doit être une chaîne de caractères.',
            'password.min' => 'La clé de sécurité doit contenir au moins ' . env('KEY_MIN_LENGTH', 12) . ' caractère.',
            'password.max' => 'La clé de sécurité ne doit pas dépasser 255 caractères.',
            'password.regex' => 'La clé de sécurité doit contenir au moins une lettre minuscule, une lettre majuscule et un chiffre.',
            'password_confirmation.required' => 'La confirmation du clé de sécurité est obligatoire.',
            'password_confirmation.string' => 'La confirmation du clé de sécurité doit être une chaîne de caractères.',
            'password_confirmation.min' => 'La confirmation du clé de sécurité doit contenir au moins ' . env('KEY_MIN_LENGTH', 12) . ' caractère.',
            'password_confirmation.same' => 'Les mots de passe ne correspondent pas.',
        ]);

        /* Sauvegarde de la clé de sécurité */
        $key = new Key();
        $key->user_id = Auth::user()->id;
        $key->key = Hash::make($request->password);

        if ($key->save()) {
            LogController::addLog('Sauvegarde de la clé de sécurité');
            return back()->with('success', 'La clé de sécurité a été sauvegardée avec succès 👍.');
        } else {
            LogController::addLog('Erreur lors de la sauvegarde de la clé de sécurité', null, 1);
            return back()->with('error', 'Une erreur est survenue lors de la sauvegarde de la clé de sécurité.');
        }
    }

    /**
     * Vérifie la clé de sécurité
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse comptes | avec l'id et le mot de passe d'un des comptes
     */
    public function getPassword(Request $request)
    {
        /* Validation des données */
        $request->validate([
            'account_id' => 'required|min:1|exists:account_manager.accounts,id',
            'password' => 'required|string',
        ], [
            'account_id.required' => 'Vous n\'êtes pas censé modifier cette valeur 1',
            'account_id.min' => 'Vous n\'êtes pas censé modifier cette valeur 2',
            'account_id.exists' => 'Vous n\'êtes pas censé modifier cette valeur 3',
            'password.required' => 'La clé de sécurité est obligatoire.',
            'password.string' => 'La clé de sécurité doit être une chaîne de caractères.',
        ]);

        /* Vérification du propriétaire du compte */
        $compte = Account::find($request->account_id);
        if ($compte->user_id != Auth::user()->id) {
            LogController::addLog('Tentative de récupération d\'un mot de passe d\'un compte qui n\'appartient pas à la personne connecté', null, 1);
            return back()->with('error', 'Ce compte ne vous appartient pas et cette action a été reportée à l\'administrateur ❌.');
        }

        /* Vérification de la clé de sécurité */
        $key = Key::where('user_id', Auth::user()->id)->first();
        if ($key && Hash::check($request->password, $key->key)) {
            LogController::addLog('Vérification d\'une clé de sécurité correcte');
            return redirect()->route('comptes')->with(['account_id' => $compte->id, 'account_password' => $this->decryptPassword($compte->id, $key->key)]);
        }

        LogController::addLog('Vérification d\'une clé de sécurité incorrecte', null, 1);
        return back()->with('error', 'Le clé de sécurité est incorect ❌.');
    }



    /*----------------------------------*/
    /* Changement de la clé de sécurité */
    /*----------------------------------*/
    /**
     * Affiche la page de changement de la clé de sécurité
     * @return \Illuminate\View\View private.change_key
     */
    public function changeKey()
    {
        return view('private.change_key');
    }

    /**
     * Sauvegarde la nouvelle clé de sécurité et encrypte les mots de passe avec la nouvelle clé
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse comptes
     */
    public function changeKeySave(Request $request)
    {
        /* Validation des données */
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:' . env('KEY_MIN_LENGTH', 12) . '|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).*$/',
            'password_confirmation' => 'required|string|same:password',
        ], [
            'current_password.required' => 'L\'ancienne clé de sécurité est obligatoire.',
            'current_password.string' => 'L\'ancienne clé de sécurité doit être une chaîne de caractères.',
            'password.required' => 'La nouvelle clé de sécurité est obligatoire.',
            'password.string' => 'La nouvelle clé de sécurité doit être une chaîne de caractères.',
            'password.min' => 'La nouvelle clé de sécurité doit contenir au moins ' . env('KEY_MIN_LENGTH', 12) . ' caractère.',
            'password.max' => 'La nouvelle clé de sécurité ne doit pas dépasser 255 caractères.',
            'password.regex' => 'La nouvelle clé de sécurité doit contenir au moins une lettre minuscule, une lettre majuscule et un chiffre.',
            'password_confirmation.required' => 'La confirmation de la nouvelle clé de sécurité est obligatoire.',
            'password_confirmation.string' => 'La confirmation de la nouvelle clé de sécurité doit être une chaîne de caractères.',
            'password_confirmation.same' => 'Les mots de passe ne correspondent pas.',
        ]);

        /* Mise en place des variables */
        $old_key = $request->current_password;
        $new_key = $request->password;

        /* Vérification de l'ancienne clé de sécurité */
        $key = Key::where('user_id', Auth::user()->id)->first();
        if (!$key || !Hash::check($old_key, $key->key)) {
            LogController::addLog('Tentative de modification de la clé de sécurité avec une ancienne clé incorrecte', null, 1);
            return back()->with('error', 'Votre clé de sécurité actuel est incorrect.');
        }

        /* Sauvegarde de la nouvelle clé de sécurité */
        $key->key = Hash::make($new_key);

        /* Modification de la clé de sécurité */
        if ($key->save()) {
            /* Récupération des comptes */
            $comptes = PrivateController::getComptes('', '', '');

            /* Chiffrement des mots de passe */
            foreach ($comptes as $compte) {
                $compte->password = $this->encryptPassword($this->decryptPassword($compte->id, $old_key), $new_key);
                if (!$compte->save()) {
                    LogController::addLog('Erreur lors de la modification de la clé de sécurité', null, 1);
                    return back()->with('error', 'Une erreur est survenue lors de la modification de la clé de sécurité.');
                }
            }

            LogController::addLog('Modification de la clé de sécurité');
            return redirect()->route('comptes')->with('success', 'La clé de sécurité a été modifiée avec succès 👍.');
        } else {
            LogController::addLog('Erreur lors de la modification de la clé de sécurité', null, 1);
            return back()->with('error', 'Une erreur est survenue lors de la modification de la clé de sécurité.');
        }
    }




    /*========*/
    /* Compte */
    /*========*/
    /*-----------------------*/
    /* Affichage des comptes */
    /*-----------------------*/
    /**
     * Affiche tous les comptes
     * @param Request $request
     * @return \Illuminate\View\View private.compte
     */
    public function comptes(Request $request)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        $sort = $request->query('sort') ?? 'id';
        $order = $request->query('order') ?? 'desc';
        $search = $request->query('search') ?? '';

        /* Récupération des comptes */
        $comptes = PrivateController::getComptes('', '', '', $sort, $order);
        if ($search != '') { $comptes = PrivateController::getComptesSearch($comptes, $search, $sort, $order); }

        return view('private.compte', compact('comptes'));
    }

    /**
     * Affiche tous les comptes d'un même nom
     * @param Request $request
     * @param string $name Nom du compte
     * @return \Illuminate\View\View private.compte
     */
    public function comptesName(Request $request, string $name)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        $sort = $request->query('sort') ?? 'id';
        $order = $request->query('order') ?? 'desc';
        $search = $request->query('search') ?? '';

        /* Récupération des comptes */
        $comptes = PrivateController::getComptes($name, '', '', $sort, $order);
        if ($search != '') { $comptes = PrivateController::getComptesSearch($comptes, $search, $sort, $order); }

        return view('private.compte', compact('comptes'));
    }

    /**
     * Affiche tous les comptes d'un même email
     * @param Request $request
     * @param string $email Identifiant du compte
     * @return \Illuminate\View\View private.compte
     */
    public function comptesEmail(Request $request, string $email)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        $sort = $request->query('sort') ?? 'id';
        $order = $request->query('order') ?? 'desc';
        $search = $request->query('search') ?? '';

        /* Récupération des comptes */
        $comptes = PrivateController::getComptes('', $email, '', $sort, $order);
        if ($search != '') { $comptes = PrivateController::getComptesSearch($comptes, $search, $sort, $order); }

        return view('private.compte', compact('comptes'));
    }

    /**
     * Affiche tous les comptes d'un même pseudo
     * @param Request $request
     * @param string $pseudo Pseudo du compte
     * @return \Illuminate\View\View private.compte
     */
    public function comptesPseudo(Request $request, string $pseudo)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        $sort = $request->query('sort') ?? 'id';
        $order = $request->query('order') ?? 'desc';
        $search = $request->query('search') ?? '';

        /* Récupération des comptes */
        $comptes = PrivateController::getComptes('', '', $pseudo, $sort, $order);
        if ($search != '') { $comptes = PrivateController::getComptesSearch($comptes, $search, $sort, $order); }

        return view('private.compte', compact('comptes'));
    }

    /**
     * Affiche les comptes d'un même nom et d'un même email
     * @param Request $request
     * @param string $name Nom du compte
     * @param string $email Identifiant du compte
     * @return \Illuminate\View\View private.compte
     */
    public function comptesNameEmail(Request $request, string $name, string $email)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        $sort = $request->query('sort') ?? 'id';
        $order = $request->query('order') ?? 'desc';
        $search = $request->query('search') ?? '';

        /* Récupération des comptes */
        $comptes = PrivateController::getComptes($name, $email, '', $sort, $order);
        if ($search != '') { $comptes = PrivateController::getComptesSearch($comptes, $search, $sort, $order); }

        return view('private.compte', compact('comptes'));
    }

    /**
     * Affiche les comptes d'un même name et d'un même pseudo
     * @param Request $request
     * @param string $name Nom du compte
     * @param string $pseudo Pseudo du compte
     * @return \Illuminate\View\View private.compte
     */
    public function comptesNamePseudo(Request $request, string $name, string $pseudo)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        $sort = $request->query('sort') ?? 'id';
        $order = $request->query('order') ?? 'desc';
        $search = $request->query('search') ?? '';

        /* Récupération des comptes */
        $comptes = PrivateController::getComptes($name, '', $pseudo, $sort, $order);
        if ($search != '') { $comptes = PrivateController::getComptesSearch($comptes, $search, $sort, $order); }

        return view('private.compte', compact('comptes'));
    }

    /**
     * Affiche les comptes d'un même email et d'un même pseudo
     * @param Request $request
     * @param string $email Identifiant du compte
     * @param string $pseudo Pseudo du compte
     * @return \Illuminate\View\View private.compte
     */
    public function comptesEmailPseudo(Request $request, string $email, string $pseudo)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        $sort = $request->query('sort') ?? 'id';
        $order = $request->query('order') ?? 'desc';
        $search = $request->query('search') ?? '';

        /* Récupération des comptes */
        $comptes = PrivateController::getComptes('', $email, $pseudo, $sort, $order);
        if ($search != '') { $comptes = PrivateController::getComptesSearch($comptes, $search, $sort, $order); }

        return view('private.compte', compact('comptes'));
    }

    /**
     * Affiche les détails d'un compte d'un même nom, d'un même email et d'un même pseudo
     * @param Request $request
     * @param string $name Nom du compte
     * @param string $email Identifiant du compte
     * @param string $pseudo Pseudo du compte
     * @return \Illuminate\View\View private.compte
     */
    public function comptesNameEmailPseudo(Request $request, string $name, string $email, string $pseudo)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        $sort = $request->query('sort') ?? 'id';
        $order = $request->query('order') ?? 'desc';
        $search = $request->query('search') ?? '';

        /* Récupération des comptes */
        $comptes = PrivateController::getComptes($name, $email, $pseudo, $sort, $order);
        if ($search != '') { $comptes = PrivateController::getComptesSearch($comptes, $search, $sort, $order); }

        return view('private.compte', compact('comptes'));
    }



    /*---------------------*/
    /* Édition des comptes */
    /*---------------------*/
    /**
     * Ajoute un compte
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse Retourne la page précédente
     */
    public function addCompte(Request $request)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        /* Validation des données */
        $request->validate([
            'name' => 'required|string|min:1|max:255',
            'email' => 'required|string|min:1|max:255',
            'password' => 'required|string|min:1|max:255',
            'pseudo' => 'nullable|string|min:1|max:255',
            'key' => 'required|string|min:1|max:255',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'name.string' => 'Le nom doit être une chaine de caractère.',
            'name.min' => 'Le nom doit contenir au moins 1 caractère.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'email.required' => 'L\'email est obligatoire.',
            'email.string' => 'L\'email doit être une chaîne de caractères.',
            'email.min' => 'L\'email doit contenir au moins 1 caractère.',
            'email.max' => 'L\'email ne doit pas dépasser 255 caractères.',
            'password.required' => 'Le clé de sécurité est obligatoire.',
            'password.string' => 'La clé de sécurité doit être une chaîne de caractères.',
            'password.min' => 'La clé de sécurité doit contenir au moins 1 caractère.',
            'password.max' => 'La clé de sécurité ne doit pas dépasser 255 caractères.',
            'pseudo.string' => 'Le pseudo doit être une chaîne de caractères.',
            'pseudo.min' => 'Le pseudo doit contenir au moins 1 caractère.',
            'pseudo.max' => 'Le pseudo ne doit pas dépasser 255 caractères.',
            'key.required' => 'La clé de sécurité est obligatoire.',
            'key.string' => 'La clé de sécurité doit être une chaîne de caractères.',
            'key.min' => 'La clé de sécurité doit contenir au moins 1 caractère.',
            'key.max' => 'La clé de sécurité ne doit pas dépasser 255 caractères.',
        ]);

        /* Vérification de la clé de sécurité */
        $key = Key::where('user_id', Auth::user()->id)->first();
        if (!$key || !Hash::check($request->key, $key->key)) {
            LogController::addLog('Tentative d\'ajout d\'un compte avec une clé de sécurité incorrecte', null, 1);
            return back()->with('error', 'La clé de sécurité est incorrecte ❌.');
        }

        /* Message de confirmation */
        if (Account::where('name', $request->name)->where('email', $request->email)->first()) {
            $message = 'Attention, un compte similaire éxiste déjà. 🤔';
        } else {
            $message = '';
        }

        if (!Account::where('email', $request->email)->first()) {
            $message = $message . 'C\'est la première fois que vous utilisez cet email, vérifiez bien qu\'il est correct. 😉';
        }

        /* Ajout de l'compte */
        $compte = new Account();
        $compte->user_id = Auth::user()->id;
        $compte->name = ucfirst($request->name);
        $compte->email = $request->email;
        $compte->pseudo = $request->pseudo ?? '-';

        /* Chiffrement du mot de passe */
        $compte->password = $this->encryptPassword($request->password, $key->key);

        /* Sauvegarde du compte */
        if ($compte->save()) {
            LogController::addLog('Ajout d\'un compte');
            return back()->with('success', 'Le compte a été ajouté avec succès 👍.')->with('message', $message);
        } else {
            LogController::addLog('Erreur lors de l\'ajout d\'un compte', null, 1);
            return back()->with('error', 'Une erreur est survenue lors de l\'ajout du compte ❌.');
        }
    }

    /**
     * Modifie un compte
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse Retourne la page précédente
     */
    public function editCompte(Request $request)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        /* Validation des données */
        $request->validate([
            'id' => 'required|numeric|min:1|exists:account_manager.accounts,id',
            'name' => 'required|string|min:1|max:255',
            'email' => 'required|string|min:1|max:255',
            'password' => 'nullable|string|min:1|max:255',
            'pseudo' => 'nullable|string|min:1|max:255',
            'key' => 'required|string|min:1|max:255',
        ], [
            'id.required' => 'L\'id est obligatoire.',
            'id.numeric' => 'L\'id doit être un nombre.',
            'id.min' => 'L\'id doit être supérieur à 0.',
            'id.exists' => 'L\'id n\'existe pas.',
            'name.required' => 'Le name est obligatoire.',
            'name.string' => 'Le name doit être une name.',
            'name.min' => 'Le name doit contenir au moins 1 caractère.',
            'name.max' => 'Le name ne doit pas dépasser 255 caractères.',
            'email.required' => 'L\'email est obligatoire.',
            'email.string' => 'L\'email doit être une chaîne de caractères.',
            'email.min' => 'L\'email doit contenir au moins 1 caractère.',
            'email.max' => 'L\'email ne doit pas dépasser 255 caractères.',
            'password.string' => 'La clé de sécurité doit être une chaîne de caractères.',
            'password.min' => 'La clé de sécurité doit contenir au moins 1 caractère.',
            'password.max' => 'La clé de sécurité ne doit pas dépasser 255 caractères.',
            'pseudo.string' => 'Le pseudo doit être une chaîne de caractères.',
            'pseudo.min' => 'Le pseudo doit contenir au moins 1 caractère.',
            'pseudo.max' => 'Le pseudo ne doit pas dépasser 255 caractères.',
            'key.required' => 'La clé de sécurité est obligatoire.',
            'key.string' => 'La clé de sécurité doit être une chaîne de caractères.',
            'key.min' => 'La clé de sécurité doit contenir au moins 1 caractère.',
            'key.max' => 'La clé de sécurité ne doit pas dépasser 255 caractères.',
        ]);

        /* Vérification de la clé de sécurité */
        $key = Key::where('user_id', Auth::user()->id)->first();
        if (!$key || !Hash::check($request->key, $key->key)) {
            LogController::addLog('Tentative d\'ajout d\'un compte avec une clé de sécurité incorrecte', null, 1);
            return back()->with('error', 'La clé de sécurité est incorrecte ❌.');
        }

        /* Modification de l'compte */
        $compte = Account::find($request->id);
        $compte->name = ucfirst($request->name);
        $compte->email = $request->email;
        $compte->pseudo = $request->pseudo ?? '-';

        /* Chiffrement du mot de passe */
        if ($request->password != null) {
            $compte->password = $this->encryptPassword($request->password, $key->key);
        }

        /* Sauvegarde du compte */
        if ($compte->save()) {
            LogController::addLog('Modification du compte id: ' . $compte->id);
            return back()->with('success', 'Le compte a été modifié avec succès 👍.');
        } else {
            LogController::addLog('Erreur lors de la modification du compte id: ' . $compte->id, null, 1);
            return back()->with('error', 'Une erreur est survenue lors de la modification du compte ❌.');
        }
    }

    /**
     * Supprime un compte
     * @param string $id Id du compte
     * @return \Illuminate\Http\RedirectResponse Retourne la page précédente
     */
    public function removeCompte(string $id)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        /* Validation des données */
        if ($id == null) { back()->with('error', 'l\'id est null ❌.'); }
        if (!is_numeric($id)) { back()->with('error', 'l\'id n\'est pas un nombre ❌.'); }
        if ($id <= 0) { back()->with('error', 'l\'id est inférieur ou égal à 0 ❌.'); }

        $compte = Account::find($id);
        if (!$compte) { back()->with('error', 'Le compte n\'existe pas ❌.'); }
        if ($compte->user_id != Auth::user()->id) { back()->with('error', 'Ce compte ne vous appartient pas ❌.'); }

        /* Suppression de l'compte */
        if ($compte->delete()) {
            LogController::addLog('Suppression du compte id: ' . $compte->id);
            return back()->with('success', 'Le compte a été supprimé avec succès 👍.');
        } else {
            LogController::addLog('Erreur lors de la suppression du compte id: ' . $compte->id, null, 1);
            return back()->with('error', 'Une erreur est survenue lors de la suppression du compte ❌.');
        }
    }



    /*-----------------------------*/
    /* Téléchargements de fichiers */
    /*-----------------------------*/
    /**
     * Télécharge le fichier des comptes
     * @param Request $request
     * @return \Illuminate\Http\Response Retourne le fichier
     */
    public function downloadComptes(Request $request)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        request()->validate([
            'download_param' => 'required|string',
            'param_separator' => 'required|string',
            'password' => 'required|string',
        ]);

        /* Vérification de la clé de sécurité */
        $key = Key::where('user_id', Auth::user()->id)->first();
        if (!$key || !Hash::check($request->password, $key->key)) {
            LogController::addLog('Tentative de téléchargement du fichier des comptes avec une clé de sécurité incorrecte', null, 1);
            return back()->with('error', 'La clé de sécurité est incorrecte ❌.');
        }

        /* Récupération des informations */
        $param = explode($request->param_separator, $request->download_param);
        $name   = $param[0] != 'null' ? $param[0] : '';
        $email  = $param[1] != 'null' ? $param[1] : '';
        $pseudo = $param[2] != 'null' ? $param[2] : '';
        $search = $param[3] != 'null' ? $param[3] : '';
        $sort   = $param[4] != 'null' ? $param[4] : 'created_at';
        $order  = $param[5] != 'null' ? $param[5] : 'desc';

        /* Récupération des comptes */
        $comptes = PrivateController::getComptes($name, $email, $pseudo, $sort, $order);
        if ($search != '') { $comptes = PrivateController::getComptesSearch($comptes, $search, $sort, $order); }

        /* Création du fichier */
        $content  = '| Nom du compte | Identifiant / Email | Mot de passe | Pseudo |' . "\n";
        $content .= '|:-------------:|:-------------------:|:------------:|:------:|' . "\n";
        foreach ($comptes as $compte) {
            $content = $content . '| ' . $compte->name . ' | ' . $compte->email . ' | ' . $this->decryptPassword($compte->id, $key->key) . ' | ' . $compte->pseudo . ' |' . "\n";
        }

        LogController::addLog('Téléchargement du fichier des comptes');
        /* Téléchargement du fichier */
        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename=mes_comptes.md');
    }

    /**
     * Charger le fichier des comptes
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse Retourne la page précédente
     */
    public function uploadComptes(Request $request)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        /* Validation des données */
        $request->validate([
            'file' => 'required|file|mimes:md,txt|max:20480',
            'password_file_key' => 'required|string|min:1|max:255',
        ], [
            'file.required' => 'Le fichier est obligatoire.',
            'file.file' => 'Le fichier doit être un fichier.',
            'file.mimes' => 'Le fichier doit être un fichier de type md ou txt.',
            'file.max' => 'Le fichier ne doit pas dépasser 20 Mo.',
            'password_file_key.required' => 'La clé de sécurité est obligatoire pour ajouter des comptes.',
            'password_file_key.string' => 'La clé de sécurité doit être une chaîne de caractères.',
            'password_file_key.min' => 'La clé de sécurité doit contenir au moins 1 caractère.',
            'password_file_key.max' => 'La clé de sécurité ne doit pas dépasser 255 caractères.',
        ]);

        /* Vérification de la clé de sécurité */
        $key = Key::where('user_id', Auth::user()->id)->first();
        if (!$key || !Hash::check($request->password_file_key, $key->key)) {
            LogController::addLog('Tentative d\'ajout de comptes depuis un fichier avec une clé de sécurité incorrecte', null, 1);
            return back()->with('error', 'La clé de sécurité est incorrecte ❌.');
        }

        /* Récupération du contenu du fichier */
        $content = file_get_contents($request->file('file')->getRealPath());

        /* Ajout des nouveaux comptes */
        $txtComptes = explode("\n", $content);
        $loop = 0;
        $count = 1;
        foreach ($txtComptes as $txtCompte) {
            /* Ignore les 2 premières lignes */
            if ($loop < 2) {
                $loop++;
                continue;
            }

            $arrayCompte = explode(' | ', $txtCompte);

            if (count($arrayCompte) == 4) {
                $compte = new Account([
                    'user_id' => Auth::user()->id,
                    'name' => ucfirst(str_replace('| ', '', $arrayCompte[0], $count)),
                    'email' => strtolower($arrayCompte[1]),
                    'password' => $this->encryptPassword($arrayCompte[2], $key->key),
                    'pseudo' => str_replace(' |', '', $arrayCompte[3]),
                ]);


                if (!$compte->save())
                {
                    LogController::addLog('Erreur lors de l\'ajout du compte n°' . $count . ' depuis un fichier text', null, 1);
                    return back()->with('error', 'Une erreur est survenue lors de l\'ajout des comptes ❌.');
                }
            }
        }

        LogController::addLog('Ajout des comptes depuis un fichier text');
        return back()->with('success', 'Les comptes ont été ajoutés avec succès 👍.');
    }




    /*======================*/
    /* Fonction Utilitaires */
    /*======================*/
    /*---------*/
    /* Comptes */
    /*---------*/
    /**
     * Récupère les comptes
     * @param string $name
     * @param string $email
     * @param string $pseudo
     * @param string $sort
     * @param string $order
     * @return \Illuminate\Database\Eloquent\Collection $comptes
     */
    public function getComptes(string $name, string $email, string $pseudo, ?string $sort = 'id', ?string $order = 'desc')
    {
        $comptes = Account::where('user_id', Auth::user()->id)->orderBy($sort, $order)->get();

        if ($name != '') {
            /* Recherche des comptes qui contiennent le nom */
            $comptes = $comptes->filter(function ($compte) use ($name) {
                return str_contains($compte->name, $name);
            });
        }

        if ($email != '') {
            $comptes = $comptes->where('email', $email);
        }

        if ($pseudo != '') {
            $comptes = $comptes->where('pseudo', $pseudo);
        }

        /* décriptage des mots de passe */
        foreach ($comptes as $compte) {
            $compte->password = null;
        }

        return $comptes;
    }

    /**
     * Récupère les comptes qui correspondent à la recherche
     * @param string $search
     * @param string $sort
     * @param string $order
     * @param \Illuminate\Database\Eloquent\Collection $comptes
     */
    public function getComptesSearch($comptes, string $search, string $sort = 'id', $order = 'desc')
    {
        $decrypt = $comptes == null || $comptes->isEmpty();
        $comptes = $comptes ?? Account::where('user_id', Auth::user()->id)->orderBy($sort, $order)->get();

        /* Recherche des comptes qui contiennent le nom */
        $comptes = $comptes->filter(function ($compte) use ($search) {
            return str_contains(strtolower($compte->name), strtolower($search)) || str_contains(strtolower($compte->pseudo), strtolower($search));
        });

        /* décriptage des mots de passe */
        if ($decrypt)
        {
            foreach ($comptes as $compte) {
                $compte->password = null;
            }
        }

        return $comptes;
    }

    /**
     * Encrypte le texte qui lui est passé en paramètre
     * @param string $texte Texte à chiffrer
     * @param string $encryption_key Clé de chiffrement
     * @return string Texte chiffré
     */
    public function encryptPassword(string $texte, string $encryption_key)
    {
        return openssl_encrypt($texte, env('KEY_CIPHERING'), $encryption_key, env('KEY_OPTIONS'), env('KEY_ENCRYPTION_IV'));
    }

    /**
     * Décrypte le mot de passe correspondant au compte
     * @param int $id Id du compte
     * @param string $encryption_key Clé de chiffrement
     * @return string|null Mot de passe déchiffré ou null si le compte n'existe pas
     */
    public function decryptPassword(int $id, string $encryption_key)
    {
        $compte = Account::find($id);
        if ($compte) {
            return openssl_decrypt($compte->password, env('KEY_CIPHERING'), $encryption_key, env('KEY_OPTIONS'), env('KEY_ENCRYPTION_IV'));
        }

        return null;
    }
}