<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "proware");
if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Connection failed']));
}
$role = isset($_GET['role']) ? mysqli_real_escape_string($conn, $_GET['role']) : '';
$allowed_roles = ['COLLEGE STUDENT', 'SHS', 'EMPLOYEE'];
if (!in_array($role, $allowed_roles)) {
    $role = $allowed_roles[0]; // Default to first allowed role
}
$sql = "SELECT id, first_name, last_name, id_number FROM account WHERE role_category = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $role);
$stmt->execute();
$result = $stmt->get_result();
$students = [];
while ($row = mysqli_fetch_assoc($result)) {
    $students[] = [
        'id' => $row['id'],
        'name' => $row['first_name'] . ' ' . $row['last_name'],
        'id_number' => $row['id_number']
    ];
}
echo json_encode(['success' => true, 'students' => $students]);
mysqli_close($conn);
?> 