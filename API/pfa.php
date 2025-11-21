<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>PFA</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f5f6fa;
      margin: 0;
      padding: 0;
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
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .section {
      margin-bottom: 40px;
    }
    h2 {
      color: #2c3e50;
      margin-bottom: 10px;
    }
    input, textarea, button {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      margin-bottom: 20px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    button {
      background-color: #2c3e50;
      color: white;
      cursor: pointer;
    }
    button:hover {
      background-color: #34495e;
    }
    .project {
      border: 1px solid #ccc;
      border-radius: 6px;
      padding: 15px;
      margin-bottom: 15px;
    }
    .project h4 {
      margin-bottom: 5px;
      color: #34495e;
    }
    .project p {
      margin: 0;
      color: #555;
    }
    .message {
      margin-bottom: 20px;
      padding: 10px;
      border-radius: 6px;
      font-weight: bold;
      text-align: center;
    }
    .success {
      background-color: #d4edda;
      color: #155724;
    }
    .error {
      background-color: #f8d7da;
      color: #721c24;
    }
  </style>
</head>
<body>

  <header>
    <h1>📁 Projets PFA</h1>
  </header>

  <div class="container">

    <!-- Message -->
    <div id="message"></div>

    <!-- Formulaire d'ajout -->
    <div class="section">
      <h2>➕ Ajouter un projet PFA</h2>
      <form id="pfaForm" method="post" action="ajouterProjet.php">
        <input type="text" id="titrePfa" name="titre" placeholder="Titre du projet" required>
        <textarea id="descPfa" name="description" placeholder="Description du projet" rows="4" required></textarea>
        <button type="submit">Ajouter</button>
      </form>
    </div>

    <!-- Liste des projets -->
    <div class="section">
      <h2>📃 Liste des PFA</h2>
      
      <div id="pfaList">
        <?php
        $conn = new mysqli('localhost', 'root', '', 'edulink');
        if ($conn->connect_error) {
            die("Erreur: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM projet WHERE Type='PFA'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="project">';
                echo '<h4>' . htmlspecialchars($row['Titre']) . '</h4>';
                echo '<p>' . htmlspecialchars($row['Description']) . '</p>';
                echo '</div>';
            }
        } else {
            echo "<p>Aucun projet trouvé.</p>";
        }
        $conn->close();
        ?>
      </div>

    </div>

  </div>

  <script>
    const form = document.getElementById("pfaForm");
    const list = document.getElementById("pfaList");
    const messageDiv = document.getElementById("message");

    form.addEventListener("submit", function(e) {
      e.preventDefault();
      
      const titre = document.getElementById("titrePfa").value;
      const desc = document.getElementById("descPfa").value;

      const xhr = new XMLHttpRequest();
      xhr.open("POST", "ajouterProjet.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            const response = xhr.responseText.trim();
            if (response === "success") {
              // Afficher succès
              showMessage("Projet ajouté avec succès ✅", "success");

              // Ajouter le projet dans la liste
              const project = document.createElement("div");
              project.className = "project";
              project.innerHTML = `<h4>${titre}</h4><p>${desc}</p>`;
              list.prepend(project);

              form.reset();
            } else {
              // Afficher erreur
              showMessage("Projet ajouté avec succès ✅", "error");
            }
          } else {
            showMessage("Erreur serveur ❌", "error");
          }
        }
      };

      xhr.send(`titre=${encodeURIComponent(titre)}&description=${encodeURIComponent(desc)}`);
    });

    function showMessage(message, type) {
      messageDiv.innerHTML = `<div class="message ${type}">${message}</div>`;
      setTimeout(() => {
        messageDiv.innerHTML = "";
      }, 3000); // Message disparaît après 3 secondes
    }
  </script>

</body>
</html>
