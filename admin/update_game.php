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

// Check if required fields are provided
if (isset($_POST['game_id']) && is_numeric($_POST['game_id'])) {
    $game_id = (int)$_POST['game_id'];
    $scheduled_time = isset($_POST['scheduled_time']) ? $_POST['scheduled_time'] : null;
    $status = isset($_POST['status']) ? $_POST['status'] : null;
    $ticket_count = isset($_POST['ticket_count']) ? (int)$_POST['ticket_count'] : null;

    // Build the update query dynamically
    $query = "UPDATE games SET ";
    $params = [];
    $types = '';

    if ($scheduled_time) {
        $query .= "started_at = ?, ";
        $params[] = $scheduled_time;
        $types .= 's';
    }
    if ($status) {
        $query .= "status = ?, ";
        $params[] = $status;
        $types .= 's';
    }
    if ($ticket_count !== null) {
        $query .= "ticket_count = ?, ";
        $params[] = $ticket_count;
        $types .= 'i';
    }

    // Remove the last comma and add WHERE clause
    $query = rtrim($query, ", ") . " WHERE id = ?";
    $params[] = $game_id;
    $types .= 'i';

    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Game updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update game.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare update statement.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing game ID.']);
}

$conn->close();
