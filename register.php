<?php include 'includes/header.php'; ?>
<section class="register">
    <div class="container">
        <h2>Inscription</h2>
        <form id="register-form" method="post" action="process_register.php">
            <div class="form-group">
                <label for="firstname">Prénom :</label>
                <input type="text" id="firstname" name="firstname" required>
            </div>
            <div class="form-group">
                <label for="lastname">Nom :</label>
                <input type="text" id="lastname" name="lastname" required>
            </div>
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
                <div id="password-strength">
                    <div id="password-strength-bar"></div>
                </div>
                <div id="password-strength-text"></div>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmez le mot de passe :</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($_GET['redirect_to'] ?? 'index.php'); ?>">
            <button type="submit" class="btn">S'inscrire</button>
        </form>
        <p>Déjà un compte ? <a href="login.php">Connectez-vous ici</a></p>
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
<script src="assets/js/register.js"></script>
<?php include 'includes/footer.php'; ?>
