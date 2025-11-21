<?php
// Connexion à la base de données
$host = "localhost"; 
$user = "root"; 
$password = ""; 
$dbname = "plateforme_education";

// Connexion avec PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_prenom = htmlspecialchars($_POST['nom_prenom']);
    $cin = htmlspecialchars($_POST['cin']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $specialite = htmlspecialchars($_POST['specialite']);

    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT * FROM enseignants WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        echo "Cet email est déjà utilisé.";
    } else {
        // Insérer l'enseignant dans la base de données
        $sql = "INSERT INTO enseignants (nom_prenom, cin, email, password, specialite) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$nom_prenom, $cin, $email, $password, $specialite])) {
            echo "Inscription réussie !";
        } else {
            echo "Erreur lors de l'inscription.";
        }
    }
}
?>
