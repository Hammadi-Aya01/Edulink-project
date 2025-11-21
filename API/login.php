<?php
session_start();

// Connection base
$conn = new mysqli("localhost", "root", "", "edulink");

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"]; // mot de passe

    // Recherche utilisateur
    $sql = "SELECT * FROM enseignants WHERE Motpass = ?"; // Email = champ Motpass ici
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // si email trouvé : login réussi
        $_SESSION['enseignant_id'] = $row['EnseignantID'];
        header("Location: profile_enseignant.php");
        exit();
    } else {
        echo "<script>alert('Email ou mot de passe incorrect'); window.location.href='login.html';</script>";
        exit();
    }
}
?>
