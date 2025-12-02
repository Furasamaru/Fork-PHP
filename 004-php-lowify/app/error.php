<?php

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';

//message d'erreur afficher
$message = $_GET['message'] ?? "Une erreur inconnue est survenue.";
//sécuriser  l'affichage
$safeMessage = htmlspecialchars($message);

//HTML
$htmlContent = <<<HTML
<div class="container bg-dark text-white p-5 rounded-4 shadow mt-5">
    <a href="index.php" class="text-white-50 mb-3 d-inline-block">
        &larr; Retour à l'accueil
    </a>

    <div class="text-center py-5">
        <h1 class="display-4 fw-bold text-danger">Erreur</h1>

        <p class="lead mt-4">
            $safeMessage
        </p>

        <div class="mt-5">
            <a href="artists.php" class="btn btn-outline-light btn-lg px-4 py-2 rounded-pill">
                &larr; Retour à la liste des artistes
            </a>
        </div>
    </div>
</div>
HTML;


echo (new HTMLPage(title: "Erreur - Lowify"))
    ->setupBootstrap([
        "class" => "bg-dark text-white min-vh-100 d-flex justify-content-center align-items-center",
        "data-bs-theme" => "dark"
    ])
    ->addContent($htmlContent)
    ->render();
