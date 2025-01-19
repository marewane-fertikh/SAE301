<?php
// Inclusion du fichier GameModel
require_once 'GameModel.php';
    $gamemodel = new GameModel();
    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Utilisation de htmlspecialchars pour éviter une attaque par injection XSS
        $nom = htmlspecialchars($_POST['nom']);
        $prenom = htmlspecialchars($_POST['prenom']);
        $numero_etu = htmlspecialchars($_POST['id']);
        $mot_de_passe = htmlspecialchars($_POST['mdp']);
        $mot_de_passe_final = htmlspecialchars($_POST['mdpfinal']);

        // Hachage du mot de passe pour la sécurité
        $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $regex="/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_])[A-Za-z0-9\W_]{8,}$/";
        $gamemodel->CreerUtilisateur($nom,$prenom,$numero_etu,$mot_de_passe,$mot_de_passe_final,$mot_de_passe_hache,$regex);
    }


    
?>