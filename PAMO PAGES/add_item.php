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

    $actual_quantity = $quantity - $damage;
    $new_delivery = $quantity;
    $beginning_quantity = $quantity;
    $sold_quantity = 0;
    $status = ($actual_quantity <= 0) ? 'Out of Stock' : (($actual_quantity <= 10) ? 'Low Stock' : 'In Stock');

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

        if (!move_uploaded_file($imageTmpPath, $imagePath)) {
            throw new Exception('Error moving uploaded file');
        }
    } else {
        throw new Exception('Error uploading image');
    }


    $sql = "INSERT INTO inventory (
        item_code, category, item_name, sizes, price, 
        actual_quantity, new_delivery, beginning_quantity, 
        damage, sold_quantity, status, image_path
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception('Error preparing statement: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ssssdiiiiiss",
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
        $uniqueName
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error executing statement: ' . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);

    $description = "New item added: {$item_name} ({$item_code})";
    $sql = "INSERT INTO activities (action_type, description, item_code) VALUES ('new_item', ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $description, $item_code);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

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