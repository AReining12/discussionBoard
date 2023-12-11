<?php
// approveApplicant.php
// Mingchen Ju 260864282
session_start();
header('Content-Type: application/json');

include 'db_connect.php';

$userId = $_GET['userId']; // Get the user ID from the request
$boardId = $_GET['boardId']; // Get the board ID from the request

// First, check if the user is already a member of the board
$stmt = $conn->prepare('SELECT * FROM board_users WHERE user_id = ? AND board_id = ?');
$stmt->bind_param('ii', $userId, $boardId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User is already a member of this board
    // Remove the user from the applicants table
    $stmt = $conn->prepare('DELETE FROM board_applicants WHERE user_id = ? AND board_id = ?');
    $stmt->bind_param('ii', $userId, $boardId);
    $stmt->execute();

    echo json_encode(['success' => false, 'error' => 'User is already a member of this board']);
} else {
    // Add user to the board as a member
    $stmt = $conn->prepare('INSERT INTO board_users (user_id, board_id, is_board_admin) VALUES (?, ?, 0)');
    $stmt->bind_param('ii', $userId, $boardId);

    if ($stmt->execute()) {
        // Remove the user from the applicants table
        $stmt = $conn->prepare('DELETE FROM board_applicants WHERE user_id = ? AND board_id = ?');
        $stmt->bind_param('ii', $userId, $boardId);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
}

$stmt->close();
$conn->close();
?>
