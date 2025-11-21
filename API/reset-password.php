<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer l'email du formulaire
    $email = $_POST['email'];

    // Vérifier si l'email est valide
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $resetToken = bin2hex(random_bytes(16)); // Générer un token unique pour la réinitialisation
        $resetLink = "https://votresite.com/reset-password.php?token=$resetToken"; // Lien vers la page de réinitialisation

        // En-têtes de l'email
        $subject = "Réinitialisation de votre mot de passe";
        $message = "
            <html>
            <head>
                <title>Réinitialisation de votre mot de passe</title>
            </head>
            <body>
                <p>Bonjour,</p>
                <p>Nous avons reçu une demande de réinitialisation de votre mot de passe. Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>
                <a href='$resetLink'>$resetLink</a>
            </body>
            </html>
        ";

        // Pour envoyer un email en HTML, il faut spécifier le type de contenu
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: no-reply@votresite.com" . "\r\n"; // Remplacez par l'adresse e-mail de notre serveur

        // Envoi de l'email
        if (mail($email, $subject, $message, $headers)) {
            echo "Un e-mail de réinitialisation a été envoyé.";
        } else {
            echo "Échec de l'envoi de l'e-mail.";
        }
    } else {
        echo "Veuillez entrer une adresse email valide.";
    }
}
?>
