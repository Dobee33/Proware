<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Pre Order Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/">
    <link rel="stylesheet" href="../CSS/global.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton+SC&family=Smooch+Sans:wght@100..900&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body>
    <?php
    include("../Includes/Header.php");
    ?>

    <div class="preorder-container">
        <div class="process-steps">
            <div class="step completed">1. Pre Order Cart</div>
            <div class="step completed">2. Checkout Details</div>
            <div class="step active">3. Pre Order Details</div>
        </div>

        <div class="order-success">
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <h2>Thank You for Your Pre-Order!</h2>
                <p>Your order has been successfully placed.</p>
            </div>

            <div class="order-details">
                <h3>Order Details</h3>
                <div class="details-group">
                    <p><strong>Order Number:</strong> <span>#PRO12345</span></p>
                    <p><strong>Date:</strong> <span><?php echo date('F d, Y'); ?></span></p>
                </div>

                <div class="customer-info">
                    <h4>Customer Information</h4>
                    <p><strong>Name:</strong> <span>John Doe</span></p>
                    <p><strong>Course/Strand:</strong> <span>STEM</span></p>
                    <p><strong>Email:</strong> <span>john@example.com</span></p>
                    <p><strong>Phone:</strong> <span>+63 123 456 7890</span></p>
                </div>

                <div class="ordered-items">
                    <h4>Ordered Items</h4>
                    <div class="item-list">
                        <!-- Sample item, populate with PHP -->
                        <div class="order-item">
                            <span class="item-name">Product Name</span>
                            <span class="item-quantity">x1</span>
                            <span class="item-price">₱500.00</span>
                        </div>
                    </div>
                    <div class="order-total">
                        <span>Total Amount:</span>
                        <span class="total-amount">₱500.00</span>
                    </div>
                </div>

                <button class="back-home-btn" onclick="window.location.href='ProHome.php'">
                    Back to Home
                </button>
            </div>
        </div>
    </div>

    <style>
        .preorder-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 100px 1rem 1rem 1rem;
        }

        .process-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding: 1rem;
            background: white;
            border-radius: 8px;
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

        .order-success {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
        }

        .success-message {
            margin-bottom: 2rem;
        }

        .success-message i {
            color: green;
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .order-details {
            text-align: left;
            max-width: 800px;
            margin: 0 auto;
        }

        .details-group,
        .customer-info,
        .ordered-items {
            margin: 2rem 0;
            padding: 1.5rem;
            background: #f9f9f9;
            border-radius: 8px;
        }

        .item-list {
            margin: 1rem 0;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }

        .order-total {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 2px solid #eee;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }

        .total-amount {
            color: var(--primary-color);
        }

        .back-home-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 2rem;
            font-family: var(--secondary-font-family);
        }

        .back-home-btn:hover {
            opacity: 0.9;
        }
    </style>