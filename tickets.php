<?php
include 'config.php';
include 'error_handling.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect_to=tickets.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare('
        SELECT r.id, o.name AS offer_name, o.description AS offer_description, o.price, o.picture, r.qr_code 
        FROM reservations r 
        JOIN offers o ON r.offer_id = o.id 
        WHERE r.user_id = ?
    ');
    $stmt->execute([$user_id]);
    $reservations = $stmt->fetchAll();
} catch (PDOException $e) {
    echo 'Erreur : ' . $e->getMessage();
}

include 'includes/header.php';
?>
<style>
    <?php
        for ($i = 1; $i <= 100; $i++) {
            echo ".img{$i} { width: {$i}%; height:auto;}\n";
        }
    ?>
</style>
<section class="tickets">
    <div class="container">
        <h2>Mes Billets</h2>
        <?php if (empty($reservations)): ?>
            <p>Vous n'avez aucun billet.</p>
        <?php else: ?>
            <div class="flex">
                <ul>
                    <?php foreach ($reservations as $reservation): ?>
                        <li>
                            <h3><?php echo htmlspecialchars($reservation['offer_name']); ?></h3>
                            <p><?php echo htmlspecialchars($reservation['offer_description']); ?></p>
                            <p>Prix: <?php echo htmlspecialchars($reservation['price']); ?> €</p>
                            <div style="align-items: center;" class="flex">
                                <img class="img15" src="<?php echo htmlspecialchars($reservation['qr_code']); ?>" alt="QR Code pour <?php echo htmlspecialchars($reservation['offer_name']); ?>">
                                <img class="img20" src="assets/images/<?php echo htmlspecialchars($reservation['picture']); ?>">
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
