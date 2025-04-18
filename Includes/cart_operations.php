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
                $quantity = $_POST['quantity'];
                $user_id = $_SESSION['user_id'];

                // Check if item already exists in cart
                $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND item_code = ?");
                $stmt->execute([$user_id, $item_code]);
                $existing_item = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existing_item) {
                    // Update quantity if item exists
                    $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
                    $stmt->execute([$quantity, $existing_item['id']]);
                } else {
                    // Insert new item if it doesn't exist
                    $stmt = $conn->prepare("INSERT INTO cart (user_id, item_code, quantity) VALUES (?, ?, ?)");
                    $stmt->execute([$user_id, $item_code, $quantity]);
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
                
                // First get all cart items
                $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $final_cart_items = [];
                foreach ($cart_items as $cart_item) {
                    // Debug: Log the cart item code
                    error_log("Cart Item Code: " . $cart_item['item_code']);
                    
                    // Try to get inventory details for each cart item
                    $stmt = $conn->prepare("SELECT item_name, price, image_path FROM inventory WHERE item_code = ?");
                    $stmt->execute([$cart_item['item_code']]);
                    $inventory_item = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($inventory_item) {
                        // Debug: Log successful match
                        error_log("Found matching inventory item for: " . $cart_item['item_code']);
                        $final_cart_items[] = array_merge($cart_item, $inventory_item);
                    } else {
                        // Debug: Log failed match
                        error_log("No matching inventory item found for: " . $cart_item['item_code']);
                        
                        // Try to find the item with a LIKE query to catch potential formatting differences
                        $stmt = $conn->prepare("SELECT item_name, price, image_path FROM inventory WHERE item_code LIKE ?");
                        $stmt->execute(['%' . $cart_item['item_code'] . '%']);
                        $inventory_item = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($inventory_item) {
                            error_log("Found similar inventory item using LIKE query for: " . $cart_item['item_code']);
                            $final_cart_items[] = array_merge($cart_item, $inventory_item);
                        } else {
                            $final_cart_items[] = array_merge($cart_item, [
                                'item_name' => 'Item no longer available',
                                'price' => 0,
                                'image_path' => 'default.jpg'
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
                $quantity = $_POST['quantity'];
                $user_id = $_SESSION['user_id'];

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