<nav class="sidebar">
    <div class="logo">
        <img src="../Images/STI-LOGO.png" alt="PAMO Logo">
        <h2>PAMO</h2>
    </div>
    <ul class="nav-links">
        <li <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : ''; ?>
            onclick="window.location.href='dashboard.php'">
            <i class="material-icons">dashboard</i>Dashboard
        </li>

        <li <?php echo basename($_SERVER['PHP_SELF']) == 'inventory.php' ? 'class="active"' : ''; ?>
            onclick="window.location.href='inventory.php'">
            <i class="material-icons">inventory_2</i>Inventory
        </li>
        <li <?php echo basename($_SERVER['PHP_SELF']) == 'preorders.php' ? 'class="active"' : ''; ?>
            onclick="window.location.href='preorders.php'">
            <i class="material-icons">shopping_cart</i>Pre Orders
        </li>
        <li <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'class="active"' : ''; ?>
            onclick="window.location.href='reports.php'">
            <i class="material-icons">assessment</i>Reports
        </li>
        <li <?php echo basename($_SERVER['PHP_SELF']) == 'content-edit.php' ? 'class="active"' : ''; ?>
            onclick="window.location.href='content-edit.php'">
            <i class="material-icons">inventory_2</i>Edit Content
        </li>
    </ul>
    <div class="user-info">
        <img src="avatar.png" alt="User Avatar">
        <div class="user-details">
            <h4>
                <?php
                include '../includes/connection.php';
                if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];
                    $query = "SELECT first_name, last_name, role_category, program_or_position FROM account WHERE id = :user_id";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt->execute();

                    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo $row['first_name'] . ' ' . $row['last_name'];
                        $_SESSION['role_category'] = $row['role_category'];
                        $_SESSION['program_or_position'] = $row['program_or_position'];
                    } else {
                        echo 'Guest User';
                    }
                } else {
                    echo 'Guest User';
                }
                ?>
            </h4>
            <p>
                <?php
                if (isset($_SESSION['program_or_position']) && $_SESSION['program_or_position'] === 'PAMO') {
                    echo 'PAMO';
                } else {
                    echo isset($_SESSION['role_category']) ? $_SESSION['role_category'] : 'No Role Assigned';
                }
                ?>
            </p>
        </div>
    </div>
</nav>

<style>
    .sidebar {
        width: 250px;
        background-color: #FEFBC7;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        margin-right: 0px;
        font-size: 20px;
    }

    .logo {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0px 20px 0px 20px;
    }

    .logo img {
        width: 70px;
        height: 70px;
    }

    .logo h2 {
        font-weight: bold;
        margin-left: 10px;
        font-size: 40px;
        margin-top: 40px;
    }
</style>