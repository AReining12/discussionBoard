<?php
// getBoardApplicants.php
// Mingchen Ju 260864282
session_start();
header('Content-Type: application/json');

include 'db_connect.php';

// Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}
// Get the user ID from the session
$userId = $_SESSION['user_id'];
$boardId = $_GET['boardId']; // Assume the front-end has sent the boardId

// Verify if the user is an admin of the specified discussion board
$stmt = $conn->prepare('SELECT is_board_admin FROM board_users WHERE user_id = ? AND board_id = ?');
$stmt->bind_param('ii', $userId, $boardId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['is_board_admin']) {
        // The user is an admin, fetch the list of applicants
    $stmt = $conn->prepare('SELECT u.user_id, u.user, u.email FROM users u INNER JOIN board_applicants b ON u.user_id = b.user_id WHERE b.board_id = ?');
    $stmt->bind_param('i', $boardId);
    $stmt->execute();
    $applicants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Send back the list of applicants
    echo json_encode(['success' => true, 'applicants' => $applicants]);
} else {
    // Respond with an error if the user is not an admin of the board
    echo json_encode(['success' => false, 'error' => 'User is not an admin of this board']);
}

$stmt->close();
$conn->close();
?>
