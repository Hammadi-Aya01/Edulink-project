<?php
include('includes_header.php');
include('config_db.php');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: login.php');
    exit();
}

$id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();
?>

<main>
    <h2>Profil Étudiant</h2>
    <p><strong>Nom :</strong> <?= htmlspecialchars($student['name']) ?></p>
    <p><strong>Email :</strong> <?= htmlspecialchars($student['email']) ?></p>
    <p><strong>Numéro étudiant :</strong> <?= htmlspecialchars($student['student_number']) ?></p>
    <p><strong>Bio :</strong> <?= htmlspecialchars($student['bio']) ?></p>
</main>

<?php include('includes_footer.php'); ?>
