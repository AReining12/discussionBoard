<?php
// getBoardApplicants.php
// Mingchen Ju 260864282
session_start();
header('Content-Type: application/json');

include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$boardId = $_GET['boardId']; // 假设前端发送了 boardId

// 验证用户是否为该讨论板的管理员
$stmt = $conn->prepare('SELECT is_board_admin FROM board_users WHERE user_id = ? AND board_id = ?');
$stmt->bind_param('ii', $userId, $boardId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['is_board_admin']) {
    // 用户是管理员，获取申请者列表
    $stmt = $conn->prepare('SELECT u.user_id, u.user, u.email FROM users u INNER JOIN board_applicants b ON u.user_id = b.user_id WHERE b.board_id = ?');
    $stmt->bind_param('i', $boardId);
    $stmt->execute();
    $applicants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode(['success' => true, 'applicants' => $applicants]);
} else {
    echo json_encode(['success' => false, 'error' => 'User is not an admin of this board']);
}

$stmt->close();
$conn->close();
?>
