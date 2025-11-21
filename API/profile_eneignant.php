<?php
session_start();
if (!isset($_SESSION['enseignant_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "edulink");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$enseignant_id = $_SESSION['enseignant_id'];
// Récupérer les infos de l'enseignant
$sql = "SELECT * FROM enseignants WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $enseignant_id);
$stmt->execute();
$result = $stmt->get_result();
$ens = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Profil Enseignant</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* CSS inchangé */
    *{margin:0;padding:0;box-sizing:border-box;}
    body{font-family:'Segoe UI',sans-serif;background:#f5f6fa;color:#2c3e50;transition:background .3s,color .3s;}
    #sidebarToggle{display:none;}
    .sidebar{width:60px;background:#2c3e50;height:100vh;padding-top:60px;position:fixed;top:0;left:0;transition:width .3s;z-index:10;}
    .sidebar a{display:flex;align-items:center;padding:15px;color:white;text-decoration:none;gap:10px;}
    .sidebar a i{font-size:18px;min-width:24px;text-align:center;}
    .link-text{display:none;}
    #sidebarToggle:checked~.sidebar{width:260px;}
    #sidebarToggle:checked~.sidebar .link-text{display:inline;}
    .menu-btn{position:fixed;left:10px;top:10px;background:none;border:none;color:white;font-size:22px;cursor:pointer;z-index:100;}
    .topnav{position:fixed;top:0;left:60px;right:0;height:60px;background:#2c3e50;display:flex;align-items:center;justify-content:space-between;padding:0 30px;color:white;z-index:5;transition:left .3s;}
    #sidebarToggle:checked~.topnav{left:260px;}
    .topnav .menu a{color:white;text-decoration:none;font-weight:bold;display:flex;align-items:center;gap:8px; }
    .profile{position:relative;}
    .profile-icon{font-size:32px;color:white;cursor:pointer;}
    .dropdown{display:none;position:absolute;right:0;top:48px;background:white;border:1px solid #ccc;border-radius:6px;list-style:none;padding:10px 0;min-width:200px;box-shadow:0 2px 6px rgba(0,0,0,0.15);}
    .dropdown.show{display:block;}
    .dropdown li{padding:10px 20px;}
    .dropdown li:hover{background:rgba(0,0,0,0.05);}
    .dropdown a{color:#2c3e50;text-decoration:none;font-size:15px;display:flex;align-items:center;gap:8px;}
    .main{margin-left:60px;margin-top:80px;padding:20px;transition:margin-left .3s;}
    #sidebarToggle:checked~.main{margin-left:260px;}
    .profile-header{background:white;padding:20px;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.1);display:flex;align-items:center;gap:20px;}
    .avatar{font-size:60px;color:#2c3e50;background:#ecf0f1;width:60px;height:60px;display:flex;align-items:center;justify-content:center;border-radius:50%;}
    .info{display:flex;flex-direction:column;gap:6px;}
    .info p{font-size:14px;color:#666;}
    .edit-btn{margin-left:auto;padding:8px 16px;background:#2c3e50;color:white;border:none;border-radius:6px;cursor:pointer;}
    .edit-btn:hover{background:#34495e;}
    .section{margin-top:30px;}
    .section h3{margin-bottom:15px;color:#2c3e50;}
    .resource-list{list-style:none;padding:0;}
    .resource-list li{background:white;padding:15px;border-radius:6px;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:10px;display:flex;justify-content:space-between;align-items:center;}
    .resource-list a{color:#2c3e50;text-decoration:none;font-weight:bold;}
  </style>
</head>
<body>
  <input type="checkbox" id="sidebarToggle">
  <label for="sidebarToggle" class="menu-btn"><i class="fas fa-bars"></i></label>
  <div class="sidebar">
    <a href="acceuil_enseignant.php"><i class="fas fa-home"></i><span class="link-text">Accueil</span></a>
    <a href="series.php"><i class="fas fa-list"></i><span class="link-text">Séries</span></a>
    <a href="exercices.php"><i class="fas fa-pencil-alt"></i><span class="link-text">Exercices</span></a>
  </div>
  <div class="topnav">
    <div class="menu">
      <a href="acceuil_enseignant.php"><i class="fas fa-home"></i> Accueil</a>
    </div>
    <div class="profile">
      <i class="fas fa-user-circle profile-icon"></i>
      <ul class="dropdown" id="profileDropdown">
        <li><a href="profil_enseignant.php"><i class="fas fa-user"></i> Mon Profil</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
      </ul>
    </div>
  </div>
  <div class="main">
    <div class="profile-header">
      <div class="avatar"><i class="fas fa-chalkboard-teacher"></i></div>
      <div class="info">
        <p>Email : <?= htmlspecialchars($ens['email']); ?></p>
        <p>Département : <?= htmlspecialchars($ens['departement']); ?></p>
      </div>
      <a href="modifier_profil_enseignant.php"><button class="edit-btn">Modifier</button></a>
    </div>
    <div class="section">
      <h3>Mes Séries de Cours</h3>
      <ul class="resource-list">
        <?php
        $res1 = $conn->query("SELECT * FROM series_cours WHERE enseignant_id = $enseignant_id");
        while($row = $res1->fetch_assoc()){
            echo '<li>'.htmlspecialchars($row['titre']).' <a href="'.htmlspecialchars($row['fichier']).'">Télécharger</a></li>';
        }
        ?>
      </ul>
    </div>
    <div class="section">
      <h3>Mes Séries d'Exercices</h3>
      <ul class="resource-list">
        <?php
        $res2 = $conn->query("SELECT * FROM series_exercices WHERE enseignant_id = $enseignant_id");
        while($row = $res2->fetch_assoc()){
            echo '<li>'.htmlspecialchars($row['titre']).' <a href="'.htmlspecialchars($row['fichier']).'">Télécharger</a></li>';
        }
        ?>
      </ul>
    </div>
    <div class="section">
      <h3>Changer le mot de passe</h3>
      <form action="update_password.php" method="post" style="display:flex;flex-direction:column;gap:10px;max-width:400px;">
        <input type="password" name="old_password" placeholder="Ancien mot de passe" required style="padding:10px;border:1px solid #ccc;border-radius:6px;">
        <input type="password" name="new_password" placeholder="Nouveau mot de passe" required style="padding:10px;border:1px solid #ccc;border-radius:6px;">
        <button type="submit" style="width:120px;padding:10px;background:#2c3e50;color:white;border:none;border-radius:6px;cursor:pointer;">Valider</button>
      </form>
    </div>
  </div>
  <script>
    const icon=document.querySelector('.profile-icon');
    const dd=document.getElementById('profileDropdown');
    icon.addEventListener('click',e=>{e.stopPropagation();dd.classList.toggle('show');});
    document.addEventListener('click',e=>{if(!e.target.closest('.profile'))dd.classList.remove('show');});
  </script>
</body>
</html>
<?php $conn->close(); ?>
