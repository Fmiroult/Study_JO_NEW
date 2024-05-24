<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $verification_code = $_POST['verification_code'];

    if (isset($_SESSION['verification_code']) && $_SESSION['verification_code'] == $verification_code) {
        $current_time = time();
        $code_generation_time = $_SESSION['verification_code_timestamp'];

        // Vérifiez si 30 minutes se sont écoulées
        if (($current_time - $code_generation_time) <= 1800) { // 1800 secondes = 30 minutes
            $_SESSION['user_id'] = $_SESSION['temp_user_id'];
            unset($_SESSION['verification_code']);
            unset($_SESSION['temp_user_id']);
            unset($_SESSION['verification_code_timestamp']);

            // Rediriger l'utilisateur vers la page de paiement s'il a des articles dans le panier
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                echo json_encode(['status' => 'success', 'redirect_to' => 'checkout.php']);
            } else {
                echo json_encode(['status' => 'success', 'redirect_to' => 'index.php']);
            }
        } else {
            unset($_SESSION['verification_code']);
            unset($_SESSION['temp_user_id']);
            unset($_SESSION['verification_code_timestamp']);
            echo json_encode(['status' => 'error', 'message' => 'Le code de vérification a expiré.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Code de vérification incorrect.']);
    }
}
?>
