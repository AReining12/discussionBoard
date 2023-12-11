<?php
// joinCourse.php
// Mingchen Ju 260864282

session_start(); // Ensure the session is started
header('Content-Type: application/json'); // Set the response content type to JSON

include 'db_connect.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id']; // Get the ID of the currently logged-in user

// Retrieve boardId from the request
$data = json_decode(file_get_contents('php://input'), true);
$boardId = $data['boardId'];

// First, check if the user is already a member of the board
$stmt = $conn->prepare('SELECT * FROM board_users WHERE user_id = ? AND board_id = ?');
$stmt->bind_param('ii', $userId, $boardId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'User is already a member of this board.']);
    exit;
}

// Then, check if the user has already requested to join this board
$stmt = $conn->prepare('SELECT * FROM board_applicants WHERE user_id = ? AND board_id = ?');
$stmt->bind_param('ii', $userId, $boardId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'You have already requested to join this board.']);
    exit;
}

// Add the user to the board_applicants table
$stmt = $conn->prepare('INSERT INTO board_applicants (user_id, board_id) VALUES (?, ?)');
$stmt->bind_param('ii', $userId, $boardId);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    // Log error information to the server's error log
    error_log('Database error: ' . $stmt->error);
    echo json_encode(['success' => false, 'error' => 'An error occurred while processing your request.']);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
