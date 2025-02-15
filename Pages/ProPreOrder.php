<!DOCTYPE html>
<html lang="en">

<head>
    <title>Pre Order Page</title>
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/ProPreOrder.css">
    <link rel="stylesheet" href="../CSS/global.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Smooch+Sans:wght@100..900&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php
    include("../Includes/Header.php");
    ?>
    <div class="preorder-container">
        <div class="process-steps">
            <div class="step active">1. Pre Order Cart</div>
            <div class="step">2. Checkout Details</div>
            <div class="step">3. Pre Order Details</div>
        </div>

        <h1>Cart</h1>
        
        <div class="cart-layout">
            <div class="products-section">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><img src="../Images/sample-product.jpg" alt="Product" class="product-image"></td>
                            <td>Sample Product</td>
                            <td>₱350.00</td>
                            <td>
                                <div class="quantity-control">
                                    <button type="button" class="qty-btn">-</button>
                                    <input type="number" value="1" min="1" class="qty-input">
                                    <button type="button" class="qty-btn">+</button>
                                </div>
                            </td>
                            <td>
                                <button class="delete-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="summary-section">
                <table class="total-table">
                    <tr>
                        <th>Total Amount:</th>
                        <td>₱350.00</td>
                    </tr>
                </table>

                <form action="ProCheckout.php" method="POST" id="checkoutForm">
                    <input type="hidden" name="cart_items" value="">
                    <input type="hidden" name="total_amount" value="350.00">
                    <button type="submit" class="proceed-btn">Proceed to Checkout</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        .preorder-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .process-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding: 1rem;
            background: white;
            border-radius: 8px;
            margin-top: 100px;
        }

        .step {
            font-family: var(--primary-font-family);
            color: var(--text-color);
            padding: 0.5rem 1rem;
        }

        .step.active {
            color: var(--primary-color);
        }

        .step.completed {
            color: green;
        }

        .delete-btn {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            padding: 0.5rem;
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .delete-btn:hover {
            color: #c82333;
        }

        h1 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 2rem;
        }

        .products-table, .total-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .products-table th, .products-table td,
        .total-table th, .total-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .products-table th {
            background: var(--primary-color);
            color: white;
        }

        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .qty-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            cursor: pointer;
            border-radius: 4px;
        }

        .qty-input {
            width: 60px;
            padding: 0.5rem;
            text-align: center;
        }

        .total-table {
            max-width: 300px;
            margin-left: auto;
        }

        .proceed-btn {
            display: block;
            width: 200px;
            margin: 2rem 0 0 auto;
            padding: 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: var(--secondary-font-family);
            font-size: 1.1rem;
        }

        .proceed-btn:hover {
            background: #005a94;
        }

        .cart-layout {
            display: flex;
            gap: 2rem;
            align-items: flex-start;
        }

        .products-section {
            flex: 1;
        }

        .summary-section {
            width: 300px;
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .total-table {
            margin-left: 0;
            margin-bottom: 1.5rem;
        }

        .proceed-btn {
            width: 100%;
            margin: 0;
        }

        @media (max-width: 768px) {
            .cart-layout {
                flex-direction: column;
            }

            .summary-section {
                width: 100%;
            }
        }
    </style>

    <script>
        // Add this JavaScript to ensure the form submits properly
        document.getElementById('checkoutForm').addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent default form submission
            // You can add validation here if needed
            this.submit(); // Submit the form
        });
    </script>
</body>

</html>