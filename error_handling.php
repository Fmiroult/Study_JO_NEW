<?php
// Activer le rapport d'erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!function_exists('customErrorHandler')) {
    // Définir un gestionnaire d'erreurs personnalisé
    function customErrorHandler($errno, $errstr, $errfile, $errline)
    {
        $error_message = "Erreur [$errno] : $errstr dans le fichier $errfile à la ligne $errline";
        error_log($error_message, 3, 'errors.log'); // Enregistrer les erreurs dans un fichier de log
        // Vous pouvez également envoyer un email aux administrateurs ici si nécessaire
        if (!(error_reporting() & $errno)) {
            return;
        }
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    set_error_handler('customErrorHandler');
}

if (!function_exists('customExceptionHandler')) {
    // Définir un gestionnaire d'exceptions personnalisé
    function customExceptionHandler($exception)
    {
        $error_message = "Exception non capturée : " . $exception->getMessage();
        error_log($error_message, 3, 'errors.log');
        echo json_encode(['status' => 'error', 'message' => 'Une erreur s\'est produite. Veuillez réessayer plus tard.']);
        exit;
    }

    set_exception_handler('customExceptionHandler');
}

if (!function_exists('shutdownFunction')) {
    // Définir une fonction pour les erreurs fatales
    function shutdownFunction()
    {
        $error = error_get_last();
        if ($error !== NULL) {
            $error_message = "Erreur fatale : " . $error['message'] . " dans le fichier " . $error['file'] . " à la ligne " . $error['line'];
            error_log($error_message, 3, 'errors.log');
            echo json_encode(['status' => 'error', 'message' => 'Une erreur fatale s\'est produite. Veuillez réessayer plus tard.']);
            exit;
        }
    }

    register_shutdown_function('shutdownFunction');
}
?>
