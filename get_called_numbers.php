<?php
// get_called_numbers.php
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

// Check if game_id is provided in POST request or GET request
$game_id = isset($_POST['game_id']) ? (int)$_POST['game_id'] : (isset($_GET['game_id']) ? (int)$_GET['game_id'] : 0);

if ($game_id > 0) {
    // Fetch called numbers for the specific game ID
    $result = $conn->query("SELECT number, game_id FROM called_numbers WHERE game_id = '$game_id'");
    if ($result) {
        $calledNumbers = [];
        while ($row = $result->fetch_assoc()) {
            $calledNumbers[] = $row['number'];
        }

        echo json_encode([
            'game_id' => $game_id,
            'called_numbers' => $calledNumbers
        ]);
    } else {
        echo json_encode(['error' => 'Error fetching called numbers.']);
    }
} else {
    echo json_encode(['error' => 'Invalid or missing game ID.']);
}

$conn->close();
