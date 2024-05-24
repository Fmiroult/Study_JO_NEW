<?php
include 'includes/header.php';
include 'config.php';
include 'error_handling.php';

// Récupérer les offres depuis la base de données
$stmt = $pdo->prepare("SELECT id, name, description, price, picture FROM offers");
$stmt->execute();
$offers = $stmt->fetchAll();
?>
<style>
    <?php
        for ($i = 1; $i <= 100; $i++) {
            echo ".img{$i} { width: {$i}%; height:auto;}\n";
        }
    ?>
</style>
<section class="offers">
    <div class="container">
        <h2>Offres de Billets</h2>
        <div class="offer-list">
            <?php foreach ($offers as $offer): ?>
                <div class="offer-item">
                    <img class="img40" src="assets/images/<?php echo htmlspecialchars($offer['picture']); ?>">
                    <h3><?php echo htmlspecialchars($offer['name']); ?></h3>
                    <p><?php echo htmlspecialchars($offer['description']); ?></p>
                    <p>Prix: <?php echo htmlspecialchars(number_format($offer['price'], 2)); ?> €</p>
                    <label for="quantity-<?php echo $offer['id']; ?>">Quantité :</label>
                    <input type="number" name="quantity" id="quantity-<?php echo $offer['id']; ?>" value="1" min="1" max="10">
                    <button class="btn add-to-cart" data-id="<?php echo $offer['id']; ?>">Ajouter au panier</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Voile gris transparent -->
<div id="overlay" style="display: none;"></div>

<!-- Pop-up de confirmation -->
<div id="popup" style="display: none;">
    <div id="popup-content">
        <p>Les tickets ont bien été ajoutés au panier</p>
        <button id="go-to-cart" class="btn">Aller au panier</button>
        <button id="continue-shopping" class="btn">Continuer mes achats</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/offers.js"></script>
<?php include 'includes/footer.php'; ?>
