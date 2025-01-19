{{--
 * Ce fichier fait partie du projet Lys secure
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
--}}

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partage de compte</title>
</head>

<!-- Corps de l'email -->
<body style="font-family: 'Poppins', Arial, sans-serif">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center" style="padding: 20px;">
                <table class="content" width="600" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse; border: 1px solid #3232FF;">
                    <!-- Entête de l'email -->
                    <tr>
                        <td class="header" style="background-color: #3232FF; padding: 40px; text-align: center; color: white; font-size: 24px;">
                            Partage de compte
                        </td>
                    </tr>

                    <!-- Contenu de l'email -->
                    <tr>
                        <td class="body" style="padding: 40px; text-align: left; font-size: 16px; line-height: 1.6;">
                            {{ ucfirst(auth()->user()->name) }} vous a partagé son compte "<b>{{ $data['name'] }}</b>" depuis Lys secure.

                            <br><br>

                            <h1 style="font-size: 18px; font-weight: bold;">Informations du compte :</h1>
                            <ul>
                                <li><b>Nom :</b> {{ $data['name'] }}</li>
                                <li><b>Identifiant :</b> {{ $data['email'] }}</li>
                                <li><b>Mot de passe :</b> {{ $data['password'] }}</li>
                                @if ($data['pseudo'] != '-')
                                <li><b>Pseudo :</b> {{ $data['pseudo'] }}</li>
                                @endif
                            </ul>
                        </td>
                    </tr>

                    <!-- Footer de l'email -->
                    <tr>
                        <td class="footer" style="background-color: #333333; padding: 40px; text-align: center; color: white; font-size: 14px;">
                            <span>Copyright © 2024 - <script>document.write(new Date().getFullYear())</script>
                            <a href="https://florobart.github.io/" target="_blank"><b>Floris Robart</b></a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>