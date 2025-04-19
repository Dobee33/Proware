<?php
session_start();
require_once '../Includes/connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['item_id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$item_id = $_POST['item_id'];
$user_id = $_SESSION['user_id'];

try {
    // Prepare and execute the delete query by ID for precise removal
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND id = ?");
    $result = $stmt->execute([$user_id, $item_id]);

    if ($result && $stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Item not found in cart']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?> 