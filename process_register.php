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
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $user_key = bin2hex(random_bytes(16)); // Génération de la clé utilisateur
    $redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : 'index.php';

    try {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $existing_user = $stmt->fetch();

        if ($existing_user) {
            echo json_encode(['status' => 'error', 'message' => 'Cet email est déjà utilisé.']);
            exit;
        }

        $verification_token = bin2hex(random_bytes(16)); // Génération du token de vérification
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $cart_data = json_encode($cart);
        $cart_data = urlencode($cart_data);

        $verification_link = "https://jo-study-fmi-fbdd2be0127a.herokuapp.com/verify_email.php?token=$verification_token&email=$email&firstname=$firstname&lastname=$lastname&password=$password&user_key=$user_key&cart=$cart_data";

        // Configuration de PHPMailer
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.sendgrid.net';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('SENDGRID_USERNAME'); // Utiliser la variable d'environnement
        $mail->Password = getenv('SENDGRID_API_KEY'); // Utiliser la variable d'environnement
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // Configuration de l'email
        $mail->setFrom('study.jo.fmi@gmail.com', 'Vérification JO 2024');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Vérifiez votre adresse email';
        $mail->Body = "Merci de vous être inscrit. Veuillez cliquer <a href=\"$verification_link\">ici</a> pour valider votre inscription.";

        $mail->send();

        // Réponse JSON pour le succès
        echo json_encode(['status' => 'success', 'redirect_to' => 'pending_verification.php']);
    } catch (Exception $e) {
        // Réponse JSON pour l'erreur avec plus de détails
        echo json_encode([
            'status' => 'error',
            'message' => 'Erreur lors de l\'envoi de l\'email de vérification. Erreur Mailer : ' . $mail->ErrorInfo,
            'details' => $e->getMessage()
        ]);
    }
}
?>
