<?php
// quitCourse.php

session_start();
header('Content-Type: application/json');

include 'db_connect.php'; // Include the database connection

// Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$boardId = $data['boardId'];
$userId = $_SESSION['user_id']; // Get the user ID from the session

// Check if the user is a member of the course and not an admin
$stmt = $conn->prepare('SELECT * FROM board_users WHERE board_id = ? AND user_id = ? AND is_board_admin = 0');
$stmt->bind_param('ii', $boardId, $userId);
$stmt->execute();
$isMember = $stmt->get_result()->fetch_assoc();

if ($isMember) {
    // If the user is a member and not an admin, remove the user from the course
    $deleteStmt = $conn->prepare('DELETE FROM board_users WHERE board_id = ? AND user_id = ?');
    $deleteStmt->bind_param('ii', $boardId, $userId);
    $deleteStmt->execute();

    echo json_encode(['success' => true, 'message' => 'Successfully quit the course']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: Unauthorized or not a member of the course']);
}

$stmt->close();
$conn->close();
?>
