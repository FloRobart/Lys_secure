/*
 * Ce fichier fait partie du projet Lys secure
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
 */

/* Affichage du mot de passe */
function showKey()
{
    /* Définition des variables */
    /* Input password */
    var passwordInput1 = document.getElementById("key");

    /* SVG eyes open */
    var svgEyeOpen1 = document.getElementById("key_svgEyeOpen1");

    /* SVG eyes close */
    var svgEyeClose1 = document.getElementById("key_svgEyeClose1");

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