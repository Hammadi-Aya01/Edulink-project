<?php
session_start();
if (!isset($_SESSION['etudiant_id'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "edulink");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$etudiant_id = $_SESSION['etudiant_id'];
$sql = "SELECT * FROM etudiant WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $etudiant_id);
$stmt->execute();
$result = $stmt->get_result();
$etudiant = $result->fetch_assoc();

// Charger les projets
$sql_proj = "SELECT * FROM projets WHERE etudiant_id = ?";
$stmt_proj = $conn->prepare($sql_proj);
$stmt_proj->bind_param("i", $etudiant_id);
$stmt_proj->execute();
$result_proj = $stmt_proj->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Profil Étudiant</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f6fa;
      margin: 0;
    }

    header {
      background-color: #2c3e50;
      color: white;
      padding: 20px;
      text-align: center;
    }

    .container {
      max-width: 1000px;
      margin: 30px auto;
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .profile {
      display: flex;
      gap: 30px;
      align-items: center;
      margin-bottom: 30px;
    }

    .profile img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
    }

    .profile-info {
      flex: 1;
    }

    .profile-info h2 {
      margin-bottom: 5px;
    }

    .profile-info p {
      margin: 3px 0;
      color: #555;
    }

    .edit-btn {
      padding: 8px 15px;
      background-color: #2c3e50;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 10px;
    }

    .edit-btn:hover {
      background-color: #34495e;
    }

    .section {
      margin-top: 30px;
    }

    .section h3 {
      color: #2c3e50;
      margin-bottom: 10px;
    }

    .cv, .posts {
      background-color: #f0f2f5;
      padding: 15px;
      border-radius: 8px;
    }

    .cv ul {
      list-style: disc;
      padding-left: 20px;
    }

    .post {
      background: white;
      padding: 15px;
      margin-bottom: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      position: relative;
    }

    .post h4 {
      margin: 0;
    }

    .delete-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      background: red;
      color: white;
      border: none;
      border-radius: 4px;
      padding: 5px 10px;
      cursor: pointer;
    }

    .delete-btn:hover {
      background: darkred;
    }
  </style>
</head>
<body>

  <header>
    <h1>👤 Votre Profil </h1>
  </header>

  <div class="container">

    <!-- Info Profil -->
    <div class="profile">
      <img src="<?php echo htmlspecialchars($etudiant['photo'] ?? 'https://via.placeholder.com/120'); ?>" alt="Photo de profil">
      <div class="profile-info">
        <h2><?php echo htmlspecialchars($etudiant['nom']); ?></h2>
        <p>Numéro : <?php echo htmlspecialchars($etudiant['numero']); ?></p>
        <p>Email : <?php echo htmlspecialchars($etudiant['email']); ?></p>
        <a href="edit_profil_etudiant.php"><button class="edit-btn">Modifier le profil</button></a>
      </div>
    </div>

    <!-- Mini CV -->
    <div class="section">
      <h3>🎓 Mini CV</h3>
      <div class="cv">
        <ul>
          <li><?php echo htmlspecialchars($etudiant['niveau'] . ' - ' . $etudiant['filiere'] . ' - ' . $etudiant['universite']); ?></li>
          <li>Projet PFA : ...</li>
          <li>Projet PFE : ...</li>
        </ul>
      </div>
    </div>

    <!-- Liste des publications -->
    <div class="section">
      <h3>📝 Mes publications</h3>
      <div class="posts">
        <?php while ($proj = $result_proj->fetch_assoc()) { ?>
          <div class="post">
            <h4><?php echo htmlspecialchars($proj['titre']); ?></h4>
            <p>Publié le <?php echo htmlspecialchars($proj['date_pub'] ?? ''); ?></p>
            <a href="supprimer_projet.php?id=<?php echo $proj['id']; ?>">
              <button class="delete-btn">Supprimer</button>
            </a>
          </div>
        <?php } ?>
      </div>
    </div>

  </div>

</body>
</html>

<?php
$conn->close();
?>