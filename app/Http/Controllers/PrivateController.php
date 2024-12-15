<?php
namespace App\Http\Controllers;

/*
 * Ce fichier fait partie du projet Account Manager
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
 */

use App\Models\Account;
use App\Models\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class PrivateController extends Controller
{
    /*=========*/
    /* Accueil */
    /*=========*/
    /**
     * Affiche l'accueil
     * @return \Illuminate\View\View private.accueil
     */
    public function accueil()
    {
        $key = Key::where('user_id', auth()->user()->id)->first();
        if ($key != null) {
            session(['key_exist' => filter_var(true, FILTER_VALIDATE_BOOLEAN)]);
        }

        return view('private.accueil');
    }



    /*------------------*/
    /* Gestion des clés */
    /*------------------*/
    /**
     * Sauvegarde la clé de cryptage
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
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit contenir au moins ' . env('KEY_MIN_LENGTH', 12) . ' caractère.',
            'password.max' => 'Le mot de passe ne doit pas dépasser 255 caractères.',
            'password.regex' => 'Le mot de passe doit contenir au moins une lettre minuscule, une lettre majuscule et un chiffre.',
            'password_confirmation.required' => 'La confirmation du mot de passe est obligatoire.',
            'password_confirmation.string' => 'La confirmation du mot de passe doit être une chaîne de caractères.',
            'password_confirmation.min' => 'La confirmation du mot de passe doit contenir au moins ' . env('KEY_MIN_LENGTH', 12) . ' caractère.',
            'password_confirmation.same' => 'Les mots de passe ne correspondent pas.',
        ]);

        /* Sauvegarde de la clé de cryptage */
        $key = new Key();
        $key->user_id = auth()->user()->id;
        $key->key = Hash::make($request->password);

        if ($key->save()) {
            LogController::addLog('Sauvegarde de la clé de cryptage');
            return back()->with('success', 'La clé de cryptage a été sauvegardée avec succès 👍.');
        } else {
            LogController::addLog('Erreur lors de la sauvegarde de la clé de cryptage', null, 1);
            return back()->with('error', 'Une erreur est survenue lors de la sauvegarde de la clé de cryptage.');
        }
    }

    /**
     * Vérifie la clé de cryptage
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse comptes
     */
    public function checkKey(Request $request)
    {
        /* Validation des données */
        $request->validate([
            'password' => 'required|string',
        ], [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
        ]);

        /* Vérification de la clé de cryptage */
        $key = Key::where('user_id', auth()->user()->id)->first();
        if ($key && Hash::check($request->password, $key->key)) {

            LogController::addLog('Vérification d\'une clé de cryptage correcte');
            return redirect()->route('comptes');
        }

        LogController::addLog('Vérification d\'une clé de cryptage incorrecte', null, 1);
        return back()->with('error', 'Le mot de passe est incorect ❌.');
    }



    /*----------------------------------*/
    /* Changement de la clé de cryptage */
    /*----------------------------------*/
    /**
     * Affiche la page de changement de la clé de cryptage
     * @return \Illuminate\View\View private.change_key
     */
    public function changeKey()
    {
        return view('private.change_key');
    }

    /**
     * Sauvegarde la nouvelle clé de cryptage et encrypte les mots de passe avec la nouvelle clé
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
            'current_password.required' => 'L\'ancien mot de passe est obligatoire.',
            'current_password.string' => 'L\'ancien mot de passe doit être une chaîne de caractères.',
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.string' => 'Le nouveau mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le nouveau mot de passe doit contenir au moins ' . env('KEY_MIN_LENGTH', 12) . ' caractère.',
            'password.max' => 'Le nouveau mot de passe ne doit pas dépasser 255 caractères.',
            'password.regex' => 'Le nouveau mot de passe doit contenir au moins une lettre minuscule, une lettre majuscule et un chiffre.',
            'password_confirmation.required' => 'La confirmation du nouveau mot de passe est obligatoire.',
            'password_confirmation.string' => 'La confirmation du nouveau mot de passe doit être une chaîne de caractères.',
            'password_confirmation.same' => 'Les mots de passe ne correspondent pas.',
        ]);

        /* Mise en place des variables */
        $old_key = $request->current_password;
        $new_key = $request->password;

        /* Vérification de l'ancienne clé de cryptage */
        $key = Key::where('user_id', auth()->user()->id)->first();
        if (!$key || !Hash::check($old_key, $key->key)) {
            LogController::addLog('Tentative de modification de la clé de cryptage avec une ancienne clé incorrecte', null, 1);
            return back()->with('error', 'Votre mot de passe actuel est incorrect.');
        }

        /* Sauvegarde de la nouvelle clé de cryptage */
        $key->key = Hash::make($new_key);

        /* Modification de la clé de cryptage */
        if ($key->save()) {
            /* Récupération des comptes */
            $comptes = PrivateController::getComptes('', '', '');

            /* Chiffrement des mots de passe */
            foreach ($comptes as $compte) {
                $compte->password = $this->encryptPassword($this->decryptPassword($compte->id), $new_key);
                if (!$compte->save()) {
                    LogController::addLog('Erreur lors de la modification de la clé de cryptage', null, 1);
                    return back()->with('error', 'Une erreur est survenue lors de la modification de la clé de cryptage.');
                }
            }

            LogController::addLog('Modification de la clé de cryptage');
            return redirect()->route('comptes')->with('success', 'La clé de cryptage a été modifiée avec succès 👍.');
        } else {
            LogController::addLog('Erreur lors de la modification de la clé de cryptage', null, 1);
            return back()->with('error', 'Une erreur est survenue lors de la modification de la clé de cryptage.');
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

        $sort = $request->query('sort') ?? 'created_at';
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

        $sort = $request->query('sort') ?? 'created_at';
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

        $sort = $request->query('sort') ?? 'created_at';
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

        $sort = $request->query('sort') ?? 'created_at';
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

        $sort = $request->query('sort') ?? 'created_at';
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

        $sort = $request->query('sort') ?? 'created_at';
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

        $sort = $request->query('sort') ?? 'created_at';
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

        $sort = $request->query('sort') ?? 'created_at';
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
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'name.string' => 'Le nom doit être une chaine de caractère.',
            'name.min' => 'Le nom doit contenir au moins 1 caractère.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'email.required' => 'L\'email est obligatoire.',
            'email.string' => 'L\'email doit être une chaîne de caractères.',
            'email.min' => 'L\'email doit contenir au moins 1 caractère.',
            'email.max' => 'L\'email ne doit pas dépasser 255 caractères.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit contenir au moins 1 caractère.',
            'password.max' => 'Le mot de passe ne doit pas dépasser 255 caractères.',
            'pseudo.string' => 'Le pseudo doit être une chaîne de caractères.',
            'pseudo.min' => 'Le pseudo doit contenir au moins 1 caractère.',
            'pseudo.max' => 'Le pseudo ne doit pas dépasser 255 caractères.',
        ]);

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
        $compte->user_id = auth()->user()->id;
        $compte->name = ucfirst($request->name);
        $compte->email = $request->email;
        $compte->pseudo = $request->pseudo ?? '-';

        /* Chiffrement du mot de passe */
        $compte->password = $this->encryptPassword($request->password);

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
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit contenir au moins 1 caractère.',
            'password.max' => 'Le mot de passe ne doit pas dépasser 255 caractères.',
            'pseudo.string' => 'Le pseudo doit être une chaîne de caractères.',
            'pseudo.min' => 'Le pseudo doit contenir au moins 1 caractère.',
            'pseudo.max' => 'Le pseudo ne doit pas dépasser 255 caractères.',
        ]);

        /* Modification de l'compte */
        $compte = Account::find($request->id);
        $compte->name = ucfirst($request->name);
        $compte->email = $request->email;
        $compte->pseudo = $request->pseudo ?? '-';

        /* Chiffrement du mot de passe */
        if ($request->password != null) {
            $compte->password = $this->encryptPassword($request->password);
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
        if ($compte->user_id != auth()->user()->id) { back()->with('error', 'Ce compte ne vous appartient pas ❌.'); }

        /* Suppression de l'compte */
        if ($compte->delete()) {
            LogController::addLog('Suppression du compte id: ' . $compte->id);
            return back()->with('success', 'Le compte a été supprimé avec succès 👍.');
        } else {
            LogController::addLog('Erreur lors de la suppression du compte id: ' . $compte->id, null, 1);
            return back()->with('error', 'Une erreur est survenue lors de la suppression du compte ❌.');
        }
    }

    /**
     * Permet d'afficher le mot de passe d'un compte
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse Retourne le mot de passe
     */
    public function getPassword(Request $request)
    {
        $id = $request->id;
        $password = $this->decryptPassword($id);

        return response($password, 200)->header('Content-Type', 'text/plain');
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

        /* Récupération des paramètres de l'url */
        $name = $request->query('name') ?? '';
        $email = $request->query('email') ?? '';
        $pseudo = $request->query('pseudo') ?? '';
        $sort = $request->query('sort') ?? 'created_at';
        $order = $request->query('order') ?? 'desc';
        $search = $request->query('search') ?? '';


        /* Récupération des comptes */
        $comptes = PrivateController::getComptes($name, $email, $pseudo, $sort, $order);
        if ($search != '') { $comptes = PrivateController::getComptesSearch($comptes, $search, $sort, $order); }

        /* Création du fichier */
        $content = '| Nom du compte | Identifiant / Email | Mot de passe | Pseudo |' . "\n";
        $content = $content . '|:-------------:|:------------------:|:------------------:|:------------------:|' . "\n";
        foreach ($comptes as $compte) {
            $content = $content . '| ' . $compte->name . ' | ' . $compte->email . ' | ' . $this->decryptPassword($compte->id) . ' | ' . $compte->pseudo . ' |' . "\n";
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
        ], [
            'file.required' => 'Le fichier est obligatoire.',
            'file.file' => 'Le fichier doit être un fichier.',
            'file.mimes' => 'Le fichier doit être un fichier de type md ou txt.',
            'file.max' => 'Le fichier ne doit pas dépasser 20 Mo.',
        ]);

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
                    'user_id' => auth()->user()->id,
                    'name' => ucfirst(str_replace('| ', '', $arrayCompte[0], $count)),
                    'email' => strtolower($arrayCompte[1]),
                    'password' => $this->encryptPassword($arrayCompte[2]),
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
    public function getComptes(string $name, string $email, string $pseudo, ?string $sort = 'created_at', ?string $order = 'desc')
    {
        $comptes = Account::where('user_id', auth()->user()->id)->orderBy($sort, $order)->get();

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
    public function getComptesSearch($comptes, string $search, string $sort = 'created_at', $order = 'desc')
    {
        $decrypt = $comptes == null || $comptes->isEmpty();
        $comptes = $comptes ?? Account::where('user_id', auth()->user()->id)->orderBy($sort, $order)->get();

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
     * @return string Texte chiffré
     */
    public function encryptPassword(string $texte, ?string $encryption_key = null)
    {
        $encryption_key = $encryption_key == null ? session()->get('key') : $encryption_key;
        return openssl_encrypt($texte, env('KEY_CIPHERING'), $encryption_key, env('KEY_OPTIONS'), env('KEY_ENCRYPTION_IV'));
    }

    /**
     * Décrypte le mot de passe correspondant au compte
     * @param int $id Id du compte
     * @return string|null Mot de passe déchiffré ou null si le compte n'existe pas
     */
    public function decryptPassword(int $id)
    {
        $compte = Account::find($id);
        if ($compte) {
            $encryption_key = session()->get('key');
            return openssl_decrypt($compte->password, env('KEY_CIPHERING'), $encryption_key, env('KEY_OPTIONS'), env('KEY_ENCRYPTION_IV'));
        }

        return null;
    }
}