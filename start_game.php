<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'tambola_game';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Check if 'start_time' is set in POST request
if (isset($_POST['start_time'])) {
    $startTime = $_POST['start_time'];
    $status = "scheduled";
    // Prepare SQL statement to insert the selected start time
   // Prepare SQL statement to insert the selected start time and status
$stmt = $conn->prepare("INSERT INTO games (started_at, status) VALUES (?, ?)");
$stmt->bind_param("ss", $startTime, $status);


    if ($stmt->execute()) {
        echo $conn->insert_id;  // Return the game ID
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Start time not provided!";
}

$conn->close();
