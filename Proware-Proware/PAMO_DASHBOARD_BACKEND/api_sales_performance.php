<?php
include '../Includes/connection.php';

$category = $_GET['category'] ?? '';
$course = $_GET['course'] ?? '';
$period = $_GET['period'] ?? 'daily';

// Determine group by clause based on period
if ($period === 'monthly') {
    $dateSelect = "DATE_FORMAT(s.sale_date, '%Y-%m')";
    $groupBy = "DATE_FORMAT(s.sale_date, '%Y-%m')";
} elseif ($period === 'yearly') {
    $dateSelect = "YEAR(s.sale_date)";
    $groupBy = "YEAR(s.sale_date)";
} else { // daily
    $dateSelect = "DATE(s.sale_date)";
    $groupBy = "DATE(s.sale_date)";
}

// Always select category and course (if available)
if ($category === 'Tertiary-Uniform') {
    $query = "SELECT $dateSelect as date, SUM(s.quantity) as total_sales, i.category, c.course_name as course
              FROM sales s
              LEFT JOIN inventory i ON s.item_code = i.item_code
              LEFT JOIN course_item ci ON i.id = ci.inventory_id
              LEFT JOIN course c ON ci.course_id = c.id";
} else {
    $query = "SELECT $dateSelect as date, SUM(s.quantity) as total_sales, i.category, NULL as course
              FROM sales s
              LEFT JOIN inventory i ON s.item_code = i.item_code";
}

$query .= " WHERE 1";

$params = [];

if ($category === 'Tertiary-Uniform' && !empty($course)) {
    $query .= " AND i.category = :category AND c.course_name = :course";
    $params[':category'] = $category;
    $params[':course'] = $course;
} else if ($category) {
    $query .= " AND i.category = :category";
    $params[':category'] = $category;
}

$query .= " GROUP BY $groupBy, i.category, course ORDER BY date ASC";

$stmt = $conn->prepare($query);
$stmt->execute($params);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($data);