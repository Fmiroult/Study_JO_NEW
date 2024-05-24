$(document).ready(function() {
    $('#verify-code-form').on('submit', function(event) {
        event.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: 'process_verify_code.php',
            data: formData,
            success: function(response) {
                try {
                    response = JSON.parse(response);
                    if (response.status === 'success') {
                        window.location.href = response.redirect_to;
                    } else {
                        $('#error-message').text(response.message);
                        $('#error-popup').show();
                    }
                } catch (e) {
                    console.error('Erreur lors du traitement de la réponse JSON: ', e);
                    console.error('Réponse brute: ', response);
                    $('#error-message').text('Erreur lors de la vérification. Veuillez réessayer.');
                    $('#error-popup').show();
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors de la requête AJAX: ' + error);
                $('#error-message').text('Erreur lors de la requête AJAX. Veuillez réessayer.');
                $('#error-popup').show();
            }
        });
    });

    $('#error-ok').on('click', function() {
        $('#error-popup').hide();
    });
});
