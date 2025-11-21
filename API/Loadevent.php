<?php 
$mysqli = new mysqli('localhost', 'root', '', 'Edulink');
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(array("error" => "Connection failed"));
    exit;
}

$sql = "SELECT EventID, Lieu, Date, Activite, Description FROM evenement ORDER BY Date ASC";
$res = $mysqli->query($sql);

if (!$res) {
    echo json_encode(array("error" => $mysqli->error));
    exit;
}

$events = array();
while ($row = $res->fetch_assoc()) {
    $events[] = array(
        'title' => $row['Activite'] . " @ " . $row['Lieu'],
        'start' => $row['Date'],
        'description' => $row['Description']
    );
}
echo json_encode($events);
?>
