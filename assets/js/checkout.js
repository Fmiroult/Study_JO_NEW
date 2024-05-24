$(document).ready(function() {
    $('#pay-button').on('click', function() {
        // Simuler un paiement
        setTimeout(function() {
            $('#payment-form').hide();
            $('#payment-result').show();

            // Récupérer les IDs des offres sélectionnées et leurs quantités
            var offerIds = {};
            $('input[name^="offer_id"]').each(function() {
                var offerId = $(this).attr('name').match(/\d+/)[0];
                var quantity = $(this).val();
                offerIds[offerId] = quantity;
            });

            // Envoi de la requête AJAX pour sauvegarder l'achat dans la base de données
            $.ajax({
                type: 'POST',
                url: 'process_purchase.php',
                data: { offer_ids: JSON.stringify(offerIds) },
                success: function(response) {
                    try {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            console.log('Achat enregistré avec succès');
                        } else {
                            console.error('Erreur lors de l\'enregistrement de l\'achat: ' + response.message);
                            $('#payment-result').text('Erreur lors de l\'enregistrement de l\'achat: ' + response.message).css('color', 'red');
                        }
                    } catch (e) {
                        console.error('Erreur de parsing JSON: ' + e);
                        $('#payment-result').text('Erreur interne. Veuillez réessayer plus tard.').css('color', 'red');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors de la requête AJAX: ' + error);
                    $('#payment-result').text('Erreur lors de la communication avec le serveur. Veuillez réessayer plus tard.').css('color', 'red');
                }
            });

        }, 1000); // Simule un délai de 1 seconde pour le paiement
    });
});
