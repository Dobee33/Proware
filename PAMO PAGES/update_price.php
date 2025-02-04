<?php
header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "root", "", "proware");

if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

if (isset($_POST['item_code']) && isset($_POST['price'])) {
    $item_code = $_POST['item_code'];
    $price = floatval($_POST['price']);

    $sql = "UPDATE inventory SET price = ? WHERE item_code = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ds", $price, $item_code);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed']);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
}

mysqli_close($conn);
?>