<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'etudiant') {
    header("Location: login.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "edulink");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Accueil Étudiant</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --bg-color: #f5f6fa;
      --text-color: #2c3e50;
      --sidebar-bg: #2c3e50;
      --nav-bg: #2c3e50;
      --card-bg: #ffffff;
      --icon-color: #f1c40f;
    }
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: var(--bg-color);
      color: var(--text-color);
      transition: background-color 0.3s, color 0.3s;
    }
    body.dark-mode {
      --bg-color: #2c3e50;
      --text-color: #ecf0f1;
      --sidebar-bg: #1a252f;
      --nav-bg: #1a252f;
      --card-bg: #34495e;
      --icon-color: #f39c12;
    }
    /* Sidebar */
    #sidebarToggle { display: none; }
    .sidebar { width: 60px; background-color: var(--sidebar-bg); height: 100vh; padding-top: 60px; position: fixed; top: 0; left: 0; transition: width 0.3s; z-index: 10; }
    .sidebar a { display: flex; align-items: center; padding: 15px; color: white; text-decoration: none; }
    .sidebar a i { margin-right: 10px; font-size: 18px; min-width: 24px; text-align: center; }
    .link-text { display: none; }
    #sidebarToggle:checked ~ .sidebar { width: 220px; }
    #sidebarToggle:checked ~ .sidebar .link-text { display: inline; }
    /* Top Navigation */
    .topnav { position: fixed; top: 0; left: 60px; right: 0; height: 60px; background-color: var(--nav-bg); display: flex; align-items: center; justify-content: space-between; padding: 0 30px; color: white; z-index: 5; transition: left 0.3s; }
    #sidebarToggle:checked ~ .topnav { left: 220px; }
    .topnav .menu { display: flex; gap: 20px; align-items: center; }
    .topnav a { color: white; text-decoration: none; font-weight: bold; }
    .menu-btn { position: fixed; left: 10px; top: 10px; background: none; border: none; color: white; font-size: 22px; cursor: pointer; z-index: 100; }
    /* Profile Dropdown */
    .profile { position: relative; }
    .profile-icon { font-size: 32px; color: white; cursor: pointer; }
    .dropdown { display: none; position: absolute; right: 0; top: 48px; background-color: var(--card-bg); border: 1px solid #ccc; border-radius: 6px; list-style: none; padding: 10px 0; min-width: 200px; box-shadow: 0 2px 6px rgba(0,0,0,0.15); z-index: 20; }
    .dropdown.show { display: block; }
    .dropdown li { padding: 10px 20px; margin-bottom: 6px; }
    .dropdown li:last-child { margin-bottom: 0; }
    .dropdown li:hover { background-color: rgba(0,0,0,0.05); }
    .dropdown a, .dropdown button, .dropdown .theme-btn { color: var(--text-color); text-decoration: none; font-size: 15px; width: 100%; display: flex; align-items: center; gap: 8px; background: none; border: none; text-align: left; cursor: pointer; }
    .theme-btn i { color: var(--icon-color); }
    /* Main content */
    .main { margin-left: 60px; margin-top: 80px; padding: 20px; transition: margin-left 0.3s; }
    #sidebarToggle:checked ~ .main { margin-left: 220px; }
    /* Post Form Card */
    .post-form { background-color: var(--card-bg); padding: 20px; border-radius: 10px; margin: 0 auto 20px auto; max-width: 600px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    .post-form h3 { margin-bottom: 20px; color: var(--text-color); text-align: center; }
    .post-form form { display: flex; flex-direction: column; gap: 15px; align-items: center; }
    .post-form input, .post-form select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 15px; }
    .post-form button { padding: 10px 20px; background-color: var(--sidebar-bg); color: white; border-radius: 6px; border: none; cursor: pointer; }
    .post-form button:hover { background-color: #34495e; }

    .cards{display:flex;gap:20px;margin-bottom:30px;flex-wrap:wrap;}
    .card{flex:1;min-width:180px;background:white;padding:20px;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.1);display:flex;flex-direction:column;align-items:center;gap:10px;text-align:center;}
    .card i{font-size:28px;color:#2c3e50;}
    .card h4{font-size:16px;margin:0;}
    .card a{margin-top:auto;color:#2c3e50;text-decoration:none;font-weight:bold;}

    .main .content {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 20px;
    }
    /* Message Box styling */
    .message-box {
      width: 250px;
      background: var(--card-bg);
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      text-align: center;
    }
  </style>
</head>
<body>
  <!-- Sidebar Toggle -->
  <input type="checkbox" id="sidebarToggle">
  <label for="sidebarToggle" class="menu-btn">
    <i class="fas fa-bars"></i>
  </label>
  <!-- Sidebar -->
  <div class="sidebar">
    <a href="Cours.php"><i class="fas fa-book"></i><span class="link-text">Cours</span></a>
    <a href="pfa.php"><i class="fas fa-project-diagram"></i><span class="link-text">PFA</span></a>
    <a href="pfe.php"><i class="fas fa-laptop-code"></i><span class="link-text">PFE</span></a>
    <a href="calendrier.php"><i class="fas fa-calendar-alt"></i><span class="link-text">Calendrier</span></a>
  </div>
  <!-- Top Navigation -->
  <div class="topnav">
    <div class="menu">
      <a href="acceuil_etudiant.php"><i class="fas fa-home"></i> Accueil</a>
    </div>
    <div class="profile">
      <i class="fas fa-user-circle profile-icon"></i>
      <ul class="dropdown" id="profileDropdown">
        <li><a href="profil_etudiant.php"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($user['name']); ?></a></li>
        <li><button type="button" onclick="openSettings()"><i class="fas fa-cog"></i> Paramètres</button></li>
        <li>
          <button id="theme-toggler" class="theme-btn"><i class="fas fa-moon"></i> Mode Sombre</button>
        </li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
      </ul>
    </div>
  </div>
  <!-- Main Content -->
  <div class="main">
    <div class="content">
      <!-- Message Box -->
      <div class="card message-box">
        <i class="fas fa-envelope"></i>
        <h4>Messages Enseignants</h4>
        <a href="chat.php">Accéder</a>
      </div>
      <!-- Post Form -->
      <div class="post-form">
        <h3>Créer une publication</h3>
        <form action="create_post.php" method="post" id="postForm">
          <input type="text" name="titre" id="titre" placeholder="Titre de la publication" required>
          <select name="type" id="typeSelect">
            <option value="cours">Cours</option>
            <option value="pfa">PFA</option>
            <option value="pfe">PFE</option>
          </select>
          <button type="submit">Publier</button>
        </form>
      </div>
    </div>
  </div>
  <script>
    function openSettings() { alert('Ouvrir paramètres'); }
    const profileIcon = document.querySelector('.profile-icon');
    const dropdown = document.getElementById('profileDropdown');
    profileIcon.addEventListener('click', e => { e.stopPropagation(); dropdown.classList.toggle('show'); });
    document.addEventListener('click', e => { if (!e.target.closest('.profile')) dropdown.classList.remove('show'); });
    const themeBtn = document.getElementById('theme-toggler');
    themeBtn.addEventListener('click', () => {
      document.body.classList.toggle('dark-mode');
      themeBtn.innerHTML = document.body.classList.contains('dark-mode')
        ? '<i class="fas fa-sun"></i> Mode Clair'
        : '<i class="fas fa-moon"></i> Mode Sombre';
    });
  </script>
</body>
</html>
<?php $conn->close(); ?>
