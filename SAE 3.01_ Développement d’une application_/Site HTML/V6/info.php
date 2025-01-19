<?php

// Inclusion du modèle
require_once "GameModel.php";

// Initialisation du modèle
$gameModel = new GameModel();

// Gestion des actions via GET
$action = $_GET['action'] ?? null;
$query = $_GET['query'] ?? null;

if ($action === "searchGame" && !empty($query)) {
    // Rechercher un jeu spécifique
    $game = $gameModel->getGameByName($query);

    // Si le jeu n'est pas trouvé, afficher un message
    if (!$game) {
        $errorMessage = "Jeu introuvable.";
    }
} else {
    // Par défaut, on récupère le premier jeu de la base de données
    $games = $gameModel->getAllGames();
    $game = $games[0] ?? null;

    if (!$game) {
        $errorMessage = "Aucun jeu trouvé dans la base de données.";
    }
}
//var_dump($game);
//var_dump($sql);

// Inclusion de la vue
include "infos_view.php";