<?php
session_start();
// 1) Protection par session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id   = $_SESSION['user_id'];
$user_role = $_SESSION['user_role']; // 'etudiant', 'enseignant' ou 'entreprise'

// 2) Connexion BD
$conn = new mysqli("localhost","root","","edulink");
if ($conn->connect_error) {
    die("Connection failed: ".$conn->connect_error);
}

// 3) Traitement du POST
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name      = $_POST['name'];
    $prenom    = $_POST['prenom'] ?? '';
    $email     = $_POST['email'];
    $telephone = $_POST['telephone'] ?? '';
    $adresse   = $_POST['adresse'] ?? '';

    // Mettre à jour la ligne correspondante
    $stmt = $conn->prepare("
        UPDATE users 
        SET name = ?, prenom = ?, email = ?, telephone = ?, adresse = ?
        WHERE id = ?
    ");
    $stmt->bind_param("sssssi",
        $name, $prenom, $email, $telephone, $adresse, $user_id
    );
    if ($stmt->execute()) {
        // Redirection vers la page profil selon le rôle
        switch($user_role) {
            case 'enseignant':  $loc = 'profil_enseignant.php'; break;
            case 'entreprise':  $loc = 'profil_entreprise.php'; break;
            default:            $loc = 'profil_etudiant.php';
        }
        header("Location: $loc");
        exit();
    } else {
        $error = "Erreur lors de la mise à jour : ".$stmt->error;
    }
}

// 4) Chargement des données existantes
$stmt = $conn->prepare("
    SELECT name, prenom, email, telephone, adresse 
    FROM users WHERE id = ?
");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier mon profil</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    /* Ton CSS inchangé */
    *{margin:0;padding:0;box-sizing:border-box;}
    body{font-family:'Segoe UI',sans-serif;background:#f5f6fa;color:#2c3e50;}
    .main{margin:60px auto;padding:40px;max-width:600px;}
    .section{background:white;padding:30px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.1);}
    .section h3{text-align:center;margin-bottom:25px;color:#2c3e50;font-size:22px;}
    form{display:flex;flex-direction:column;gap:15px;}
    label{font-weight:bold;}
    input[type=text],input[type=email]{padding:10px;border:1px solid #ccc;border-radius:6px;width:100%;}
    button{padding:12px;background:#2c3e50;color:white;border:none;border-radius:6px;cursor:pointer;font-size:16px;transition:background .3s;}
    button:hover{background:#34495e;}
    .error{color:red;margin-bottom:10px;}
  </style>
</head>
<body>
  <div class="main">
    <div class="section">
      <h3>Modifier mon profil</h3>
      <?php if(!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>
      <form action="modifier_profil.php" method="post">
        <label for="name">Nom :</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>">

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label for="telephone">Téléphone :</label>
        <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>">

        <label for="adresse">Adresse :</label>
        <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($user['adresse']) ?>">

        <button type="submit">Enregistrer</button>
      </form>
    </div>
  </div>
</body>
</html>
<?php $conn->close(); ?>
