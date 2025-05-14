<?php
include '../Includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section = $_POST['section'];
    $title = $_POST['title'];
    $image = $_FILES['image'];

    $targetDir = '../uploads/Homepage contents/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); // Create directory if it doesn't exist
    }
    $fileName = uniqid() . '_' . basename($image['name']);
    $targetFilePath = $targetDir . $fileName;
    $dbFilePath = 'uploads/Homepage contents/' . $fileName; // Save relative to project root

    $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (in_array($imageFileType, $allowedTypes)) {
        if (move_uploaded_file($image['tmp_name'], $targetFilePath)) {
            $stmt = $conn->prepare("INSERT INTO homepage_content (section, image_path, title, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            $stmt->execute([$section, $dbFilePath, $title]);
            header('Location: content-edit.php?success=1');
            exit();
        } else {
            die('Failed to upload image. Check directory permissions and path.');
        }
    } else {
        echo 'Invalid file type.';
    }
}
?> 