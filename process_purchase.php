<?php
include 'config.php';
include 'error_handling.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $offer_ids = json_decode($_POST['offer_ids'], true);
    $reservation_key = bin2hex(random_bytes(16)); // Génération de la clé de réservation

    $user_key_stmt = $pdo->prepare("SELECT user_key FROM users WHERE id = ?");
    $user_key_stmt->execute([$user_id]);
    $user_key = $user_key_stmt->fetchColumn();

    try {
        // Démarrer la transaction
        $pdo->beginTransaction();

        // Récupérer le nombre total de places disponibles
        $seats_stmt = $pdo->prepare("SELECT total_seats FROM seats WHERE id = 1 FOR UPDATE");
        $seats_stmt->execute();
        $seats = $seats_stmt->fetch();

        $total_seats = $seats['total_seats'];

        foreach ($offer_ids as $offer_id => $quantity) {
            // Récupérer les informations de l'offre
            $offer_stmt = $pdo->prepare("SELECT name, price, seats_consumed FROM offers WHERE id = ?");
            $offer_stmt->execute([$offer_id]);
            $offer = $offer_stmt->fetch();

            $seats_required = $offer['seats_consumed'] * $quantity;

            // Vérifier si suffisamment de places sont disponibles
            if ($total_seats < $seats_required) {
                $pdo->rollBack();
                echo json_encode(['status' => 'error', 'message' => 'Nombre de places insuffisant pour l\'offre ID: ' . $offer_id]);
                exit;
            }

            $total_seats -= $seats_required;

            for ($i = 0; $i < $quantity; $i++) {
                $final_key = hash('sha256', $user_key . $reservation_key . $i); // Concaténation des clés avec un index unique pour chaque billet
                $qr_code = "https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode($final_key); // Génération de QR code

                // Insérer dans la table reservations
                $reservation_stmt = $pdo->prepare("INSERT INTO reservations (user_id, offer_id, reservation_key, qr_code) VALUES (?, ?, ?, ?)");
                $reservation_stmt->execute([$user_id, $offer_id, $final_key, $qr_code]);

                // Insérer dans la table purchases
                $purchase_stmt = $pdo->prepare("INSERT INTO purchases (user_id, offer_id, purchase_key) VALUES (?, ?, ?)");
                $purchase_stmt->execute([$user_id, $offer_id, $final_key]);
            }
        }

        // Mettre à jour le nombre total de places disponibles
        $update_seats_stmt = $pdo->prepare("UPDATE seats SET total_seats = ? WHERE id = 1");
        $update_seats_stmt->execute([$total_seats]);

        // Valider la transaction
        $pdo->commit();

        // Suppression du panier après l'achat
        unset($_SESSION['cart']);

        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la réservation : ' . $e->getMessage()]);
    }
}
?>
