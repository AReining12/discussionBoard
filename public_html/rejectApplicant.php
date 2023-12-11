<?php
// rejectApplicant.php
// Mingchen Ju 260864282

session_start();
header('Content-Type: application/json');

include 'db_connect.php';

// Ensure user is logged in and necessary parameters are provided
if (!isset($_SESSION['user_id']) || !isset($_GET['userId']) || !isset($_GET['boardId'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters or not logged in']);
    exit;
}

$userId = $_GET['userId'];
$boardId = $_GET['boardId'];

// Check if the logged-in user is an admin of the board
$stmt = $conn->prepare('SELECT is_board_admin FROM board_users WHERE user_id = ? AND board_id = ?');
$stmt->bind_param('ii', $_SESSION['user_id'], $boardId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row || !$row['is_board_admin']) {
    echo json_encode(['success' => false, 'error' => 'User is not an admin of this board']);
    exit;
}

// Reject the applicant from the board
$stmt = $conn->prepare('DELETE FROM board_applicants WHERE user_id = ? AND board_id = ?');
$stmt->bind_param('ii', $userId, $boardId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to reject applicant']);
}

$stmt->close();
$conn->close();
?>
