<?php
try {
    $conn = mysqli_connect("localhost", "root", "", "proware");

    if (!$conn) {
        throw new Exception('Connection failed: ' . mysqli_connect_error());
    }

    $item_code = mysqli_real_escape_string($conn, $_POST['newItemCode']);
    $category = mysqli_real_escape_string($conn, $_POST['newCategory']);
    $item_name = mysqli_real_escape_string($conn, $_POST['newItemName']);
    $sizes = mysqli_real_escape_string($conn, $_POST['newSize']);
    $price = floatval($_POST['newItemPrice']);
    $quantity = intval($_POST['newItemQuantity']);
    $damage = intval($_POST['newItemDamage']);

    // For new items, beginning_quantity starts at 0 since it's a new entry
    $beginning_quantity = 0;
    // New delivery is the initial quantity being added
    $new_delivery = $quantity;
    // Actual quantity is beginning_quantity + new_delivery - damage
    $actual_quantity = $beginning_quantity + $new_delivery - $damage;
    $sold_quantity = 0;
    $status = ($actual_quantity <= 0) ? 'Out of Stock' : (($actual_quantity <= 20) ? 'Low Stock' : 'In Stock');

    if (isset($_FILES['newImage']) && $_FILES['newImage']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['newImage']['tmp_name'];
        $imageName = $_FILES['newImage']['name'];
        $imageSize = $_FILES['newImage']['size'];
        $imageType = $_FILES['newImage']['type'];

        $uploadDir = '../uploads/itemlist/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Get file extension
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);

        // Generate unique filename
        $uniqueName = uniqid('img_', true) . '.' . $imageExtension;
        $imagePath = $uploadDir . $uniqueName;
        $dbFilePath = 'uploads/itemlist/' . $uniqueName;

        if (!move_uploaded_file($imageTmpPath, $imagePath)) {
            throw new Exception('Error moving uploaded file');
        }
    } else {
        throw new Exception('Error uploading image');
    }

    // Check if item_code already exists
    $check_sql = "SELECT COUNT(*) FROM inventory WHERE item_code = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "s", $item_code);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_bind_result($check_stmt, $item_code_count);
    mysqli_stmt_fetch($check_stmt);
    mysqli_stmt_close($check_stmt);
    if ($item_code_count > 0) {
        throw new Exception('Item code already exists. Please enter a unique item code.');
    }

    // Calculate shared_in_courses
    $course_ids = isset($_POST['course_id']) ? (is_array($_POST['course_id']) ? $_POST['course_id'] : [$_POST['course_id']]) : [];
    $RTW = (count($course_ids) > 1) ? 1 : 0;

    // Debug: Log course_ids
    // error_log('Course IDs received: ' . print_r($course_ids, true));

    $sql = "INSERT INTO inventory (
        item_code, category, item_name, sizes, price, 
        actual_quantity, new_delivery, beginning_quantity, 
        damage, sold_quantity, status, image_path, RTW, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception('Error preparing statement: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ssssdiiiiissi",
        $item_code,
        $category,
        $item_name,
        $sizes,
        $price,
        $actual_quantity,
        $new_delivery,
        $beginning_quantity,
        $damage,
        $sold_quantity,
        $status,
        $dbFilePath,
        $RTW
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error executing statement: ' . mysqli_stmt_error($stmt));
    }
    $new_inventory_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // Log the activity in the audit trail
    $delivery_order = mysqli_real_escape_string($conn, $_POST['deliveryOrderNumber']);
    $description = "New item added: {$item_name} ({$item_code}) - Delivery Order #: {$delivery_order}, Initial delivery: {$new_delivery}, Damage: {$damage}, Actual quantity: {$actual_quantity}";
    $sql = "INSERT INTO activities (action_type, description, item_code, timestamp) VALUES ('New Item', ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $description, $item_code);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // After inventory insert, link to course_item if course_id is provided
    if (!empty($_POST['course_id'])) {
        $course_ids = is_array($_POST['course_id']) ? $_POST['course_id'] : [$_POST['course_id']];
        foreach ($course_ids as $course_id) {
            $course_id = intval($course_id);
            // error_log('Processing course_id: ' . $course_id); // Debug each course_id
            if ($course_id > 0) {
                $sql = "INSERT INTO course_item (inventory_id, course_id) VALUES (?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ii", $new_inventory_id, $course_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    }

    mysqli_close($conn);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    error_log("Error in add_item.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>