<?php
// listAdminBoards.php
// Mingchen Ju 260864282
session_start();
header('Content-Type: application/json');

include 'db_connect.php';

// Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    // Respond with an error if no user is logged in
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}
// Get the user ID from the session
$userId = $_SESSION['user_id'];

// Prepare a SQL statement to select all boards where the user is an admin
$stmt = $conn->prepare('SELECT b.board_id, b.board_name FROM boards b INNER JOIN board_users bu ON b.board_id = bu.board_id WHERE bu.user_id = ? AND bu.is_board_admin = 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
// Fetch the result as an associative array
$boards = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Respond with the list of boards where the user is an admin
echo json_encode(['success' => true, 'boards' => $boards]);

$stmt->close();
$conn->close();
?>
