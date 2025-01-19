<?php

class GameModel {
    private $connection;

    public function __construct() {
        require 'nbpn.php';
        $this->connection = $connection;
        $this->connection->query("SET NAMES 'utf8'");
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //error mode 
    }

    // Méthode pour récupérer tous les jeux
    public function getAllGames() {
        $sql = "
        SELECT 
            jeux.id_jeu,
            jeux.titre,
            jeux.date_parution_debut,
            jeux.date_parution_fin,
            jeux.information_date,
            jeux.version,
            COALESCE(jeux.nombre_de_joueurs, 'Nombre inconnu') AS nombre_de_joueurs,
            jeux.age_indique,
            jeux.mots_cles,
            COALESCE(mecanisme.nom, 'Type inconnu') AS mecanisme,
            -- Récupérer les noms des auteurs associés au jeu
            string_agg(DISTINCT COALESCE (auteur.nom, 'Auteur inconnu'),', ') AS auteurs,
            -- Récupérer les noms des éditeurs associés au jeu
            string_agg(DISTINCT COALESCE (editeur.nom, 'Editeur inconnu'),', ') AS editeurs
        FROM jeux
        -- Association avec les mécanismes
        LEFT JOIN mecanisme ON mecanisme.mecanisme_id = jeux.mecanisme_id
        -- Associations avec les auteurs via jeu_auteur
        LEFT JOIN jeuauteur ON jeuauteur.jeu_id = jeux.id_jeu
        LEFT JOIN auteur ON auteur.auteur_id = jeuauteur.auteur_id
        -- Associations avec les éditeurs via jeu_editeur
        LEFT JOIN jeuediteur ON jeuediteur.jeu_id = jeux.id_jeu
        LEFT JOIN editeur ON editeur.editeur_id = jeuediteur.editeur_id
        GROUP BY 
            jeux.id_jeu, jeux.titre, jeux.date_parution_debut, jeux.date_parution_fin,
            jeux.information_date, jeux.version, jeux.nombre_de_joueurs, jeux.age_indique, 
            jeux.mots_cles, mecanisme.nom
    ";
    //var_dump($sql);
    //retourne  toutes  les informations de la bd et le renvoie en tableau associatif facile a manipuler pour récupérer ce qu'on a besoin
    return $this->connection->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour rechercher un jeu par nom  : On aura donc comme résultat un  tableau décerner a un jeux ou il y aura toute les infos (i=>1,... )
    public function getGameByName($name) {
    $sql = "SELECT 
                jeux.id_jeu, 
                jeux.titre, 
                jeux.date_parution_debut, 
                jeux.date_parution_fin, 
                jeux.information_date, 
                jeux.version, 
                COALESCE(jeux.nombre_de_joueurs, 'Nombre de joueurs inconnu') AS nombre_de_joueurs,
                jeux.age_indique, 
                jeux.mots_cles, 
                COALESCE(mecanisme.nom, 'Type inconnu') AS mecanisme,
                string_agg(DISTINCT auteur.nom, ', ') AS auteurs,
                string_agg(DISTINCT editeur.nom, ', ') AS editeurs
            FROM jeux
            LEFT JOIN mecanisme ON mecanisme.mecanisme_id = jeux.mecanisme_id
            LEFT JOIN jeuauteur ON jeuauteur.jeu_id = jeux.id_jeu
            LEFT JOIN auteur ON auteur.auteur_id = jeuauteur.auteur_id
            LEFT JOIN jeuediteur ON jeuediteur.jeu_id = jeux.id_jeu
            LEFT JOIN editeur ON editeur.editeur_id = jeuediteur.editeur_id
            WHERE jeux.titre ILIKE :name
            GROUP BY jeux.id_jeu, jeux.titre, jeux.date_parution_debut, jeux.date_parution_fin, 
                     jeux.information_date, jeux.version, jeux.nombre_de_joueurs, 
                     jeux.age_indique, jeux.mots_cles, mecanisme.nom";

    $stmt = $this->connection->prepare($sql);
    $searchTerm = $name . '%'; // Rechercher les jeux qui commencent par $name
    $stmt->bindParam(':name', $searchTerm);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer tous les jeux correspondant de la bd  grace au fetchAll

}
// Fonction pour créer un utilisateur après validation du mot de passe avec une regex= expression  et hachage=  mdp caché avant insertion dans la base de données.
public function CreerUtilisateur($nom, $prenom,$numero_etu,$mot_de_passe,$mot_de_passe_final,$mot_de_passe_hache, $regex){
        
        $role_id =1;
        // Préparer et exécuter la requête d'insertion
        if(preg_match($regex,$mot_de_passe_final) && $mot_de_passe_final == $mot_de_passe){
        $stmt = $this->connection->prepare('INSERT INTO utilisateurs (numero_etu ,nom, prenom, mot_de_passe,mot_de_passe_nonh,role_id) VALUES (:numero_etu, :nom ,:prenom, :mot_de_passe,:mot_de_passe_nonh,:role_id)');
        $stmt->execute([
            ':numero_etu'=>$numero_etu,
            ':nom' => $nom,
            ':prenom'=> $prenom,
            ':mot_de_passe' => $mot_de_passe_hache,
            ':mot_de_passe_nonh'=> $mot_de_passe,
            ':role_id'=>$role_id       ]);

        echo "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        
    }
    else{
        echo "insérer un mot de passe d'une longueur de 8 avec une majuscule au moins un chiffre et un caractère spécial";
    }
    }
    public function getOrCreateEmprunteur($nom, $email, $adresse) {
        // Vérifier si l'emprunteur existe déjà
        $sqlCheck = "SELECT emprunteur_id FROM Emprunteur WHERE nom = :nom AND email = :email";
        $stmtCheck = $this->connection->prepare($sqlCheck);
        $stmtCheck->execute([
            ':nom' => $nom,
            ':email' => $email
        ]);
        $emprunteur = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($emprunteur) {
            // Si l'emprunteur existe, retourner son ID
            return $emprunteur['emprunteur_id'];
        }

        // Sinon, insérer un nouvel emprunteur
        $sqlInsert = "INSERT INTO Emprunteur (nom, email, adresse) VALUES (:nom, :email, :adresse)";
        $stmtInsert = $this->connection->prepare($sqlInsert);
        $stmtInsert->execute([
            ':nom' => $nom,
            ':email' => $email,
            ':adresse' => $adresse
        ]);

        // Retourner l'ID du nouvel emprunteur
        return $this->connection->lastInsertId();
    }
    public function createPret($boiteId, $emprunteurId, $startDate, $endDate) {
        $db = $this->connection;
        $sql = "INSERT INTO pret (boite_id, emprunteur_id, date_emprunt, date_retour)
                VALUES (:boiteId, :emprunteurId, :startDate, :endDate)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':boiteId', $boiteId);
        $stmt->bindParam(':emprunteurId', $emprunteurId);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();
    }
    public function getBoiteIdByGameId($jeuId) {
    $sql = "SELECT boite_id FROM boites WHERE jeu_id = :jeuId LIMIT 1";
    $stmt = $this->connection->prepare($sql);
    $stmt->bindParam(':jeuId', $jeuId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne un tableau associatif contenant boite_id
}
//La fonction  pour réservé un seul jeux  
public function getSingleGame($name) {
    $sql = "SELECT 
                jeux.id_jeu, 
                jeux.titre, 
                jeux.date_parution_debut, 
                jeux.date_parution_fin, 
                jeux.information_date, 
                jeux.version, 
                COALESCE(jeux.nombre_de_joueurs, 'Nombre de joueurs inconnu') AS nombre_de_joueurs,
                jeux.age_indique, 
                jeux.mots_cles, 
                COALESCE(mecanisme.nom, 'Type inconnu') AS mecanisme,
                string_agg(DISTINCT auteur.nom, ', ') AS auteurs,
                string_agg(DISTINCT editeur.nom, ', ') AS editeurs
            FROM jeux
            LEFT JOIN mecanisme ON mecanisme.mecanisme_id = jeux.mecanisme_id
            LEFT JOIN jeuauteur ON jeuauteur.jeu_id = jeux.id_jeu
            LEFT JOIN auteur ON auteur.auteur_id = jeuauteur.auteur_id
            LEFT JOIN jeuediteur ON jeuediteur.jeu_id = jeux.id_jeu
            LEFT JOIN editeur ON editeur.editeur_id = jeuediteur.editeur_id
            WHERE jeux.titre ILIKE :name
            GROUP BY jeux.id_jeu, jeux.titre, jeux.date_parution_debut, jeux.date_parution_fin, 
                     jeux.information_date, jeux.version, jeux.nombre_de_joueurs, 
                     jeux.age_indique, jeux.mots_cles, mecanisme.nom"; // On limite à un seul jeu

    $stmt = $this->connection->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retourne un seul jeu sous forme de tableau associatif
}





// Fonction pour authentifier un utilisateur en vérifiant l'identifiant et le mot de passe dans la base de données.
public function connecterUtilisateur($identifiant, $mot_de_passe) {
    try {
        $stmt = $this->connection->prepare('SELECT numero_etu, mot_de_passe_nonh FROM utilisateurs WHERE numero_etu = :numero_etu');
        $stmt->execute([':numero_etu' => $identifiant]);
        
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($utilisateur) {
            if ($mot_de_passe === $utilisateur['mot_de_passe_nonh']) {
                return true; // Connexion réussie
            } else {
                throw new Exception("Le mot de passe est incorrect.");
            }
        } else {
            throw new Exception("Aucun utilisateur trouvé avec cet identifiant.");
        }
    } catch (PDOException $e) {
        throw new Exception("Erreur SQL : " . $e->getMessage());
    }
}
// La fonction met à jour le profil d'un utilisateur en fonction de son ID, de son nom, de son numéro d'étudiant et optionnellement de son mot de passe.
public function updateUserProfile($userId, $username, $numero, $hashedPassword = null, $password = null)
{
    try {
        // Construire la requête de mise à jour
        $query = "UPDATE utilisateurs SET nom = :username, numero_etu = :numero";
        if ($password) {
            $query .= ", mot_de_passe_nonh = :password";
        }
        if ($hashedPassword) {
            $query .= ", mot_de_passe = :password_h";
        }
        $query .= " WHERE id = :user_id";

        // Préparer la requête
        $stmt = $this->connection->prepare($query);

        // Lier les paramètres
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':numero', $numero);
        if ($password) {
            $stmt->bindParam(':password', $password);
        }
        if ($hashedPassword) {
            $stmt->bindParam(':password_h', $hashedPassword);
        }
        $stmt->bindParam(':user_id', $userId);

        // Exécuter la requête
        $stmt->execute();

        return "Profil mis à jour avec succès.";
    } catch (PDOException $e) {
        // Gérer les erreurs
        return "Erreur lors de la mise à jour du profil : " . $e->getMessage();
    }
}



// fonction permettant de mettre a jour son role par son id utilisateur
public function updateUserRole($userId, $newRoleId)
{
    $sql = "UPDATE utilisateurs SET role_id = :role_id WHERE id_utilisateur = :user_id";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
        'role_id' => $newRoleId,
        'user_id' => $userId
    ]);
}


// fonction qui permettant de recupérer le nom de son role
public function getRoleIdByName($roleName)
{
    $sql = "SELECT role_id FROM roles WHERE role_name = :role_name";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute(['role_name' => $roleName]);
    return $stmt->fetchColumn();
}



// fonction permettant de recupérer le user id  qui nous servira pour d'autre fonction(update role)
public function getUserById($userId)
{
    $sql = "
        SELECT utilisateurs.id, utilisateurs.nom, roles.role_name
        FROM utilisateurs
        LEFT JOIN roles ON utilisateurs.role_id = roles.role_id
        WHERE utilisateurs.id = :user_id
    ";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);

    
//fonction permettant de recupérer le numéro etudiant 
}
public function getUserIdByNumeroEtu($numeroEtu) {
    $query = "SELECT id FROM utilisateurs WHERE numero_etu = :numeroEtu";
    $stmt = $this->connection->prepare($query);
    $stmt->bindParam(':numeroEtu', $numeroEtu, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result ? $result['id'] : null;
}




    }
    
    








?> 