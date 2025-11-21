<?php
// Connexion à la base de données
$host = "localhost"; 
$user = "root"; 
$password = ""; 
$dbname = "plateforme_education";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_prenom = htmlspecialchars($_POST['nom_prenom']);
    $num_carte = htmlspecialchars($_POST['num_carte']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $filiere = htmlspecialchars($_POST['filiere']);

    // Vérifier si les mots de passe correspondent
    if ($password !== $confirm_password) {
        die("Les mots de passe ne correspondent pas.");
    }

    // Hachage du mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Vérifier si l'email ou le numéro de carte existe déjà
    $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE email = ? OR num_carte = ?");
    $stmt->execute([$email, $num_carte]);

    if ($stmt->rowCount() > 0) {
        die("Cet email ou numéro de carte est déjà utilisé.");
    } else {
        // Insérer l'étudiant dans la base de données
        $sql = "INSERT INTO etudiants (nom_prenom, num_carte, email, password, filiere) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$nom_prenom, $num_carte, $email, $hashed_password, $filiere])) {
            echo "Inscription réussie !";
        } else {
            echo "Erreur lors de l'inscription.";
        }
    }
}
?>
