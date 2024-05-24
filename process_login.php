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
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Vérifier si l'utilisateur est un administrateur
            if ($user['role'] === 'admin') {
                $_SESSION['user_id'] = $user['id'];
                echo json_encode(['status' => 'success', 'redirect_to' => 'admin.php']);
            } else {
                // Générer un code de vérification pour les utilisateurs non-admin
                $verification_code = rand(100000, 999999);
                $_SESSION['verification_code'] = $verification_code;
                $_SESSION['temp_user_id'] = $user['id'];
                $_SESSION['verification_code_timestamp'] = time(); // Enregistrer le timestamp

                // Envoyer le code de vérification par email
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.sendgrid.net';
                $mail->SMTPAuth = true;
                $mail->getenv('SENDGRID_USERNAME');
                $mail->getenv('SENDGRID_API_KEY');
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';

                $mail->setFrom('study.jo.fmi@gmail.com', 'JO 2024 Verification');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Votre code de vérification';
                $mail->Body = "Votre code de vérification est : <strong>$verification_code</strong><br><br>Attention, ce code ne restera valide que 30 minutes.";

                $mail->send();

                echo json_encode(['status' => 'success', 'redirect_to' => 'verify_code.php']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Email ou mot de passe incorrect.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur : ' . $e->getMessage()]);
    }
}
?>
