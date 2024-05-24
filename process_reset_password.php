<?php
include 'config.php';
require 'vendor/autoload.php';

use Dotenv\Dotenv;

// Charger les variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];

    try {
        // Vérifier si le token est valide
        $stmt = $pdo->prepare('SELECT * FROM users WHERE reset_token = ?');
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user && $user['reset_expiration'] >= time()) {
            // Mettre à jour le mot de passe
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('UPDATE users SET password = ?, reset_token = NULL, reset_expiration = NULL WHERE reset_token = ?');
            $stmt->execute([$hashed_password, $token]);

            echo json_encode(['status' => 'success', 'message' => 'Votre mot de passe a été réinitialisé avec succès.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Le lien de réinitialisation est invalide ou a expiré.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la réinitialisation du mot de passe.']);
    }
}
?>
