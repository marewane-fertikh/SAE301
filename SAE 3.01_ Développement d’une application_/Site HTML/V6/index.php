<?php
// Inclusion du modèle
require_once 'GameModel.php';

// Initialisation du modèle
$gameModel = new GameModel();

// Vérification si une recherche a été soumise
$query = $_GET['query'] ?? null;
if ($query) {
    // Recherche des jeux contenant le terme saisi
    $games = $gameModel->getGameByName($query);
} else {
    // Si aucune recherche, récupérer tous les jeux
    $games = $gameModel->getAllGames();
}
//changement pour reserver un jeu 


if (isset($_POST['reserverGame'])) {
    // Récupérer les données du formulaire
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $adresse = trim($_POST['adresse']);
    $jeu = trim($_POST['game-select']);
    $dates = explode(' au ', trim($_POST['date-range'])); // Supposons que les dates sont séparées par "à"

    // Assurez-vous que les dates sont au format correct
    if (isset($_POST['date-range'])) {
        $dateRange = $_POST['date-range']; // "16/01/2025 au 19/01/2025"
    
        // Séparation des dates en utilisant " au "
        $dates = explode(" au ", $dateRange);
    
        if (count($dates) === 2) {
            $startDate = $dates[0]; // "16/01/2025"
            $endDate = $dates[1];   // "19/01/2025"
    
            // Conversion en format compatible avec PHP (Y-m-d)
            $startDateFormatted = DateTime::createFromFormat('d/m/Y', $startDate);
            $endDateFormatted = DateTime::createFromFormat('d/m/Y', $endDate);
    
            if ($startDateFormatted && $endDateFormatted) {
                $startDateSQL = $startDateFormatted->format('Y-m-d'); // Format SQL
                $endDateSQL = $endDateFormatted->format('Y-m-d');     // Format SQL
    
                // Debugging output (à retirer dans un environnement de production)
                echo "Date de début (SQL) : $startDateSQL<br>";
                echo "Date de fin (SQL) : $endDateSQL<br>";
    
                // Vous pouvez maintenant utiliser $startDateSQL et $endDateSQL pour vos requêtes SQL
            } else {
                //echo "Erreur lors de la conversion des dates.";
            }
        } else {
            echo "Format de plage de dates incorrect.";
        }
    } else {
        echo "Aucune date sélectionnée.";
    }

    try {
        // Récupérer l'emprunteur ou le créer
        $emprunteurId = $gameModel->getOrCreateEmprunteur($nom, $email, $adresse);
    
        // Récupérer l'ID du jeu
        $game = $gameModel->getGameByName($jeu);
        if (!$game) {
            throw new Exception("Le jeu sélectionné n'existe pas dans la base de données.");
        }
       //var_dump($game);
        $jeuId = $game[0]['id_jeu']; //faut faire un var_dump car les infos sont dans un tab associatif et sont dans $game[0]
    
        // Récupérer l'ID de la boîte associée au jeu
        $boite = $gameModel->getBoiteIdByGameId($jeuId);
        if (!$boite) {
            throw new Exception("Aucune boîte disponible pour le jeu sélectionné.");
        }
        $boiteId = $boite['boite_id'];
    
        // Créer la réservation
        $gameModel->createPret($boiteId, $emprunteurId, $startDate, $endDate);
    
        echo "Réservation effectuée avec succès !";
    } catch (Exception $e) {
        echo "Erreur lors de la réservation : " . $e->getMessage();
    }
    $gameName = trim($_POST['game-select']);
$singleGame = $gameModel->getSingleGame($gameName);
var_dump($singleGame);
if ($singleGame) {
    echo "Jeu trouvé : " . htmlspecialchars($singleGame['titre']);
    // Vous pouvez afficher ou traiter les données du jeu ici
} else {
    echo "Aucun jeu trouvé avec ce nom.";
}

    //var_dump($_POST);
}


// Inclusion de la vue
include 'Home_View.php';