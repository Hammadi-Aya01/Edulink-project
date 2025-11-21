<?php
$host = 'localhost';
$dbname = 'edulink';
$username = 'root'; // بدّل حسب إعداداتك
$password = '';     // بدّل حسب إعداداتك

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // تمكين الأخطاء
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Échec de connexion: " . $e->getMessage());
}
?>
