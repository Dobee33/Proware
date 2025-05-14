<?php
include '../Includes/connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    // Get image path
    $stmt = $conn->prepare('SELECT image_path FROM homepage_content WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $imagePath = '../' . $row['image_path'];
        // Delete DB record
        $delStmt = $conn->prepare('DELETE FROM homepage_content WHERE id = ?');
        if ($delStmt->execute([$id])) {
            // Delete file if exists
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            echo json_encode(['success' => true]);
            exit;
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete from database.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Image not found.']);
        exit;
    }
}
echo json_encode(['success' => false, 'error' => 'Invalid request.']); 