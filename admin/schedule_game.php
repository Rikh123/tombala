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

// Check if scheduled_time and ticket_count are provided in POST request
if (isset($_POST['scheduled_time'], $_POST['ticket_count']) && !empty($_POST['scheduled_time'])) {
    $scheduled_time = $_POST['scheduled_time'];
    $ticket_count = (int)$_POST['ticket_count'];
    $status = 'Scheduled'; // Default status for new games

    // Insert the new game into the database
    $stmt = $conn->prepare("INSERT INTO games (started_at, status, ticket_count) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $scheduled_time, $status, $ticket_count);

    if ($stmt->execute()) {
        $gameId = $stmt->insert_id;  // Get the inserted game ID
        $stmt->close();

        // Generate tickets immediately after scheduling the game
        if (generateTickets($gameId, $ticket_count, $conn)) {
            echo json_encode(['success' => true, 'message' => 'Game scheduled and tickets generated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Game scheduled, but failed to generate tickets.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to schedule game.']);
        $stmt->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing scheduled time or ticket count.']);
}

$conn->close();


// Generate a random Tambola ticket
function generateTicket() {
    $ticket = array_fill(0, 3, array_fill(0, 9, 0));
    $columns = [];
    for ($col = 0; $col < 9; $col++) {
        $start = $col * 10 + 1;
        $end = $col == 8 ? 99 : $start + 9;
        $columns[$col] = range($start, $end);
        shuffle($columns[$col]);
    }
    foreach ($ticket as &$row) {
        $filledPositions = array_rand($row, 5);
        foreach ($filledPositions as $pos) {
            $row[$pos] = array_shift($columns[$pos]);
        }
    }
    return $ticket;
}

// Save ticket to the database with a blank player_name
function saveTicket($gameId, $ticket, $conn) {
    $ticketJson = json_encode($ticket);
    $stmt = $conn->prepare("INSERT INTO tickets (game_id, player_name, ticket) VALUES (?, '', ?)");
    $stmt->bind_param("is", $gameId, $ticketJson);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Generate multiple tickets for a game
function generateTickets($gameId, $ticketCount, $conn) {
    $success = true;
    for ($i = 0; $i < $ticketCount; $i++) {
        $ticket = generateTicket();
        if (!saveTicket($gameId, $ticket, $conn)) {
            $success = false;
            break;
        }
    }
    return $success;
}
