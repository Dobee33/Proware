<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => '', 'cart_count' => 0];
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                if (!isset($_SESSION['user_id'])) {
                    $response['message'] = 'Please login to add items to cart';
                    break;
                }

                $item_code = $_POST['item_code'];
                $quantity = intval($_POST['quantity']);
                $size = isset($_POST['size']) && !empty($_POST['size']) ? $_POST['size'] : null;
                $user_id = $_SESSION['user_id'];

                // Get item details from inventory including available stock
                $stmt = $conn->prepare("SELECT category, actual_quantity FROM inventory WHERE item_code = ? OR item_code LIKE ? LIMIT 1");
                $stmt->execute([$item_code, $item_code . '-%']);
                $item = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$item) {
                    $response['message'] = 'Item not found in inventory';
                    break;
                }

                // Get current quantity in cart for this item
                $stmt = $conn->prepare("SELECT SUM(quantity) as cart_quantity FROM cart WHERE user_id = ? AND item_code = ?");
                $stmt->execute([$user_id, $item_code]);
                $cart_quantity = $stmt->fetch(PDO::FETCH_ASSOC)['cart_quantity'] ?? 0;

                // Check if adding this quantity would exceed available stock
                if (($cart_quantity + $quantity) > $item['actual_quantity']) {
                    $response['message'] = 'Adding this quantity would exceed available stock. Available: ' . $item['actual_quantity'];
                    break;
                }

                // Set size to 'One Size' for accessories if not set
                if (stripos($item['category'], 'accessories') !== false || stripos($item['category'], 'sti-accessories') !== false) {
                    $size = 'One Size';
                }

                // Check if item already exists in cart with the same size
                $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND item_code = ? AND (size = ? OR (size IS NULL AND ? IS NULL))");
                $stmt->execute([$user_id, $item_code, $size, $size]);
                $existing_item = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existing_item) {
                    // Update quantity if item exists with same size
                    $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
                    $success = $stmt->execute([$quantity, $existing_item['id']]);
                } else {
                    // Insert new item if it doesn't exist with this size
                    $stmt = $conn->prepare("INSERT INTO cart (user_id, item_code, quantity, size) VALUES (?, ?, ?, ?)");
                    $success = $stmt->execute([$user_id, $item_code, $quantity, $size]);
                }

                if (!$success) {
                    $response['message'] = 'Failed to update cart';
                    error_log("Failed to update cart - " . json_encode($stmt->errorInfo()));
                    break;
                }

                // Get total items in cart
                $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                
                $_SESSION['cart_count'] = $total;
                $response['success'] = true;
                $response['message'] = 'Item added to cart successfully';
                $response['cart_count'] = $total;
                break;

            case 'get_cart':
                if (!isset($_SESSION['user_id'])) {
                    $response['message'] = 'Please login to view cart';
                    break;
                }

                $user_id = $_SESSION['user_id'];
                
                // Get all cart items including size, using LIKE for item_code matching
                $stmt = $conn->prepare("
                    SELECT 
                        c.*,
                        i.item_name,
                        i.price,
                        i.image_path,
                        i.category 
                    FROM cart c 
                    LEFT JOIN inventory i ON c.item_code = i.item_code
                    WHERE c.user_id = ?
                    GROUP BY c.id
                ");
                $stmt->execute([$user_id]);
                $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $final_cart_items = [];
                foreach ($cart_items as $item) {
                    if ($item['item_name']) {
                        // Fix image path
                        if ($item['image_path']) {
                            // Remove any existing path prefix
                            $image_name = basename($item['image_path']);
                            $item['image_path'] = '../uploads/itemlist/' . $image_name;
                        } else {
                            $item['image_path'] = '../uploads/itemlist/default.jpg';
                        }
                        // Remove any size suffix from item name (e.g., "Item Name S" -> "Item Name")
                        $item['item_name'] = rtrim($item['item_name'], " SMLX234567");
                        
                        // Make sure size is included in response (even if it's null)
                        if (!isset($item['size'])) {
                            $item['size'] = null;
                        }
                        
                        $final_cart_items[] = $item;
                    } else {
                        // Try one more time with a broader search
                        $stmt = $conn->prepare("
                            SELECT item_name, price, image_path, category 
                            FROM inventory 
                            WHERE item_code LIKE ?
                            LIMIT 1
                        ");
                        $stmt->execute(['%' . $item['item_code'] . '%']);
                        $inventory_item = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($inventory_item) {
                            // Fix image path for found inventory item
                            if ($inventory_item['image_path']) {
                                $image_name = basename($inventory_item['image_path']);
                                $inventory_item['image_path'] = '../uploads/itemlist/' . $image_name;
                            } else {
                                $inventory_item['image_path'] = '../uploads/itemlist/default.jpg';
                            }
                            // Remove any size suffix from item name
                            $inventory_item['item_name'] = rtrim($inventory_item['item_name'], " SMLX234567");
                            $final_cart_items[] = array_merge($item, $inventory_item);
                        } else {
                            // Fallback for items that might not be in inventory anymore
                            $final_cart_items[] = array_merge($item, [
                                'item_name' => 'Item no longer available',
                                'price' => 0,
                                'image_path' => '../uploads/itemlist/default.jpg'
                            ]);
                        }
                    }
                }

                // Update cart count
                $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                $_SESSION['cart_count'] = $total;

                $response['success'] = true;
                $response['cart_items'] = $final_cart_items;
                $response['cart_count'] = $total;
                break;

            case 'update':
                if (!isset($_SESSION['user_id'])) {
                    $response['message'] = 'Please login to update cart';
                    break;
                }

                $item_id = $_POST['item_id'];
                $quantity = intval($_POST['quantity']);
                $user_id = $_SESSION['user_id'];

                // Get item details from cart and inventory
                $stmt = $conn->prepare("
                    SELECT c.item_code, c.quantity as current_cart_quantity, i.actual_quantity 
                    FROM cart c 
                    JOIN inventory i ON c.item_code = i.item_code 
                    WHERE c.id = ? AND c.user_id = ?
                ");
                $stmt->execute([$item_id, $user_id]);
                $item = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$item) {
                    $response['message'] = 'Item not found in cart';
                    break;
                }

                // Get total quantity in cart for this item (excluding current item)
                $stmt = $conn->prepare("
                    SELECT SUM(quantity) as other_cart_quantity 
                    FROM cart 
                    WHERE user_id = ? AND item_code = ? AND id != ?
                ");
                $stmt->execute([$user_id, $item['item_code'], $item_id]);
                $other_cart_quantity = $stmt->fetch(PDO::FETCH_ASSOC)['other_cart_quantity'] ?? 0;

                // Check if new quantity would exceed available stock
                if (($other_cart_quantity + $quantity) > $item['actual_quantity']) {
                    $response['message'] = 'Updating to this quantity would exceed available stock. Available: ' . $item['actual_quantity'];
                    break;
                }

                // Update item quantity
                $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$quantity, $item_id, $user_id]);

                // Get total items in cart
                $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                
                $_SESSION['cart_count'] = $total;
                $response['success'] = true;
                $response['message'] = 'Cart updated successfully';
                $response['cart_count'] = $total;
                break;

            case 'remove':
                if (!isset($_SESSION['user_id'])) {
                    $response['message'] = 'Please login to remove items';
                    break;
                }

                $item_id = $_POST['item_id'];
                $user_id = $_SESSION['user_id'];

                // Remove item from cart
                $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
                $stmt->execute([$item_id, $user_id]);

                // Get total items in cart
                $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                
                $_SESSION['cart_count'] = $total;
                $response['success'] = true;
                $response['message'] = 'Item removed successfully';
                $response['cart_count'] = $total;
                break;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?> 