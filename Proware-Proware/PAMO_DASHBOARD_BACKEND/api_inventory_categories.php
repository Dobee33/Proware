<?php
include '../Includes/connection.php';

$query = "SELECT DISTINCT category FROM inventory WHERE category IS NOT NULL AND category != '' ORDER BY category ASC";
$stmt = $conn->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo json_encode($categories); 