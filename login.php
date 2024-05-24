<?php include 'includes/header.php'; 
include 'error_handling.php';?>
<section class="login">
    <div class="container">
        <h2>Connexion</h2>
        <form id="login-form">
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
            </div>
            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($_GET['redirect_to'] ?? 'index.php'); ?>">
            <button type="submit" class="btn">Se connecter</button>
        </form>
        <p>Pas encore de compte ? <a href="register.php">Inscrivez-vous ici</a></p>
    </div>
</section>

<!-- Pop-up d'erreur -->
<div id="error-popup" style="display: none;">
    <div id="error-popup-content">
        <p id="error-message"></p>
        <button id="error-ok" class="btn">OK</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/login.js"></script>
<?php include 'includes/footer.php'; ?>
