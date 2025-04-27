<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "proware");
if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Connection failed']));
}
$item_code = isset($_GET['item_code']) ? mysqli_real_escape_string($conn, $_GET['item_code']) : '';
if (empty($item_code)) {
    die(json_encode(['success' => false, 'message' => 'Item code required']));
}
$sql = "SELECT sizes FROM inventory WHERE item_code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $item_code);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
if ($item && !empty($item['sizes'])) {
    $sizes = array_map('trim', explode(',', $item['sizes']));
    echo json_encode(['success' => true, 'sizes' => $sizes]);
} else {
    echo json_encode(['success' => false, 'message' => 'No sizes found']);
}
mysqli_close($conn);
?> 