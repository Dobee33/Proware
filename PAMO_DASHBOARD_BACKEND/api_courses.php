<?php
include '../Includes/connection.php';

$query = "SELECT course_name FROM course WHERE course_name IS NOT NULL AND course_name != '' ORDER BY course_name ASC";
$stmt = $conn->prepare($query);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo json_encode($courses);