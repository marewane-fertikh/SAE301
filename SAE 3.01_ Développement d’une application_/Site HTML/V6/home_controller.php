<?php

require_once "GameModel.php";

if (isset($_GET["action"])) {
    $action = $_GET["action"];

    if ($action === "showAllGames") {
        // Initialisation du modèle
        $gameModel = new GameModel($db);

        // Récupérer tous les jeux
        $games = $gameModel->getAllGames();

        // Inclure la vue correspondante
        include "../views/test.html"; // Affiche la liste des jeux dynamiquement
    }

    if ($action === "searchGame") {
        // Récupérer le paramètre de recherche
        $query = $_GET['query'] ?? '';

        // Initialisation du modèle
        $gameModel = new GameModel($db);

        // Rechercher un jeu par son nom
        $game = $gameModel->getGameByName($query);

        // Inclure la vue correspondante
        include "../views/game_details.php"; // Affiche les détails d'un jeu
    }
}