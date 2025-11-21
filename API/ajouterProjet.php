<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connexion correcte
$conn = new mysqli('localhost', 'root', '', 'edulink');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$titre = $_POST['titre'] ?? '';
$description = $_POST['description'] ?? '';

$etudiantID = 1;
$enseignantID = 1;
$entrepriseID = 1;

if (!empty($titre) && !empty($description)) {
    $stmt = $conn->prepare("INSERT INTO projet (Type, Titre, Description, EtudiantID, EnseignantID, EntrepriseID) VALUES ('PFE', ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Erreur prepare: " . $conn->error);
    }
    $stmt->bind_param("ssiii", $titre, $description, $etudiantID, $enseignantID, $entrepriseID);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Erreur execute: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Champs vides!";
}

$conn->close();
?>
