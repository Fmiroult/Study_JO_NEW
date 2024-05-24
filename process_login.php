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
            // Si l'utilisateur est un administrateur, rediriger directement
            if ($user['role'] === 'admin') {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['firstname'] . ' ' . $user['lastname'];
                $_SESSION['user_role'] = $user['role'];
                
                echo json_encode(['status' => 'success', 'redirect_to' => 'admin_dashboard.php']);
            } else {
                // Génération du code de vérification pour les utilisateurs non-admin
                $verification_code = rand(100000, 999999);

                // Sauvegarde du code de vérification et du timestamp dans la session
                $_SESSION['verification_code'] = $verification_code;
                $_SESSION['temp_user_id'] = $user['id'];
                $_SESSION['verification_code_timestamp'] = time();

                // Envoi d'un email avec le code de vérification
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.sendgrid.net';
                $mail->SMTPAuth = true;
                $mail->Username = getenv('SENDGRID_USERNAME'); // Utiliser la variable d'environnement
                $mail->Password = getenv('SENDGRID_API_KEY'); // Utiliser la variable d'environnement
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';

                $mail->setFrom('study.jo.fmi@gmail.com', 'Verification JO 2024');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Votre code de vérification';
                $mail->Body = "Bonjour, votre code de vérification est : <strong>$verification_code</strong><br><br> Attention, ce code ne restera valide que 30 minutes.";

                $mail->send();

                echo json_encode(['status' => 'success', 'redirect_to' => 'verify_code.php']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Email ou mot de passe incorrect.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'envoi de l\'email de vérification. Erreur Mailer : ' . $mail->ErrorInfo]);
    }
}
?>
