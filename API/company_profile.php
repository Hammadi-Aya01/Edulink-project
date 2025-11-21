<?php
include('includes_header.php');
include('config_db.php');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'company') {
    header('Location: login.php');
    exit();
}

$id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM companies WHERE id = ?");
$stmt->execute([$id]);
$company = $stmt->fetch();
?>

<main>
    <h2>Profil Entreprise</h2>
    <p><strong>Nom :</strong> <?= htmlspecialchars($company['name']) ?></p>
    <p><strong>Email :</strong> <?= htmlspecialchars($company['email']) ?></p>
    <p><strong>Adresse :</strong> <?= htmlspecialchars($company['address']) ?></p>
    <p><strong>Description :</strong> <?= htmlspecialchars($company['description']) ?></p>
</main>

<?php include('includes_footer.php'); ?>
