<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>UniConnect</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header>
    <nav>
        <a href="index.php">Accueil</a>
        <?php if (isset($_SESSION['user_type'])): ?>
            <?php if ($_SESSION['user_type'] == 'student'): ?>
                <a href="student_profile.php">Mon Profil Étudiant</a>
            <?php elseif ($_SESSION['user_type'] == 'company'): ?>
                <a href="company_profile.php">Mon Profil Entreprise</a>
            <?php endif; ?>
            <a href="logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="login.php">Connexion</a>
            <a href="register.php">Inscription</a>
        <?php endif; ?>
    </nav>
</header>
