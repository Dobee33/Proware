<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School IMS - Content Management</title>
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
                <button class="add-content-btn" onclick="openUploadModal()">
                    <i class="material-icons">add_photo_alternate</i>
                    Add New Content
                </button>
            </header>

            <div class="content-container">
                <div class="content-filters">
                    <div class="search-bar">
                        <i class="material-icons">search</i>
                        <input type="text" placeholder="Search content...">
                    </div>
                    <select id="contentType">
                        <option value="all">All Content</option>
                        <option value="banner">Banners</option>
                        <option value="announcement">Announcements</option>
                        <option value="gallery">Gallery</option>
                    </select>
                </div>

                <div class="content-grid">
                    <!-- Content items will be dynamically added here -->
                </div>
            </div>

            <!-- Upload Modal -->
            <div id="uploadModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Add New Content</h2>
                    <form id="uploadForm">
                        <div class="form-group">
                            <label>Content Type</label>
                            <select name="type" required>
                                <option value="banner">Banner</option>
                                <option value="announcement">Announcement</option>
                                <option value="gallery">Gallery</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="3"></textarea>
                        </div>
                        <div class="upload-area" id="dropZone">
                            <input type="file" id="fileInput" accept="image/*" hidden>
                            <div class="upload-prompt">
                                <i class="material-icons">cloud_upload</i>
                                <p>Drag & Drop image here or</p>
                                <button type="button" onclick="document.getElementById('fileInput').click()">
                                    Browse Files
                                </button>
                            </div>
                            <div class="preview-area" id="previewArea"></div>
                        </div>
                        <div class="form-group">
                            <label>Display Duration</label>
                            <div class="date-range">
                                <input type="date" name="startDate" required>
                                <span>to</span>
                                <input type="date" name="endDate" required>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-secondary" onclick="closeUploadModal()">Cancel</button>
                            <button type="submit" class="btn-primary">Upload Content</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>

</html>