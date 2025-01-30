<?php
require_once '../Includes/connection.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM account WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($user['status'] === 'inactive') {
            header("Location: login.php?error=account_inactive");
            exit();
        }
        if (password_verify($password, $user['password'])) {

            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'student') {
                header("Location: ProHome.php");
                exit();
            } else if ($user['role'] === 'pamo') {
                header("Location: ../PAMO PAGES/index.php");
                exit();
            } else if ($user['role'] === 'admin') {
                header("Location: ../ADMIN/admin_page.php");
                exit();
            }
        } else {
            header("Location: login.php?error=incorrect_password");
            exit();
        }
    } else {
        header("Location: login.php?error=account_not_found ");
        //header("Location: login.php?error=account_not_found " . password_hash($password, PASSWORD_DEFAULT));//
        exit();
    }
    // Close connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STI Login</title>
</head>

<body>
    <div class="login-container">
        <div class="content-wrapper">
            <div class="logo-section">
                <div class="logo-container">
                    <img src="../Images/STI-LOGO.png" alt="STI Logo">
                </div>
            </div>
            <div class="form-container">
                <h2>Welcome Back!</h2>
                <p class="subtitle">Please login to your account</p>
                <form method="POST" action="">
                    <div class="form-group">
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" placeholder="Email Address" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="Password" required>
                        </div>
                    </div>
                    <button type="submit" class="login-btn">
                        <span>Login</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php
                            switch ($_GET['error']) {
                                case 'incorrect_password':
                                    echo 'Incorrect password. Please try again.';
                                    break;
                                case 'account_not_found':
                                    echo 'Account does not exist. Please check your email.';
                                    break;
                                case 'account_inactive':
                                    echo 'Your account is currently inactive. Please contact the administrator.';
                                    break;
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background-image: url('../Images/STI-LOGIN.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        position: relative;
    }

    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom,
                #F6ECBD 0%,
                #ECE386 25%,
                #E3DD6D 50%);
        opacity: 0.8;
        z-index: 1;
    }

    .login-container {
        width: 100%;
        max-width: 900px;
        position: relative;
        z-index: 2;
        padding: 2rem;
    }

    .content-wrapper {
        display: flex;
        align-items: center;
        gap: 3rem;
        background: rgba(255, 255, 255, 0.68);
        padding: 3rem;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(1px);
    }

    .logo-section {
        flex: 0 0 350px;
        background: rgba(246, 236, 189, 0.3);
        border-radius: 15px;
        transition: transform 0.3s ease;
    }

    .logo-container {
        text-align: center;
    }

    .logo-container img {
        width: 100%;
        height: auto;
        border-radius: 20px;

    }

    .location {
        color: #003d82;
        font-weight: bold;
        font-size: 1.8rem;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 2px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .form-container {
        flex: 1;
        max-width: 400px;
    }

    h2 {
        color: #003d82;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .subtitle {
        color: #666;
        margin-bottom: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .input-icon {
        position: relative;
    }

    .input-icon i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #0056b3;
    }

    .form-group input {
        width: 100%;
        padding: 1rem 1rem 1rem 45px;
        border: 2px solid #e1e1e1;
        border-radius: 10px;
        font-size: 1rem;
        background: white;
        transition: all 0.3s ease;
    }

    .form-group input:focus {
        border-color: #0056b3;
        box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.1);
        outline: none;
    }

    .login-btn {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #0056b3, #003d82);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .login-btn:hover {
        background: linear-gradient(135deg, #003d82, #002855);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .error-message {
        color: #dc3545;
        font-size: 0.9rem;
        margin-top: 1rem;
        text-align: center;
        background: rgba(220, 53, 69, 0.1);
        padding: 0.8rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 1px solid rgba(220, 53, 69, 0.2);
    }

    .error-message i {
        font-size: 1.1rem;
    }

    @media (max-width: 768px) {
        .content-wrapper {
            flex-direction: column;
            padding: 2rem;
        }

        .logo-section {
            flex: 0 0 auto;
            width: 100%;
            max-width: 300px;
        }
    }
</style>

</html>