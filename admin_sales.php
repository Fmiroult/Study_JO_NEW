<?php
session_start();
include 'config.php';
include 'error_handling.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || $user['role'] !== 'admin') {
        header("Location: index.php");
        exit;
    }

    // Récupérer le nombre total de places disponibles
    $seats_stmt = $pdo->prepare("SELECT total_seats FROM seats WHERE id = 1");
    $seats_stmt->execute();
    $seats = $seats_stmt->fetch();
    $total_seats = $seats ? $seats['total_seats'] : 0;

    // Récupérer le nombre total de places achetées
    $purchased_seats_stmt = $pdo->query("
        SELECT SUM(offers.seats_consumed) AS purchased_seats
        FROM purchases
        JOIN offers ON purchases.offer_id = offers.id
    ");
    $purchased_seats = $purchased_seats_stmt->fetch()['purchased_seats'];

    // Récupérer les ventes par offre
    $sales_stmt = $pdo->query("
        SELECT offers.name, COUNT(purchases.id) AS sales_count
        FROM purchases
        JOIN offers ON purchases.offer_id = offers.id
        GROUP BY offers.name
    ");
    $sales = $sales_stmt->fetchAll();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Une erreur s\'est produite. Veuillez réessayer plus tard.']);
    exit;
}

include 'includes/header.php';
?>

<section class="admin-sales">
    <div class="container">
        <h2>Ventes par Offre</h2>
        <p>Places totales : <?php echo htmlspecialchars($total_seats); ?></p>
        <p>Places achetées : <?php echo htmlspecialchars($purchased_seats); ?></p>
        <p>Places disponibles : <?php echo htmlspecialchars($total_seats - $purchased_seats); ?></p>
        <table>
            <tr>
                <th>Offre</th>
                <th>Nombre de ventes</th>
            </tr>
            <?php foreach ($sales as $sale): ?>
                <tr>
                    <td><?php echo htmlspecialchars($sale['name']); ?></td>
                    <td><?php echo htmlspecialchars($sale['sales_count']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
