<?php
header('Content-Type: application/json');

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'tambola_game';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed.']);
    exit;
}

// Check if game_id is provided in POST request
$game_id = isset($_POST['game_id']) ? (int)$_POST['game_id'] : (isset($_GET['game_id']) ? (int)$_GET['game_id'] : 0);

if ($game_id > 0) {
    // Fetch game details for the specified game_id
    $query = "SELECT started_at, status FROM games WHERE id = $game_id LIMIT 1";
} else {
    // Fetch the latest game if no game_id is provided
    $query = "SELECT started_at, status FROM games ORDER BY id DESC LIMIT 1";
}

$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Convert start time to IST if needed
    $startTime = new DateTime($row['started_at'], new DateTimeZone('UTC'));

    echo json_encode([
        'start_time' => date('Y-m-d H:i:s', strtotime($row['started_at'])),
        'status' => $row['status']  // Directly return the status
    ]);
} else {
    echo json_encode(['error' => 'No games found.']);
}

$conn->close();
