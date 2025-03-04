<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'tambola_game';
date_default_timezone_set('Asia/Kolkata');  // E.g., 'Asia/Kolkata' or 'UTC'

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

echo "Connected to database.\n";

// Fetch the latest scheduled game start time directly from the 'games' table
$result = $conn->query("SELECT id, started_at FROM games WHERE status = 'scheduled' ORDER BY id ASC LIMIT 1");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $startTime = strtotime($row['started_at']);
    $now = time();

echo "Current Time: " . date('H:i:s d-m-Y') . "\n";

    if ($now >= $startTime) {
        $gameId = $row['id'];
        echo "Starting game ID: $gameId\n";

        // Update game status to 'in_progress'
        $conn->query("UPDATE games SET status = 'Game In progress...' WHERE id = $gameId");

        // Generate all 99 numbers and shuffle them
        $numbers = range(1, 99);
        shuffle($numbers);

        // Call numbers randomly every 5-10 seconds
        foreach ($numbers as $num) {
            $stmt = $conn->prepare("INSERT INTO called_numbers (game_id, number) VALUES (?, ?)");
            $stmt->bind_param("ii", $gameId, $num);
            $stmt->execute();
            $stmt->close();

            echo "Called number: $num\n";  // Print called number

            sleep(rand(5, 10));  // Pause for 5-10 seconds before calling the next number
        }

        // Update game status to 'completed' after all numbers are called
        $conn->query("UPDATE games SET status = 'Game Completed' WHERE id = $gameId");
        echo "Game ID $gameId completed.\n";
    } else {
        echo "No game scheduled to start yet.\n";
    }
} else {
    echo "No scheduled games found.\n";
}
$conn->close();
