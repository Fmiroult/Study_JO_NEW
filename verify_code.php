<?php include 'includes/header.php'; ?>
<section class="verify-code">
    <div class="container">
        <h2>Vérification du Code</h2>
        <p>Veuillez récupérer le code qui vous a été transmit par mail et le noter ci-dessous.</p>
        <p>Veillez à bien vérifier vos spam et/ou courriers indésirables.</p>
        <p>Attention, ce code est valable 30 minutes.</p>
        <form id="verify-code-form">
            <div class="form-group">
                <label for="verification_code">Code de vérification :</label>
                <input type="text" id="verification_code" name="verification_code" required>
            </div>
            <button type="submit" class="btn">Vérifier</button>
        </form>
        <div id="error-popup" style="display: none;">
            <div id="error-popup-content">
                <p id="error-message"></p>
                <button id="error-ok" class="btn">OK</button>
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#verify-code-form').on('submit', function(event) {
        event.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: 'process_verify_code.php',
            data: formData,
            success: function(response) {
                response = JSON.parse(response);
                if (response.status === 'success') {
                    window.location.href = response.redirect_to;
                } else {
                    $('#error-message').text(response.message);
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
</script>
<?php include 'includes/footer.php'; ?>
