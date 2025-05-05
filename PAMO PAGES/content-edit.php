<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Management</title>
    <link rel="stylesheet" href="../PAMO CSS/content-edit.css">
    <link rel="stylesheet" href="../assets/css/content-edit.css">
    <link rel="stylesheet" href="../PAMO CSS/styles.css">
    <script src="../PAMO JS/content-edit.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php include '../PAMO PAGES/includes/sidebar.php'; ?>
        <main class="main-content">
            <header>
                <h1>Content Management</h1>
            </header>

            <div class="content-container">
                <!-- Hero Section Box -->
                <div class="section-box" onclick="openSection('hero')">
                    <div class="section-header">
                        <h2>Hero Section</h2>
                        <i class="material-icons">image</i>
                    </div>
                    <div class="section-content" id="hero-content">
                        <div class="current-images">
                            <h3>Current Hero Images</h3>
                            <div class="image-grid" id="hero-image-grid">
                                <!-- Hero images will be dynamically added here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categories Section Box -->
                <div class="section-box" onclick="openSection('categories')">
                    <div class="section-header">
                        <h2>Categories Section</h2>
                        <i class="material-icons">category</i>
                    </div>
                    <div class="section-content" id="categories-content">
                        <div class="category-grid">
                            <!-- STI Uniform Box -->
                            <div class="category-box" onclick="openCategory('uniform')">
                                <h3>STI Uniform</h3>
                                <div class="category-images" id="uniform-images">
                                    <!-- Uniform images will be dynamically added here -->
                                </div>
                            </div>
                            
                            <!-- STI Accessories Box -->
                            <div class="category-box" onclick="openCategory('accessories')">
                                <h3>STI Accessories</h3>
                                <div class="category-images" id="accessories-images">
                                    <!-- Accessories images will be dynamically added here -->
                                </div>
                            </div>
                            
                            <!-- STI Shirt Box -->
                            <div class="category-box" onclick="openCategory('shirt')">
                                <h3>STI Shirt</h3>
                                <div class="category-images" id="shirt-images">
                                    <!-- Shirt images will be dynamically added here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Featured Products Box -->
                <div class="section-box" onclick="openSection('featured')">
                    <div class="section-header">
                        <h2>Featured Products</h2>
                        <i class="material-icons">star</i>
                    </div>
                    <div class="section-content" id="featured-content">
                        <div class="featured-grid" id="featured-products-grid">
                            <!-- Featured products will be dynamically added here -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../PAMO JS/content-edit.js"></script>
</body>

</html>