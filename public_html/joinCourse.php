<?php
// joinCourse.php

session_start(); // 确保会话已经启动
header('Content-Type: application/json'); // 设置响应的内容类型为 JSON

include 'db_connect.php'; // 包含数据库连接

// 检查用户是否登录
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id']; // 获取当前登录用户的 ID

// 从请求中获取 boardId
$data = json_decode(file_get_contents('php://input'), true);
$boardId = $data['boardId'];

// 检查用户是否已经申请加入该讨论板
$stmt = $conn->prepare('SELECT * FROM board_applicants WHERE user_id = ? AND board_id = ?');
$stmt->bind_param('ii', $userId, $boardId); // 'ii' 表示两个参数都是整型
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'You have already requested to join this board.']);
    exit;
}

// 将用户添加到 board_applicants 表中
$stmt = $conn->prepare('INSERT INTO board_applicants (user_id, board_id) VALUES (?, ?)');
$stmt->bind_param('ii', $userId, $boardId);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    // 记录错误信息到服务器的错误日志
    error_log('Database error: ' . $stmt->error);
    echo json_encode(['success' => false, 'error' => 'An error occurred while processing your request.']);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
