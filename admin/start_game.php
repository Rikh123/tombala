<?php
header('Content-Type: application/json');

// Check if game_id is provided
if (isset($_POST['game_id']) && is_numeric($_POST['game_id'])) {
    $game_id = (int)$_POST['game_id'];
    $batFile = "run_game.bat";

    // Create the batch command
    $command = "$batFile $game_id";

    // Execute the batch file with the game ID as a parameter
    $output = [];
    $returnVar = 0;
    exec($command, $output, $returnVar);

    if ($returnVar === 0) {
        echo json_encode(['success' => true, 'message' => 'Game started successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to start game.', 'output' => $output]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing game ID.']);
}
