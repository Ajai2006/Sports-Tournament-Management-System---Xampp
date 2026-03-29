<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

$tid = isset($_GET['tournament_id']) ? (int)$_GET['tournament_id'] : 0;
if (!$tid) { echo '[]'; exit; }

$teams = $conn->query("
    SELECT t.id, t.name FROM teams t
    JOIN tournament_teams tt ON t.id = tt.team_id
    WHERE tt.tournament_id = $tid ORDER BY t.name
");

$result = [];
while ($t = $teams->fetch_assoc()) $result[] = ['id' => $t['id'], 'name' => $t['name']];
echo json_encode($result);
