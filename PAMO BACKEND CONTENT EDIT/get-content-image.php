<?php
include '../Includes/connection.php';
header('Content-Type: application/json');
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare('SELECT title, image_path FROM homepage_content WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        echo json_encode(['success' => true, 'title' => $row['title'], 'image_path' => $row['image_path']]);
        exit;
    } else {
        echo json_encode(['success' => false, 'error' => 'Image not found.']);
        exit;
    }
}
echo json_encode(['success' => false, 'error' => 'Invalid request.']); 