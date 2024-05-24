<?php
include 'config.php';
include 'error_handling.php';
session_start();

if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];
    $firstname = $_GET['firstname'];
    $lastname = $_GET['lastname'];
    $hashed_password = $_GET['password'];
    $user_key = $_GET['user_key'];
    $cart = json_decode(urldecode($_GET['cart']), true);

    // Vérifier si l'utilisateur existe déjà
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        // Créer l'utilisateur
        $stmt = $pdo->prepare('INSERT INTO users (firstname, lastname, email, password, user_key) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$firstname, $lastname, $email, $hashed_password, $user_key]);

        // Restaurer le panier
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['cart'] = $cart;

        include 'includes/header.php';
        echo "<section class='verification'>
            <div class='container'>
                <h2>Vérification Email</h2>
                <p>Votre email a été vérifié avec succès. Vous pouvez maintenant procéder au paiement.</p>
                <a href='checkout.php' class='btn'>Continuer</a>
            </div>
        </section>";
        include 'includes/footer.php';
    } else {
        include 'includes/header.php';
        echo "<section class='verification'>
            <div class='container'>
                <h2>Vérification Email</h2>
                <p>Cette adresse email est déjà vérifiée.</p>
                <a href='login.php' class='btn'>Continuer</a>
            </div>
        </section>";
        include 'includes/footer.php';
    }
} else {
    include 'includes/header.php';
    echo "<section class='verification'>
        <div class='container'>
            <h2>Vérification Email</h2>
            <p>Lien de vérification invalide ou expiré.</p>
        <a href='login.php' class='btn'>Continuer</a>
    </div>
</section>";
    include 'includes/footer.php';
}
?>
