<?php
session_start();
include 'config.php';
include 'error_handling.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $offer_id = $_GET['offer_id'];
    $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;
    if (!isset($_SESSION['cart'][$offer_id])) {
        $_SESSION['cart'][$offer_id] = 0;
    }
    $_SESSION['cart'][$offer_id] += $quantity;
    echo "success";
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    $offer_id = $_GET['offer_id'];
    if (isset($_SESSION['cart'][$offer_id])) {
        $_SESSION['cart'][$offer_id]--;
        if ($_SESSION['cart'][$offer_id] <= 0) {
            unset($_SESSION['cart'][$offer_id]);
        }
    }
}

// Récupérer les détails des offres dans le panier
$offers_in_cart = [];
if (!empty($_SESSION['cart'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $stmt = $pdo->prepare("SELECT id, name, description, price, picture, seats_consumed FROM offers WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($_SESSION['cart']));
    $offers_in_cart = $stmt->fetchAll();
}

// Récupérer le nombre total de places disponibles
$seats_stmt = $pdo->prepare("SELECT total_seats FROM seats WHERE id = 1");
$seats_stmt->execute();
$seats = $seats_stmt->fetch();
$total_seats = $seats['total_seats'];

// Récupérer le nombre total de places achetées
$purchased_seats_stmt = $pdo->query("
    SELECT SUM(offers.seats_consumed) AS purchased_seats
    FROM purchases
    JOIN offers ON purchases.offer_id = offers.id
");
$purchased_seats = $purchased_seats_stmt->fetch()['purchased_seats'];

include 'includes/header.php';
?>
<style>
    <?php
        for ($i = 1; $i <= 100; $i++) {
            echo ".img{$i} { width: {$i}%; height:auto;}\n";
        }
    ?>
</style>
<section class="cart">
    <div class="container">
        <h2>Votre Panier</h2>
        <p>Places disponibles : <?php echo htmlspecialchars($total_seats - $purchased_seats); ?></p>
        <p>Attention, les places ne seront réellement réservées qu'après le paiement éffectué.</p>
        <?php if (empty($offers_in_cart)): ?>
            <p>Votre panier est vide.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($offers_in_cart as $offer): ?>
                    <li style="display: flex; justify-content: space-between; align-items: center;">
                        <img class="img20" src="assets/images/<?php echo htmlspecialchars($offer['picture']); ?>">
                        <span>
                            <strong>Formule <?php echo htmlspecialchars($offer['name']); ?></strong><br>
                            <?php echo htmlspecialchars($offer['description']); ?> - <?php echo htmlspecialchars(number_format($offer['price'], 2)); ?> €
                            (x<?php echo $_SESSION['cart'][$offer['id']]; ?>)
                        </span>
                        <a href="panier.php?action=remove&offer_id=<?php echo $offer['id']; ?>" class="btn-remove" style="margin-left: 20px;">Retirer</a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a href="checkout.php" class="btn">Paiement</a>
        <?php endif; ?>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
