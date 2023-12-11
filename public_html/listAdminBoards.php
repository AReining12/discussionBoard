<?php
// listAdminBoards.php
// Mingchen Ju 260864282
session_start();
header('Content-Type: application/json');

include 'db_connect.php';

// 检查是否有用户登录
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

// 查询用户作为管理员的所有讨论板
$stmt = $conn->prepare('SELECT b.board_id, b.board_name FROM boards b INNER JOIN board_users bu ON b.board_id = bu.board_id WHERE bu.user_id = ? AND bu.is_board_admin = 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$boards = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// 不再需要检查 'is_staff' 字段，因为我们已经通过 'is_board_admin' 字段获取了管理员身份
echo json_encode(['success' => true, 'boards' => $boards]);

$stmt->close();
$conn->close();
?>
