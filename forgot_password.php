<?php include 'includes/header.php'; ?>
<section class="forgot-password">
    <div class="container">
        <h2>Mot de passe oublié</h2>
        <form id="forgot-password-form">
            <div class="form-group">
                <label for="email">Adresse email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit" class="btn">Envoyer</button>
        </form>
        <div id="message"></div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#forgot-password-form').on('submit', function(event) {
            event.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: 'process_forgot_password.php',
                data: formData,
                success: function(response) {
                    try {
                        response = JSON.parse(response);
                        $('#message').text(response.message);
                    } catch (e) {
                        $('#message').text('Erreur lors de l\'envoi de la demande. Veuillez réessayer.');
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
