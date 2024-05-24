$(document).ready(function() {
    $('.add-to-cart').on('click', function() {
        var offerId = $(this).data('id');
        var quantity = $('#quantity-' + offerId).val();

        $.ajax({
            type: 'GET',
            url: 'panier.php',
            data: { action: 'add', offer_id: offerId, quantity: quantity },
            success: function(response) {
                // Afficher le voile gris et le pop-up de confirmation
                $('#overlay').show();
                $('#popup').show();
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors de l\'ajout au panier: ' + error);
            }
        });
    });

    $('#go-to-cart').on('click', function() {
        window.location.href = 'panier.php';
    });

    $('#continue-shopping').on('click', function() {
        $('#overlay').hide();
        $('#popup').hide();
    });
});
