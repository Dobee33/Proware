<?php
require_once '../Includes/connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['id'];
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // First, get the current password from database
    $stmt = $conn->prepare("SELECT password FROM account WHERE id_number = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    $response = ['success' => false, 'messages' => []];

    // Verify current password
    if (!password_verify($currentPassword, $user['password'])) {
        $response['messages']['currentPassword'] = 'Current password is incorrect';
    }

    // Verify new password matches confirmation
    if ($newPassword !== $confirmPassword) {
        $response['messages']['confirmPassword'] = 'New password and confirmation do not match';
    }

    // If no errors, update password
    if (empty($response['messages'])) {
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $conn->prepare("UPDATE account SET password = ? WHERE id_number = ?");

        try {
            $updateStmt->execute([$hashedNewPassword, $userId]);
            $response['success'] = true;
            $response['messages']['success'] = 'Password successfully updated';
        } catch (PDOException $e) {
            $response['messages']['error'] = 'Error updating password. Please try again.';
        }
    }

    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .error-feedback {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .success-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            max-width: 400px;
            width: 90%;
            text-align: center;
        }

        .modal-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .success-icon {
            color: #28a745;
            font-size: 48px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>Change Password</h2>
        <form id="changePasswordForm" method="POST">
            <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="currentPassword" id="currentPassword" class="form-control" required>
                <div class="error-feedback" id="currentPasswordError"></div>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="newPassword" id="newPassword" class="form-control" required>
                <div class="error-feedback" id="newPasswordError"></div>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" required>
                <div class="error-feedback" id="confirmPasswordError"></div>
            </div>
            <button type="submit" class="btn btn-primary">Confirm Password</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
            <a href="admin_page.php" class="btn btn-danger">Cancel</a>
        </form>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="success-modal">
        <div>
            <i class="fas fa-check-circle success-icon"></i>
            <h4>Success!</h4>
            <p>Password has been successfully changed.</p>
            <button class="btn btn-primary mt-3" onclick="redirectToAdmin()">Continue</button>
        </div>
    </div>
    <div id="modalBackdrop" class="modal-backdrop"></div>

    <script>
        document.getElementById('changePasswordForm').addEventListener('submit', function (e) {
            e.preventDefault();

            // Clear previous error messages
            document.querySelectorAll('.error-feedback').forEach(el => el.textContent = '');

            const formData = new FormData(this);

            fetch('change_password.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success modal
                        document.getElementById('successModal').style.display = 'block';
                        document.getElementById('modalBackdrop').style.display = 'block';
                    } else {
                        // Show error messages
                        if (data.messages.currentPassword) {
                            document.getElementById('currentPasswordError').textContent = data.messages.currentPassword;
                        }
                        if (data.messages.confirmPassword) {
                            document.getElementById('confirmPasswordError').textContent = data.messages.confirmPassword;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });

        // Real-time password confirmation check
        document.getElementById('confirmPassword').addEventListener('input', function () {
            const newPassword = document.getElementById('newPassword').value;
            const confirmError = document.getElementById('confirmPasswordError');

            if (this.value !== newPassword) {
                confirmError.textContent = 'Passwords do not match';
            } else {
                confirmError.textContent = '';
            }
        });

        function redirectToAdmin() {
            window.location.href = 'admin_page.php';
        }
    </script>
</body>

</html>