<?php
session_start();
include '../Includes/connection.php'; // Add DB connection for image preview
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Management</title>
    <link rel="stylesheet" href="../PAMO CSS/content-edit.css">
    <link rel="stylesheet" href="../PAMO CSS/styles.css">
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
                <!-- Display Section Upload Form -->
                <div class="section-box">
                    <div class="section-header">
                        <h2>Display Section Images</h2>
                        <i class="material-icons">image</i>
                    </div>
                    <div class="section-content">
                        <form action="upload-content-image.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="section" value="display">
                            <label>Image Title: <input type="text" name="title" required></label>
                            <input type="file" name="image" accept="image/*" required>
                            <button type="submit">Upload to Display Section</button>
                        </form>
                        <h3>Current Display Images</h3>
                        <div class="image-grid">
                        <?php
                        $sql = "SELECT * FROM homepage_content WHERE section='display' ORDER BY created_at DESC";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            echo '<img src="../' . htmlspecialchars($row['image_path']) . '" alt="' . htmlspecialchars($row['title']) . '" width="100">';
                        }
                        ?>
                        </div>
                    </div>
                </div>

                <!-- New Arrivals Section Upload Form -->
                <div class="section-box">
                    <div class="section-header">
                        <h2>New Arrivals Images</h2>
                        <i class="material-icons">category</i>
                    </div>
                    <div class="section-content">
                        <form action="upload-content-image.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="section" value="new_arrival">
                            <label>Image Title: <input type="text" name="title" required></label>
                            <input type="file" name="image" accept="image/*" required>
                            <button type="submit">Upload to New Arrivals</button>
                        </form>
                        <h3>Current New Arrivals Images</h3>
                        <div class="image-grid">
                        <?php
                        $sql = "SELECT * FROM homepage_content WHERE section='new_arrival' ORDER BY created_at DESC";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            echo '<img src="../' . htmlspecialchars($row['image_path']) . '" alt="' . htmlspecialchars($row['title']) . '" width="100">';
                        }
                        ?>
                        </div>
                    </div>
                </div>

            </div>

            <script>
            document.querySelectorAll('.section-box').forEach(box => {
                box.addEventListener('click', function(e) {
                    // Only toggle if clicking the header, not inside the form
                    if (!e.target.closest('form')) {
                        this.querySelector('.section-content').classList.toggle('active');
                    }
                });
            });
            </script>
        </main>
    </div>
</body>

</html>