<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeux Olympiques 2024 - Accueil</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<style>
    <?php
        for ($i = 1; $i <= 100; $i++) {
            echo ".img{$i} { width: {$i}%; height:auto;}\n";
        }
    ?>
</style>
<body>
    <header class="flex">
            <a href="index.php">
                <div class="logo-image">
                        <img class="img20" src="assets/images/logo.png" alt="Olympics Logo">
                </div>
            </a>
            <div class="container flex">
                <div class="GoRight">
                    <nav>
                        <ul>
                            <li><a href="index.php">Accueil</a></li>
                            <li><a href="offers.php">Offres</a></li>
                            <li><a href="panier.php">Panier</a></li>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <li><a href="tickets.php">Mes Billets</a></li>
                                <?php
                                include 'config.php';
                                $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
                                $stmt->execute([$_SESSION['user_id']]);
                                $user = $stmt->fetch();
                                if ($user['role'] === 'admin'): ?>
                                    <li><a href="admin.php">Administration</a></li>
                                <?php endif; ?>
                                <li><a href="logout.php">Déconnexion</a></li>
                            <?php else: ?>
                                <li><a href="login.php">Connexion</a></li>
                                <li><a href="register.php">Inscription</a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
    </header>
    <main>
