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

// Default parameters
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = isset($_GET['offset']) && is_numeric($_GET['offset']) ? (int)$_GET['offset'] : 0;

if (isset($_GET['game_id']) && is_numeric($_GET['game_id'])) {
    $game_id = (int)$_GET['game_id'];
    
    // Check for range parameter
    if (isset($_GET['range'])) {
        $range = $_GET['range'];
        if (preg_match('/^\d+-\d+$/', $range)) {  // e.g., 1-50
            list($start, $end) = explode('-', $range);
            $start = (int)$start;
            $end = (int)$end;
            $query = "SELECT id, player_name, ticket FROM tickets WHERE game_id = $game_id AND id BETWEEN $start AND $end ORDER BY id ASC";
        } elseif (is_numeric($range)) {  // e.g., 1
            $ticket_id = (int)$range;
            $query = "SELECT id, player_name, ticket FROM tickets WHERE game_id = $game_id AND id = $ticket_id ORDER BY id ASC";
        } else {
            echo json_encode(['error' => 'Invalid range format.']);
            exit;
        }
    } else {
        // Default: Load 10 tickets only
        $query = "SELECT id, player_name, ticket FROM tickets WHERE game_id = $game_id ORDER BY id ASC LIMIT $limit OFFSET $offset";
    }
    
    $result = $conn->query($query);
    $tickets = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($tickets);
} else {
    echo json_encode(['error' => 'Invalid game ID.']);
}

$conn->close();
