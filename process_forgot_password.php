<?php
include 'config.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// Charger les variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    try {
        // Vérifier si l'email existe
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Génération du token de réinitialisation
            $reset_token = bin2hex(random_bytes(16));
            $reset_expiration = time() + 3600; // 1 heure de validité

            // Sauvegarde du token et de la date d'expiration dans la base de données
            $stmt = $pdo->prepare('UPDATE users SET reset_token = ?, reset_expiration = ? WHERE email = ?');
            $stmt->execute([$reset_token, $reset_expiration, $email]);

            // Envoi d'un email avec le lien de réinitialisation
            $reset_link = "https://study-jo-fmi-new-f0cb4b12b08f.herokuapp.com/reset_password.php?token=$reset_token";

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.sendgrid.net';
            $mail->SMTPAuth = true;
            $mail->Username = getenv('SENDGRID_USERNAME'); // Utiliser la variable d'environnement
            $mail->Password = getenv('SENDGRID_API_KEY'); // Utiliser la variable d'environnement
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom('study.jo.fmi@gmail.com', 'Réinitialisation de mot de passe');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de votre mot de passe';
            $mail->Body = "Bonjour, cliquez <a href=\"$reset_link\">ici</a> pour réinitialiser votre mot de passe. Ce lien est valide pendant une heure.";

            $mail->send();

            echo json_encode(['status' => 'success', 'message' => 'Un email avec le lien de réinitialisation a été envoyé.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Cette adresse email n\'existe pas.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'envoi de l\'email de réinitialisation. Erreur Mailer : ' . $mail->ErrorInfo]);
    }
}
?>
