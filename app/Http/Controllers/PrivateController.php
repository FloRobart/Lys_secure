<?php
namespace App\Http\Controllers;

/*
 * Ce fichier fait partie du projet Account Manager
 * Copyright (C) 2024 Floris Robart <florobart.github.com>
 */

use App\Models\Account;
use App\Models\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PrivateController extends Controller
{
    private const ciphering = "AES-128-CTR"; /* Utilisation de l'algorithme de chiffrement AES-128-CTR */
    private const options = 0; /* Utilisation de l'option 0 */
    private const encryption_iv = '1234567891011121'; /* Vecteur d'initialisation */




    /*=========*/
    /* Accueil */
    /*=========*/
    /**
     * Affiche l'accueil
     */
    public function accueil()
    {
        $key = Key::where('user_id', auth()->user()->id)->first();
        if ($key) {
            session(['key_exist' => filter_var(true, FILTER_VALIDATE_BOOLEAN)]);
        }

        return view('private.accueil');
    }



    /*------------------*/
    /* Gestion des clés */
    /*------------------*/
    /**
     * Sauvegarde la clé de cryptage
     */
    public function saveKey(Request $request)
    {
        /* Validation des données */
        $request->validate([
            'password' => 'required|string|min:1|max:255',
        ], [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit contenir au moins 1 caractère.',
            'password.max' => 'Le mot de passe ne doit pas dépasser 255 caractères.',
        ]);

        /* Sauvegarde de la clé de cryptage */
        $key = new Key();
        $key->user_id = auth()->user()->id;
        $key->key = Hash::make($request->password);

        /* Enregistrement de la clé de cryptage dans la session */
        session(['key' => $request->password]);

        if ($key->save()) {
            return back()->with('success', 'La clé de cryptage a été sauvegardée avec succès 👍.');
        } else {
            return back()->with('error', 'Une erreur est survenue lors de la sauvegarde de la clé de cryptage ❌.');
        }
    }

    /**
     * Vérifie la clé de cryptage
     */
    public function checkKey(Request $request)
    {
        /* Validation des données */
        $request->validate([
            'password' => 'required|string|min:1|max:255',
        ], [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit contenir au moins 1 caractère.',
            'password.max' => 'Le mot de passe ne doit pas dépasser 255 caractères.',
        ]);

        /* Vérification de la clé de cryptage */
        $key = Key::where('user_id', auth()->user()->id)->first();
        if ($key && Hash::check($request->password, $key->key)) {
            session(['key' => $request->password]);
            return redirect()->route('comptes');
        }

        return back()->with('error', 'Le mot de passe est incorect ❌.');
    }




    /*========*/
    /* Compte */
    /*========*/
    /*-----------------------*/
    /* Affichage des comptes */
    /*-----------------------*/
    /**
     * Affiche tous les comptes
     */
    public function comptes(Request $request)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        $sort = $request->query('sort') ?? 'created_at';
        $order = $request->query('order') ?? 'desc';

        /* Récupération des comptes */
        $comptes = PrivateController::getComptes('', '', '', $sort, $order);

        return view('private.compte', compact('comptes'));
    }

    /**
     * Affiche tous les comptes d'un même nom
     */
    public function comptesName(Request $request, string $name)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        $sort = $request->query('sort') ?? 'created_at';
        $order = $request->query('order') ?? 'desc';

        $comptes = PrivateController::getComptes($name, '', '', $sort, $order);

        return view('private.compte', compact('comptes'));
    }

    /**
     * Affiche tous les comptes d'un même email
     */
    public function comptesEmail(Request $request, string $email)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        $sort = $request->query('sort') ?? 'created_at';
        $order = $request->query('order') ?? 'desc';

        $comptes = PrivateController::getComptes('', $email, '', $sort, $order);

        return view('private.compte', compact('comptes'));
    }

    /**
     * Affiche tous les comptes d'un même pseudo
     */
    public function comptesPseudo(Request $request, string $pseudo)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        $sort = $request->query('sort') ?? 'created_at';
        $order = $request->query('order') ?? 'desc';

        $comptes = PrivateController::getComptes('', '', $pseudo, $sort, $order);

        return view('private.compte', compact('comptes'));
    }

    /**
     * Affiche les comptes d'un même nom et d'un même email
     */
    public function comptesNameEmail(Request $request, string $name, string $email)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        $sort = $request->query('sort') ?? 'created_at';
        $order = $request->query('order') ?? 'desc';

        $comptes = PrivateController::getComptes($name, $email, '', $sort, $order);

        return view('private.compte', compact('comptes'));
    }

    /**
     * Affiche les comptes d'un même name et d'un même pseudo
     */
    public function comptesNamePseudo(Request $request, string $name, string $pseudo)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        $sort = $request->query('sort') ?? 'created_at';
        $order = $request->query('order') ?? 'desc';

        $comptes = PrivateController::getComptes($name, '', $pseudo, $sort, $order);

        return view('private.compte', compact('comptes'));
    }

    /**
     * Affiche les comptes d'un même email et d'un même pseudo
     */
    public function comptesEmailPseudo(Request $request, string $email, string $pseudo)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        $sort = $request->query('sort') ?? 'created_at';
        $order = $request->query('order') ?? 'desc';

        $comptes = PrivateController::getComptes('', $email, $pseudo, $sort, $order);

        return view('private.compte', compact('comptes'));
    }

    /**
     * Affiche les détails d'un compte d'un même nom, d'un même email et d'un même pseudo
     */
    public function comptesNameEmailPseudo(Request $request, string $name, string $email, string $pseudo)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        $sort = $request->query('sort') ?? 'created_at';
        $order = $request->query('order') ?? 'desc';

        $comptes = PrivateController::getComptes($name, $email, $pseudo, $sort, $order);

        return view('private.compte', compact('comptes'));
    }



    /*---------------------*/
    /* Édition des comptes */
    /*---------------------*/
    /**
     * Ajoute un compte
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
        $compte->pseudo = $request->pseudo;
        
        /* Chiffrement du mot de passe */
        $encryption_key = session()->get('key'); /* Clé de chiffrement */
        $compte->password = openssl_encrypt($request->password, PrivateController::ciphering, $encryption_key, PrivateController::options, PrivateController::encryption_iv);
        
        
        /* Sauvegarde du compte */
        if ($compte->save()) {
            return back()->with('success', 'Le compte a été ajouté avec succès 👍.')->with('message', $message);
        } else {
            return back()->with('error', 'Une erreur est survenue lors de l\'ajout du compte ❌.');
        }
    }

    /**
     * Modifie un compte
     */
    public function editCompte(Request $request)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        /* Validation des données */
        $request->validate([
            'id' => 'required|numeric|min:1|exists:account_manager.accounts,id',
            'name' => 'required|string|min:1|max:255',
            'email' => 'required|string|min:1|max:255',
            'password' => 'required|string|min:1|max:255',
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
            'password.required' => 'Le mot de passe est obligatoire.',
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
        $compte->pseudo = $request->pseudo;

        /* Chiffrement du mot de passe */
        $encryption_key = session()->get('key'); /* Clé de chiffrement */
        $compte->password = openssl_encrypt($request->password, PrivateController::ciphering, $encryption_key, PrivateController::options, PrivateController::encryption_iv);

        /* Sauvegarde du compte */
        if ($compte->save()) {
            return back()->with('success', 'Le compte a été modifié avec succès 👍.');
        } else {
            return back()->with('error', 'Une erreur est survenue lors de la modification du compte ❌.');
        }
    }

    /**
     * Supprime un compte
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
            return back()->with('success', 'Le compte a été supprimé avec succès 👍.');
        } else {
            return back()->with('error', 'Une erreur est survenue lors de la suppression du compte ❌.');
        }
    }



    /*----------------------*/
    /* Recherche de comptes */
    /*----------------------*/
    /**
     * Recupère les comptes correspondant à la recherche
     */
    public function searchComptes(Request $request)
    {

    }



    /*-----------------------------*/
    /* Téléchargements de fichiers */
    /*-----------------------------*/
    /**
     * Télécharge le fichier des comptes
     */
    public function downloadComptes(Request $request)
    {
        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');

        /* Récupération des paramètres de l'url */
        $name = $request->query('name') ?? '';
        $email = $request->query('email') ?? '';
        $pseudo = $request->query('pseudo') ?? '';

        /* Récupération des comptes */
        $comptes = PrivateController::getComptes($name, $email, $pseudo);

        /* Création du fichier */
        $content = '| Nom du compte | Identifiant / Email | Mot de passe | Pseudo |' . "\n";
        $content = $content . '|:-------------:|:------------------:|:------------------:|:------------------:|' . "\n";
        foreach ($comptes as $compte) {
            $content = $content . '| ' . $compte->name . ' | ' . $compte->email . ' | ' . $compte->password . ' | ' . $compte->pseudo . ' |' . "\n";
        }

        /* Téléchargement du fichier */
        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename=mes_comptes.md');
    }

    /**
     * Charger le fichier des comptes
     */
    public function uploadComptes(Request $request)
    {
        return back()->with('error', 'Cette fonctionnalité n\'est pas encore disponible.');
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
     */
    public function getComptes(string $name, string $email, string $pseudo, string $sort = 'created_at', $order = 'desc')
    {
        $comptes = Account::all()->where('user_id', auth()->user()->id);

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
        $encryption_key = session()->get('key');
        foreach ($comptes as $compte) {
            $compte->password = openssl_decrypt($compte->password, PrivateController::ciphering, $encryption_key, PrivateController::options, PrivateController::encryption_iv);
        }

        return $order == 'asc' ? $comptes->sortBy($sort) : $comptes->sortByDesc($sort);
    }
}