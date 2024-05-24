$(document).ready(function() {
    function getPasswordStrength(password) {
        var strength = 0;
        var criteria = [
            (password.length >= 8),
            /[a-z]/.test(password),
            /[A-Z]/.test(password),
            /[0-9]/.test(password),
            /[\W]/.test(password)
        ];

        // Compter le nombre de critères remplis
        strength = criteria.reduce((acc, cur) => acc + cur, 0);

        return strength;
    }

    $('#password').on('input', function() {
        var password = $(this).val();
        var strength = getPasswordStrength(password);
        var strengthBar = $('#password-strength-bar');
        var strengthText = $('#password-strength-text');

        switch (strength) {
            case 1:
                strengthBar.css('width', '20%').css('background-color', 'red');
                strengthText.text('Très faible').css('color', 'red');
                break;
            case 2:
                strengthBar.css('width', '40%').css('background-color', 'orange');
                strengthText.text('Faible').css('color', 'orange');
                break;
            case 3:
                strengthBar.css('width', '60%').css('background-color', 'yellow');
                strengthText.text('Moyen').css('color', 'yellow');
                break;
            case 4:
                strengthBar.css('width', '80%').css('background-color', 'yellowgreen');
                strengthText.text('Bon').css('color', 'yellowgreen');
                break;
            case 5:
                strengthBar.css('width', '100%').css('background-color', 'green');
                strengthText.text('Très bon').css('color', 'green');
                break;
            default:
                strengthBar.css('width', '0').css('background-color', 'transparent');
                strengthText.text('');
        }
    });

    $('#register-form').on('submit', function(event) {
        event.preventDefault();

        var password = $('#password').val();
        var confirmPassword = $('#confirm_password').val();
        var passwordStrength = getPasswordStrength(password);

        if (passwordStrength < 5) {
            showErrorPopup('Le mot de passe ne respecte pas les restrictions. Il doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.');
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
                response = JSON.parse(response);
                if (response.status === 'success') {
                    window.location.href = response.redirect_to;
                } else {
                    showErrorPopup(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors de la requête AJAX: ' + error);
                showErrorPopup('Erreur lors de la requête AJAX. Veuillez réessayer.');
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
