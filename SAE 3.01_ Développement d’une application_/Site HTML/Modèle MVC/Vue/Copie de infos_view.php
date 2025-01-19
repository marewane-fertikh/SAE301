<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collection - Université Sorbonne Paris Nord</title>
    <link rel="stylesheet" href="collection.css">
</head>
<body>
    <!-- Partie Header-->
    <header>
        <a href="accueil.html"><img src="img/LogoUSPN.png" alt="Sorbonne Paris Nord"></a>
        <nav>
            <!-- Redirection des pages-->
            <a href="documentation.html">Documentation</a>
            <a href="info.php">Collection</a>
            <a href="reservation_View.php">Réservation</a>
            <a href="https://cas.univ-paris13.fr/cas/login?service=https%3A%2F%2Fent.univ-paris13.fr">ENT</a>
        </nav>
        <!-- Barre de recherche-->
        <div class="search-bar">
            <form action="index.php" method="get">
                <input type="hidden" name="action" value="searchGame">
                <input type="text" name="query" placeholder="Rechercher un jeu..." required>
                <button type="submit">🔍</button>
            </form>
        </div>
        <!-- Profil utilisateur avec un menu déroulant lorsqu'on clique sur l'image-->
        <div class="profil-utilisateur" id="profilUtilisateur">
            <img src="img/profile.png" alt="Icône Profil" class="icone-utilisateur" onclick="basculerMenuDeroulant()">
            <div class="menu-deroulant" id="menuDeroulant">
                <a href="compte.php">Gestion du profil</a>
                <button class="bouton-deconnexion">Déconnexion</button>
            </div>
        </div>
    </header>
    <!-- Boutons Filtre et Ajouter un jeu -->
    <div class="actions">
        <button onclick="ouvrirFiltre()"> ⚙️ Filtrer</button>
        <button onclick="ouvrirAjoutJeu()">🎲 Ajouter un jeu</button>
    </div>

    <!-- Pop-up du filtre -->
    <div class="popup" id="popupFiltre">
        <div class="popup-content">
            <h2>  ⚙️ Filtrer les jeux</h2>
            <form action="index.php" method="get">
                <label>
                    <input type="radio" name="tri" value="ancien"> Du plus ancien au plus récent
                </label>
                <br>
                <label>
                    <input type="radio" name="tri" value="recent"> Du plus récent au plus ancien
                </label>
                <br>
                <!-- bouton qui permet de fermer une la page filtre -->
                <button type="submit" name="bouton" value="validation" onclick="fermerFiltre()">Appliquer</button>
            </form>
        </div>
    </div>

    <!-- Pop-up du jeu -->
    <div class="popup" id="popupAjoutJeu">
        <div class="popup-content">
            <h2>Ajouter un jeu</h2>
            <!-- Formulaire à remplir pour l'ajout d'un jeu -->
            <form action="index.php" method="get">
                <label for="titreJeu">Titre du jeu :</label>
                <input type="text" id="titreJeu" name="titreJeu" required>
                <br>
                <label for="dateDebut">Date de début:</label>
                <input type="date" id="dateDebut" name="dateDebut" required>
                <br>
                <label for="dateFin">Date de fin:</label>
                <input type="date" id="dateFin" name="dateFin" required>
                <br>
                <label for="Nbjoueurs">Nombre de joueurs :</label>
                <input type="text" id="Nbjoueurs" name="Nbjoueurs" required>
                <br>
                <label for="Age">Age :</label>
                <input type="text" id="Age" name="Age" required>
                <br>
                <button type="submit">Ajouter</button>
                <!-- Bouton de fermeture de l'ajout du jeu ( Annuler) -->
                <button type="button" onclick="fermerAjoutJeu()">Annuler</button>
            </form>
        </div>
    </div>

    <div class="container">
        <h1>Collection de jeux</h1>

        <?php if (!empty($games)): ?>
            <?php foreach ($games as $game): ?>
                <div class="game-card">
                    <h3><?= htmlspecialchars($game['titre']) ?></h3>
                    <p><strong>Auteur :</strong> <?= htmlspecialchars($game['auteurs']) ?></p>
                    <p><strong>Éditeur :</strong> <?= htmlspecialchars($game['editeurs']) ?></p>
                    <p><strong>Année de publication :</strong> <?= htmlspecialchars($game['date_parution_debut']) ?></p>
                    <p><strong>Nombre de joueurs :</strong> <?= htmlspecialchars($game['nombre_de_joueurs']) ?></p>
                    <p><strong>Type de jeu :</strong> <?= htmlspecialchars($game['mecanisme']) ?></p>
                    <a href="reservation_View.php?game=<?= urlencode($game['titre']) ?>"><button>Réserver</button></a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun jeu trouvé dans la base de données.</p>
        <?php endif; ?>
    </div>

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

        // Initialisation du calendrier
        document.addEventListener('DOMContentLoaded', () => {
            flatpickr("#dateParution", {
                dateFormat: "Y/m/d",
                locale: "fr"
            });
        });

        // Fonction d'ouverture et fermeture du pop-up filtre
        function ouvrirFiltre() {
            document.getElementById('popupFiltre').style.display = 'block';
        }
        function fermerFiltre() {
            document.getElementById('popupFiltre').style.display = 'none';
        }

        // Fonction d'ouverture et fermeture du pop-up jeu
        function ouvrirAjoutJeu() {
            document.getElementById('popupAjoutJeu').style.display = 'block';
        }
        function fermerAjoutJeu() {
            document.getElementById('popupAjoutJeu').style.display = 'none';
        }

        // Affichage du menu déroulant
        function basculerMenuDeroulant() {
            const menuDeroulant = document.getElementById('menuDeroulant');
            menuDeroulant.style.display = menuDeroulant.style.display === 'block' ? 'none' : 'block';
        }

        // Fermer le menu déroulant lorsque l'on clique en dehors du menu
        window.onclick = function(event) {
            if (!event.target.matches('.icone-utilisateur')) {
                const menusDeroulants = document.getElementsByClassName('menu-deroulant');
                for (let i = 0; i < menusDeroulants.length; i++) {
                    const menuOuvert = menusDeroulants[i];
                    if (menuOuvert.style.display === 'block') {
                        menuOuvert.style.display = 'none';
                    }
                }
            }
        }
    </script>
</body>
</html>