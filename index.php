<?php include 'includes/header.php';
include 'error_handling.php'; ?>
    <style>
        <?php
            for ($i = 1; $i <= 100; $i++) {
                echo ".img{$i} { width: {$i}%; height:auto;}\n";
            }
        ?>
    </style>
    <section class="JO">
        <div class="container">
            <h2>Bienvenue aux Jeux Olympiques 2024</h2>
            <p>Rejoignez-nous pour célébrer les plus grands athlètes du monde en France.</p>
            <a href="offers.php" class="btn">Voir les offres</a>
        </div>
    </section>
    <section class="events">
        <div class="container">
            <h2>Épreuves en vedette</h2>
            <div class="event-list">
                <div class="event-item">
                    <a href="offers.php">
                        <img class="img95" src="assets/images/athletics.jpg" alt="Athlétisme">
                    </a>
                    <h3>Athlétisme</h3>
                    <p>Découvrez les compétitions d'athlétisme, où les meilleurs sprinteurs, sauteurs et lanceurs du monde s'affrontent.</p>
                </div>
                <div class="event-item">
                    <a href="offers.php">
                        <img class="img80" src="assets/images/swimming.jpg" alt="Natation">
                    </a>
                    <h3>Natation</h3>
                    <p>Plongez dans l'action avec les épreuves de natation, où chaque seconde compte.</p>
                </div>
                <div class="event-item">
                    <a href="offers.php">
                        <img class="img70" src="assets/images/gymnastics.jpg" alt="Gymnastique">
                    </a>
                    <h3>Gymnastique</h3>
                    <p>Admirez la grâce et la puissance des gymnastes dans leurs performances époustouflantes.</p>
                </div>
            </div>
        </div>
    </section>
<?php include 'includes/footer.php'; ?>
