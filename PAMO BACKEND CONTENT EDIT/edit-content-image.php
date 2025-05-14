<?php
include '../Includes/connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['title'])) {
    $id = intval($_POST['id']);
    $title = $_POST['title'];
    $newImage = isset($_FILES['image']) ? $_FILES['image'] : null;

    // Get current image path
    $stmt = $conn->prepare('SELECT image_path FROM homepage_content WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        echo json_encode(['success' => false, 'error' => 'Image not found.']);
        exit;
    }
    $dbFilePath = $row['image_path'];
    $updateImage = false;

    if ($newImage && $newImage['tmp_name']) {
        $targetDir = '../uploads/Homepage contents/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = uniqid() . '_' . basename($newImage['name']);
        $targetFilePath = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($imageFileType, $allowedTypes)) {
            echo json_encode(['success' => false, 'error' => 'Invalid file type.']);
            exit;
        }
        if (move_uploaded_file($newImage['tmp_name'], $targetFilePath)) {
            // Delete old image
            $oldPath = '../' . $dbFilePath;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
            $dbFilePath = 'uploads/Homepage contents/' . $fileName;
            $updateImage = true;
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to upload new image.']);
            exit;
        }
    }

    if ($updateImage) {
        $stmt = $conn->prepare('UPDATE homepage_content SET title = ?, image_path = ?, updated_at = NOW() WHERE id = ?');
        $success = $stmt->execute([$title, $dbFilePath, $id]);
    } else {
        $stmt = $conn->prepare('UPDATE homepage_content SET title = ?, updated_at = NOW() WHERE id = ?');
        $success = $stmt->execute([$title, $id]);
    }
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update image.']);
    }
    exit;
}
echo json_encode(['success' => false, 'error' => 'Invalid request.']); 