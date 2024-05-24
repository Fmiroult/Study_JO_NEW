$(document).ready(function() {
    // Fonction pour calculer la force du mot de passe
    function calculatePasswordStrength(password) {
        var strength = 0;
        if (password.length >= 8) strength += 1;
        if (password.match(/[a-z]/)) strength += 1;
        if (password.match(/[A-Z]/)) strength += 1;
        if (password.match(/[0-9]/)) strength += 1;
        if (password.match(/[\W]/)) strength += 1;
        return strength;
    }

    // Mettre à jour la jauge de force du mot de passe
    $('#password').on('input', function() {
        var password = $(this).val();
        var strengthBar = $('#password-strength-bar');
        var strengthText = $('#password-strength-text');
        var strength = calculatePasswordStrength(password);

        switch (strength) {
            case 1:
                strengthBar.css('width', '20%').css('background-color', 'red');
                break;
            case 2:
                strengthBar.css('width', '40%').css('background-color', 'orange');
                break;
            case 3:
                strengthBar.css('width', '60%').css('background-color', 'yellow');

                break;
            case 4:
                strengthBar.css('width', '80%').css('background-color', 'yellowgreen');
                break;
            case 5:
                strengthBar.css('width', '100%').css('background-color', 'green');
                break;
            default:
                strengthBar.css('width', '0').css('background-color', 'transparent');
        }
    });

    // Gérer la soumission du formulaire
    $('#register-form').on('submit', function(event) {
        event.preventDefault();

        var password = $('#password').val();
        var passwordStrength = calculatePasswordStrength(password);

        if (passwordStrength < 3) { // Si le mot de passe est faible
            showErrorPopup('Le mot de passe est trop faible. Veuillez choisir un mot de passe plus fort.');
            return;
        }

        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: 'process_register.php',
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
            }
        });
    });

    function showErrorPopup(message) {
        $('#error-message').text(message);
        $('#error-popup').show();
    }

    $('#error-ok').on('click', function() {
        $('#error-popup').hide();
    });
});
