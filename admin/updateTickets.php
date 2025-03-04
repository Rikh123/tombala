<?php
header('Content-Type: application/json');
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'tambola_game';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$tickets = $input['tickets'] ?? [];

if (!empty($tickets)) {
    foreach ($tickets as $ticket) {
        $id = (int)$ticket['id'];
        $player_name = $conn->real_escape_string($ticket['player_name']);

        // Update player name
        $conn->query("UPDATE tickets SET player_name = '$player_name' WHERE id = $id");
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$conn->close();
