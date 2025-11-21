<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des Événements</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f5f6fa; color: #2c3e50; padding: 20px; }
    h1, h2 { text-align: center; margin-bottom: 20px; }
    .message { max-width: 600px; margin: 10px auto; padding: 10px; border-radius: 4px; }
    .success { background: #d4edda; color: #155724; }
    .error { background: #f8d7da; color: #721c24; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
    th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
    th { background: #2c3e50; color: white; }
    tr:hover { background: #ececec; }
    form { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }
    form div { margin-bottom: 15px; }
    label { display: block; margin-bottom: 6px; font-weight: bold; }
    input, textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
    button { padding: 10px 20px; background: #2c3e50; color: white; border: none; border-radius: 4px; cursor: pointer; width: 100%; }
    button:hover { background: #34495e; }
  </style>
</head>
<body>
<?php
// events.php

// 1. Connexion à la base (EasyPHP par défaut)
$mysqli = new mysqli('localhost', 'root', '', 'Edulink');
if ($mysqli->connect_errno) {
    die('<p class="message error">'
      . 'Erreur de connexion : '
      . htmlspecialchars($mysqli->connect_error)
      . '</p>');
}

// 2. Charger la liste des entreprises pour le <select>
$entreprises = array();
$res = $mysqli->query("SELECT EntrepriseID, NomEntreprise FROM entreprise ORDER BY NomEntreprise");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $entreprises[] = $row;
    }
    $res->close();
} else {
    die('<p class="message error">'
      . 'Impossible de charger les entreprises : '
      . htmlspecialchars($mysqli->error)
      . '</p>');
}

$message      = '';
$entrepriseID = null;
$events       = null;

// 3. Traitement du formulaire d’ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère directement l’ID choisi
    $entrepriseID = isset($_POST['EntrepriseID']) ? (int)$_POST['EntrepriseID'] : 0;
    $lieu         = $_POST['Lieu'];
    $date         = $_POST['Date'];
    $activite     = $_POST['Activite'];
    $description  = $_POST['Description'];

    if ($entrepriseID > 0) {
        // Prépare l’insertion en échappant bien les backticks sur Date/Description
        $sqlInsert = "
          INSERT INTO `evenement`
            (`Lieu`, `Date`, `Activite`, `Description`, `EtudiantID`, `EntrepriseID`)
          VALUES (?, ?, ?, ?, 0, ?)
        ";
        $stmt = $mysqli->prepare($sqlInsert);
        if ($stmt === false) {
            die('<p class="message error">'
              . 'Erreur de prepare INSERT : '
              . htmlspecialchars($mysqli->error)
              . '</p>');
        }

        $stmt->bind_param('ssssi', $lieu, $date, $activite, $description, $entrepriseID);
        if ($stmt->execute()) {
            $message = '<p class="message success">'
                     . 'Événement ajouté avec succès.'
                     . '</p>';
        } else {
            $message = '<p class="message error">'
                     . 'Erreur à l’insertion : '
                     . htmlspecialchars($stmt->error)
                     . '</p>';
        }
        $stmt->close();
    } else {
        $message = '<p class="message error">'
                 . 'Veuillez sélectionner une entreprise valide.'
                 . '</p>';
    }
}

// 4. Si on a un EntrepriseID (POST ou re-sélection), récupérer ses événements
if (!empty($entrepriseID)) {
    $sqlEvents = "
      SELECT `EventID`, `Lieu`, `Date`, `Activite`, `Description`
        FROM `evenement`
       WHERE `EntrepriseID` = ?
       ORDER BY `Date` DESC
    ";
    $stmt = $mysqli->prepare($sqlEvents);
    if ($stmt === false) {
        die('<p class="message error">'
          . 'Erreur de prepare SELECT : '
          . htmlspecialchars($mysqli->error)
          . '</p>');
    }
    $stmt->bind_param('i', $entrepriseID);
    $stmt->execute();
    $events = array();
    $stmt->bind_result($eventID, $lieu, $date, $activite, $description);
    while ($stmt->fetch()) {
        $events[] = array(
            'EventID' => $eventID,
            'Lieu' => $lieu,
            'Date' => $date,
            'Activite' => $activite,
            'Description' => $description
        );
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des Événements</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* (ton CSS existant) */
  </style>
</head>
<body>

  <h1>Gestion des Événements</h1>
  <?php echo $message; ?>

  <?php if (isset($events) && $events instanceof mysqli_result && $events->num_rows > 0): ?>
  <table>
    <thead>
      <tr><th>ID</th><th>Lieu</th><th>Date</th><th>Activité</th><th>Description</th></tr>
    </thead>
    <tbody>
      <?php while ($row = $events->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['EventID']); ?></td>
        <td><?php echo htmlspecialchars($row['Lieu']); ?></td>
        <td><?php echo htmlspecialchars($row['Date']); ?></td>
        <td><?php echo htmlspecialchars($row['Activite']); ?></td>
        <td><?php echo htmlspecialchars($row['Description']); ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
<?php endif; ?>

  <!-- 6. Formulaire avec <select> d'entreprises -->
  <form method="post" action="events.php">
    <h2><i class="fas fa-calendar-plus"></i> Créer un Événement</h2>

    <div>
      <label for="EntrepriseID">Entreprise :</label>
      <select id="EntrepriseID" name="EntrepriseID" required>
        <option value="">-- Choisissez --</option>
        <?php foreach($entreprises as $ent): ?>
          <option value="<?php echo $ent['EntrepriseID']; ?>"
            <?php if ($ent['EntrepriseID']===$entrepriseID) echo 'selected'; ?>>
            <?php echo htmlspecialchars($ent['NomEntreprise']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label for="Lieu">Lieu :</label>
      <input type="text" id="Lieu" name="Lieu" required>
    </div>

    <div>
      <label for="Date">Date :</label>
      <input type="date" id="Date" name="Date" required>
    </div>

    <div>
      <label for="Activite">Activité :</label>
      <input type="text" id="Activite" name="Activite" required>
    </div>

    <div>
      <label for="Description">Description :</label>
      <textarea id="Description" name="Description" rows="3" required></textarea>
    </div>

    <button type="submit">Enregistrer</button>
  </form>

</body>
</html>