<?php
session_start();
include 'includes/header.php';
include 'error_handling.php';
?>
    <section class="success">
        <div class="container">
            <h2>Paiement Réussi</h2>
            <p>Merci pour votre achat. Votre réservation est confirmée.</p>
            <a href='index.php' class='btn'>Retour à l'accueil</a>
        </div>
    </section>
<?php include 'includes/footer.php'; ?>
