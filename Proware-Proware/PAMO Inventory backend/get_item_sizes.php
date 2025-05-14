<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "proware");
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed']);
    exit;
}

if (isset($_GET['item_code'])) {
    // Get the prefix from the item_code (before the dash)
    $item_code = $_GET['item_code'];
    $prefix = explode('-', $item_code)[0];
    $sql = "SELECT item_code, sizes FROM inventory WHERE item_code LIKE CONCAT(?, '-%')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $prefix);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $sizes = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $sizes[] = [
            'size' => $row['sizes'],
            'item_code' => $row['item_code']
        ];
    }
    echo json_encode(['success' => true, 'sizes' => $sizes]);
    mysqli_close($conn);
    exit;
}

// fallback for prefix param
$prefix = isset($_GET['prefix']) ? $_GET['prefix'] : '';
file_put_contents(__DIR__ . '/debug_prefix.txt', $prefix); // Debug: log the prefix
if (!$prefix) {
    echo json_encode(['success' => false, 'message' => 'No prefix provided']);
    exit;
}
$sql = "SELECT sizes FROM inventory WHERE item_code LIKE ?";
$stmt = mysqli_prepare($conn, $sql);
$like = $prefix . '%';
mysqli_stmt_bind_param($stmt, "s", $like);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$sizes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $sizes[] = $row['sizes'];
}
echo json_encode(['success' => true, 'sizes' => $sizes]);
mysqli_close($conn);
?> 