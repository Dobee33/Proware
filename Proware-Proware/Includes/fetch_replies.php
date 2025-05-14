<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}
require_once '../Includes/connection.php';
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, question, reply, submitted_at, student_read,
    CASE WHEN reply IS NOT NULL THEN submitted_at ELSE NULL END AS replied_at
    FROM inquiries WHERE user_id = :user_id AND reply IS NOT NULL ORDER BY submitted_at DESC");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Format replied_at as readable date
$unread_ids = [];
foreach ($rows as &$row) {
    $row['replied_at'] = $row['replied_at'] ? date('M d, Y H:i', strtotime($row['replied_at'])) : '';
    if ($row['student_read'] == 0) $unread_ids[] = $row['id'];
}
echo json_encode(['messages' => $rows, 'unread_ids' => $unread_ids]); 