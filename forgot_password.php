<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
</head>
<body>
    <h2>Mot de passe oublié</h2>
    <form id="forgot-password-form">
        <label for="email">Adresse email :</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Envoyer</button>
    </form>
    <div id="message"></div>

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
</body>
</html>
