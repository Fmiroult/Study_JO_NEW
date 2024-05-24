<?php
session_start();
include 'config.php'; // Fichier contenant les informations de connexion à la base de données
include 'error_handling.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect_to=" . urlencode('checkout.php'));
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: offers.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Calculer le prix total
$total_price = 0;
$offers_in_cart = [];
foreach ($_SESSION['cart'] as $offer_id => $quantity) {
    $stmt = $pdo->prepare("SELECT id, name, price FROM offers WHERE id = ?");
    $stmt->execute([$offer_id]);
    $offer = $stmt->fetch();
    if ($offer) {
        $total_price += $offer['price'] * $quantity;
        $offers_in_cart[] = $offer;
    }
}

include 'includes/header.php';
?>
<section class="checkout">
    <div class="container">
        <h2>Paiement</h2>
        <form id="payment-form">
            <p>Montant total : <?php echo number_format($total_price, 2); ?> €</p>
            <?php foreach ($offers_in_cart as $offer): ?>
                <input type="hidden" name="offer_id[<?php echo $offer['id']; ?>]" value="<?php echo $_SESSION['cart'][$offer['id']]; ?>">
            <?php endforeach; ?>
            <button type="button" id="pay-button" class="btn" style="margin-bottom:10px">Payer</button>
        </form>
        <div id="payment-result" style="display: none; margin-bottom:10px">
            Paiement réussi. Votre réservation a été enregistrée.
        </div>
    </div>
</section>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/checkout.js"></script>
<?php include 'includes/footer.php'; ?>
