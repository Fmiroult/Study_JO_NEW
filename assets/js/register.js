$(document).ready(function() {
    $('#register-form').on('submit', function(event) {
        event.preventDefault();

        var password = $('#password').val();
        var confirmPassword = $('#confirm_password').val();
        var passwordStrength = getPasswordStrength(password);

        if (passwordStrength < 3) {
            showErrorPopup('Le mot de passe est trop faible. Veuillez choisir un mot de passe plus fort.');
            return;
        }

        if (password !== confirmPassword) {
            showErrorPopup('Les mots de passe ne correspondent pas.');
            return;
        }

        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: 'process_register.php',
            data: formData,
            success: function(response) {
                try {
                    response = JSON.parse(response);
                    if (response.status === 'success') {
                        window.location.href = response.redirect_to;
                    } else {
                        console.error('Erreur serveur: ', response.message);
                        showErrorPopup(response.message + ' Détails: ' + response.error);
                    }
                } catch (e) {
                    console.error('Erreur lors du traitement de la réponse JSON: ', e);
                    console.error('Réponse brute: ', response);
                    showErrorPopup('Erreur lors de l\'inscription. Veuillez réessayer. Détails: ' + e.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors de la requête AJAX: ' + error);
                console.error('Statut: ' + status);
                console.error('Réponse XHR: ', xhr);
                showErrorPopup('Erreur lors de la requête AJAX. Veuillez réessayer. Détails: ' + error);
            }
        });
    });

    $('#error-ok').on('click', function() {
        $('#error-popup').hide();
    });

    $('#password').on('input', function() {
        var password = $(this).val();
        var strength = getPasswordStrength(password);

        var strengthText = '';
        var strengthColor = '';

        switch (strength) {
            case 1:
                strengthText = 'Faible';
                strengthColor = 'red';
                break;
            case 2:
                strengthText = 'Moyen';
                strengthColor = 'orange';
                break;
            case 3:
                strengthText = 'Fort';
                strengthColor = 'green';
                break;
            default:
                strengthText = '';
                strengthColor = 'transparent';
        }

        $('#password-strength-bar').css('width', (strength * 33.33) + '%').css('background-color', strengthColor);
        $('#password-strength-bar').text(strengthText);
    });
});

function getPasswordStrength(password) {
    var strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[\W]/)) strength++;
    return strength;
}

function showErrorPopup(message) {
    $('#error-message').text(message);
    $('#error-popup').show();
}
