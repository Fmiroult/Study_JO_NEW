<?php
session_start();
include 'config.php';
include 'error_handling.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

include 'includes/header.php';
?>

<section class="admin">
    <div class="container">
        <h2 class="admin-title">Espace Administrateur</h2>
        <div class="admin-buttons">
            <form action="admin_offers.php" method="get">
                <button type="submit" class="btn admin-button">Gérer les offres</button>
            </form>
            <form action="admin_sales.php" method="get">
                <button type="submit" class="btn admin-button">Voir les ventes par offre</button>
            </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
