<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { echo json_encode(['success'=>false]); exit; }
require_once 'connection.php';
$stmt = $conn->prepare("UPDATE inquiries SET student_read = 1 WHERE user_id = :uid AND reply IS NOT NULL AND student_read = 0");
$stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
echo json_encode(['success'=>true]); 