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

// Function to generate a single ticket
function generateTicket() {
    $ticket = array_fill(0, 3, array_fill(0, 9, 0));
    $columns = array_fill(0, 9, []);

    // Fill columns with numbers
    for ($col = 0; $col < 9; $col++) {
        $start = $col * 10 + 1;
        $end = $col == 8 ? 90 : $start + 9;
        $columns[$col] = range($start, $end);
        shuffle($columns[$col]);
    }

    // Fill each row with numbers
    foreach ($ticket as $rowIndex => &$row) {
        $colsToFill = array_rand($columns, 5);
        foreach ($colsToFill as $col) {
            $row[$col] = array_pop($columns[$col]);
        }
    }

    return $ticket;
}

// Check if game_id and number_of_tickets are provided
if (isset($_POST['game_id'], $_POST['number_of_tickets']) && is_numeric($_POST['game_id']) && is_numeric($_POST['number_of_tickets'])) {
    $game_id = (int)$_POST['game_id'];
    $number_of_tickets = (int)$_POST['number_of_tickets'];
    $tickets = [];

    for ($i = 0; $i < $number_of_tickets; $i++) {
        $ticket = generateTicket();
        $serializedTicket = json_encode($ticket);

        // Insert each ticket into the database
        $stmt = $conn->prepare("INSERT INTO tickets (game_id, ticket_data) VALUES (?, ?)");
        $stmt->bind_param("is", $game_id, $serializedTicket);
        if ($stmt->execute()) {
            $tickets[] = ['ticket_id' => $stmt->insert_id, 'ticket_data' => $ticket];
        }
        $stmt->close();
    }

    echo json_encode(['success' => true, 'tickets' => $tickets]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing parameters.']);
}

$conn->close();
