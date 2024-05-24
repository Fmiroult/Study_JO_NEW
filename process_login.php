<?php
include 'config.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// Charger les variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Vérifier les informations d'identification de l'utilisateur
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['firstname'] . ' ' . $user['lastname'];

            // Envoi d'un email de notification de connexion
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.sendgrid.net';
            $mail->SMTPAuth = true;
            $mail->Username = getenv('SENDGRID_USERNAME'); // Utiliser la variable d'environnement
            $mail->Password = getenv('SENDGRID_API_KEY'); // Utiliser la variable d'environnement
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom('study.jo.fmi@gmail.com', 'Notification de connexion');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Connexion réussie';
            $mail->Body = "Bonjour, vous vous êtes connecté avec succès à votre compte.";

            $mail->send();

            echo json_encode(['status' => 'success', 'redirect_to' => 'dashboard.php']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Email ou mot de passe incorrect.']);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erreur lors de l\'envoi de l\'email de notification. Erreur Mailer : ' . $mail->ErrorInfo,
            'details' => $e->getMessage()
        ]);
    }
}
?>
