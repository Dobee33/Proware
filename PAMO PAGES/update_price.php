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
        $description = "Price updated for item {$item_code} to ₱{$price}";
        mysqli_stmt_close($stmt);

        $sql = "INSERT INTO activities (action_type, description, item_code) VALUES ('price_update', ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $description, $item_code);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo json_encode(['success' => true]);
    } else {
        mysqli_stmt_close($stmt);
        echo json_encode(['success' => false, 'message' => 'Database update failed']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
}

mysqli_close($conn);
?>