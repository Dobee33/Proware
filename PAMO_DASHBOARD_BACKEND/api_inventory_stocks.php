<?php
include '../Includes/connection.php';

$category = $_GET['category'] ?? '';

$query = "SELECT category, SUM(actual_quantity) as quantity FROM inventory WHERE 1";
$params = [];

if ($category) {
    $query .= " AND category = :category";
    $params[':category'] = $category;
}
$query .= " GROUP BY category";

$stmt = $conn->prepare($query);
$stmt->execute($params);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($data);