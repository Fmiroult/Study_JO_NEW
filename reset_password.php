<?php include 'includes/header.php'; ?>
<section class="reset-password">
    <div class="container">
        <h2>Réinitialiser le mot de passe</h2>
        <form id="reset-password-form">
            <input type="hidden" id="token" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
            <div class="form-group">
                <label for="new_password">Nouveau mot de passe :</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe :</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn">Réinitialiser</button>
        </form>
        <div id="message"></div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#reset-password-form').on('submit', function(event) {
            event.preventDefault();

            var password = $('#new_password').val();
            var confirmPassword = $('#confirm_password').val();

            if (password !== confirmPassword) {
                $('#message').text('Les mots de passe ne correspondent pas.');
                return;
            }

            var formData = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: 'process_reset_password.php',
                data: formData,
                success: function(response) {
                    try {
                        response = JSON.parse(response);
                        $('#message').text(response.message);
                    } catch (e) {
                        $('#message').text('Erreur lors du traitement de la demande. Veuillez réessayer.');
                    }
                },
                error: function() {
                    $('#message').text('Erreur lors de la requête AJAX. Veuillez réessayer.');
                }
            });
        });
    });
</script>
<?php include 'includes/footer.php'; ?>
