<?php
// Récupérer le nom du jeu depuis l'URL
$gameName = isset($_GET['game']) ? htmlspecialchars($_GET['game']) : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="reservation_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
    <title>Réservation - Sorbonne Paris Nord</title>
</head>
<body>

<!-- Partie Header (Entête du site) -->
<header>
    <a href="accueil.html"><img src="img/LogoUSPN.png" alt="Sorbonne Paris Nord"></a>
    <nav>
        <a href="#">Documentation</a>
        <a href="info.php">Collection</a>
        <a href="reservation_View.php">Réservation</a>
        <a href="https://cas.univ-paris13.fr/cas/login?service=https%3A%2F%2Fent.univ-paris13.fr">ENT</a>
    </nav>
    <div class="search-bar">
    <form action="index.php" method="get">
            <input type="hidden" name="action" value="searchGame">
            <input type="text" name="query" placeholder="Recherche...">
            <button type="submit">🔍</button>
            </form>
    </div>
    <div class="user-icon">👤</div>
</header>

<!-- Partie Principale du site -->
<main>
    <div class="reservation-form">
        <h2>Réservation</h2>
        <form action="index.php" method="post">
            <!-- Numéro étudiant -->
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" placeholder="Nom" required>

            <label for="email">Email</label>
            <input type="text" id="email" name="email" placeholder="Email" required> <!-- 15 janvier je me suis arrete la -->

            <label for="adresse">Adresse</label>
            <input type="text" id="adresse" name="adresse" placeholder="Adresse" required>

            <!-- Sélection du jeu -->
            <label for="game-select">Jeux de société...</label>
            <input type="text" id="game-select" name="game-select" value="<?= $gameName ?>" placeholder="Jeux de société" required>

            

            <!-- Sélecteur de date -->
            <label for="date-range">Début et fin d'emprunt</label>
            <input type="text" id="date-range" name="date-range" placeholder="Sélectionner vos dates d'emprunt" required>

            <!-- Bouton de soumission -->
            <button type="submit" name="reserverGame">Réserver</button>
        </form>
    </div>
</main>

<script>
    // Crée un objet qui permet de manipuler les paramètres de l'URL
    const params = new URLSearchParams(window.location.search);
    // Récupère la valeur du paramètre "jeu" depuis l'URL
    const game = params.get('jeu');
    if (game) {
        const selectElement = document.getElementById('game-select');
        // Définit la valeur sélectionnée de la liste déroulante avec le jeu récupéré 
        selectElement.value = game;
    }
</script>


<!-- Footer -->
<footer class="footer">
    <p>
        <a href="mentions.html">Mentions légales</a> |
        <a href="politique.html">Politique de cookies</a> |
        <a href="protection.html">Protection de données</a>
    </p>
</footer>

<!--Configuration de la biliothèque Flatpickr pour pouvoir utiliser un calendrier -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<!--Pour que les noms soit traduits en français dans le calendrier-->
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
<script>
    flatpickr("#date-range", {
        mode: "range", // Permet de sélectionner une plage de dates
        dateFormat: "Y/m/j", // Format d'affichage des dates
        locale: "fr", // Activation de la langue française
        altInput: true, // Champ d'affichage alternatif
        altFormat: "Y/m/j", // Format lisible (ex : 15 janvier 2025)
        weekNumbers: true, // Affiche les numéros de semaine
        minDate: "today", // Empêche la sélection des dates passées
        onChange: function(Choixdate) {
            // Vérifie si deux dates sont sélectionnées
            if (Choixdate.length === 2) {
                const Datedebut = Choixdate[0];
                const Datefin = Choixdate[1];

                // Calcul de la différence en jours
                const diff = Math.abs(Datefin - Datedebut);
                const JMax = Math.ceil(diff / (1000 * 60 * 60 * 24));

                // Si la différence est supérieure à 7 jours, affiche un message d'erreur
                if (JMax > 7) {
                    alert("La durée maximale d'emprunt est d'une semaine !");
                    this.clear(); // Réinitialise la sélection
                }
            }
        }
    });
</script>

</body>
</html>