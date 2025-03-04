<?php
header('Content-Type: application/json');

// Database connection settings
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'tambola_game';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed.']);
    exit;
}

// Fetch scheduled games
$sql = "SELECT id, started_at,ticket_count,status FROM games ORDER BY started_at DESC";
$result = $conn->query($sql);

$games = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $games[] = [
            'id' => $row['id'],
            'scheduled_time' => date('Y-m-d\TH:i', strtotime($row['started_at'])), // Format for datetime-local input
            'status' => $row['status'],
            'ticket_count' => $row['ticket_count']
        ];
}

    echo json_encode($games);
} else {
    echo json_encode([]);
}

$conn->close();
