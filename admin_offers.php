<?php
session_start();
include 'config.php';
include 'error_handling.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        $offer_id = $_POST['offer_id'];
        $stmt = $pdo->prepare("DELETE FROM offers WHERE id = ?");
        $stmt->execute([$offer_id]);
    } else {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $seats_consumed = $_POST['seats_consumed'];
        $picture = '';

        if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
            $picture = basename($_FILES['picture']['name']);
            $target_dir = "assets/images/";
            $target_file = $target_dir . $picture;

            // Move the uploaded file to the target directory
            if (!move_uploaded_file($_FILES['picture']['tmp_name'], $target_file)) {
                $picture = '';
            }
        }

        $stmt = $pdo->prepare("INSERT INTO offers (name, description, price, seats_consumed, picture) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $seats_consumed, $picture]);
    }
}

$offers_stmt = $pdo->query("SELECT * FROM offers");
$offers = $offers_stmt->fetchAll();

include 'includes/header.php';
?>

<section class="admin-offers">
    <div class="container">
        <h2>Gérer les Offres</h2>
        <form method="post" action="admin_offers.php" enctype="multipart/form-data">
            <h3>Ajouter une nouvelle offre</h3>
            <div class="form-group">
                <label for="name">Nom de l'offre :</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Description :</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Prix :</label>
                <input type="text" id="price" name="price" required>
            </div>
            <div class="form-group">
                <label for="seats_consumed">Nombre de places consommées :</label>
                <input type="number" id="seats_consumed" name="seats_consumed" required>
            </div>
            <div class="form-group">
                <label for="picture">Image :</label>
                <input type="file" id="picture" name="picture" accept="image/*">
            </div>
            <button type="submit" class="btn">Ajouter l'offre</button>
        </form>

        <h3>Offres existantes</h3>
        <table>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Prix</th>
                <th>Nombre de places consommées</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($offers as $offer): ?>
                <tr>
                    <td><?php echo htmlspecialchars($offer['name']); ?></td>
                    <td><?php echo htmlspecialchars($offer['description']); ?></td>
                    <td><?php echo htmlspecialchars($offer['price']); ?></td>
                    <td><?php echo htmlspecialchars($offer['seats_consumed']); ?></td>
                    <td>
                        <?php if ($offer['picture']): ?>
                            <img src="assets/images/<?php echo htmlspecialchars($offer['picture']); ?>" alt="<?php echo htmlspecialchars($offer['name']); ?>" width="100">
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="post" action="admin_offers.php" style="display:inline;">
                            <input type="hidden" name="offer_id" value="<?php echo $offer['id']; ?>">
                            <button type="submit" name="delete" class="btn">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
