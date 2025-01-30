<?php
require_once '../Includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $idNumber = $_POST['idNumber'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Validate ID number
    if (strlen($idNumber) !== 11) {
        echo json_encode(['error' => 'ID Number must be exactly 11 digits']);
        exit;
    }

    // Generate email
    $lastSixDigits = substr($idNumber, -6);
    $email = strtolower($lastName . '.' . $lastSixDigits . '@lucena.sti.edu.ph');

    // Insert into database
    $sql = "INSERT INTO account (first_name, last_name, id_number, email, password, role, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'active')";
    $stmt = $conn->prepare($sql);

    try {
        $stmt->execute([$firstName, $lastName, $idNumber, $email, $password, $role]);
        // Store success message in session
        session_start();
        $_SESSION['success_message'] = "Account successfully created!";
        $_SESSION['generated_email'] = $email;
        header("Location: add_account.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error creating account. Please try again.";
        header("Location: add_account.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Account</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
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
            max-width: 500px;
            width: 90%;
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

        .email-display {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            word-break: break-all;
        }
    </style>
</head>

<body>
    <!-- Success Modal -->
    <div id="successModal" class="success-modal">
        <div class="text-center">
            <i class="fas fa-check-circle success-icon"></i>
            <h4>Account Created Successfully!</h4>
            <p>The account has been created with the following email:</p>
            <div class="email-display">
                <strong id="generatedEmail"></strong>
            </div>
            <p class="text-muted">Please make sure to save this email address for future reference.</p>
            <button class="btn btn-primary mt-3" onclick="closeModal()">Continue</button>
        </div>
    </div>
    <div id="modalBackdrop" class="modal-backdrop"></div>

    <div class="container mt-5">
        <h2>Add New Account</h2>
        <form id="addAccountForm" method="POST">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="firstName" id="firstName" class="form-control" pattern="[A-Za-z\s]+"
                    title="Only letters and spaces allowed" required>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="lastName" id="lastName" class="form-control" pattern="[A-Za-z\s]+"
                    title="Only letters and spaces allowed" required>
            </div>
            <div class="form-group">
                <label>ID Number (11 digits)</label>
                <input type="text" name="idNumber" id="idNumber" class="form-control" pattern="\d{11}" required>
            </div>
            <div class="form-group">
                <label>Generated Email</label>
                <input type="text" id="generatedEmailPreview" class="form-control" readonly
                    style="background-color: #f8f9fa; color: #495057;">
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="pamo">PAMO</option>
                    <option value="student">Student</option>
                </select>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Account</button>
            <a href="admin_page.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script>
        <?php
        session_start();
        if (isset($_SESSION['success_message']) && isset($_SESSION['generated_email'])) {
            echo "window.onload = function() {
                document.getElementById('successModal').style.display = 'block';
                document.getElementById('modalBackdrop').style.display = 'block';
                document.getElementById('generatedEmail').textContent = '" . $_SESSION['generated_email'] . "';
            };";
            unset($_SESSION['success_message']);
            unset($_SESSION['generated_email']);
        }
        ?>

        function closeModal() {
            document.getElementById('successModal').style.display = 'none';
            document.getElementById('modalBackdrop').style.display = 'none';
            window.location.href = 'admin_page.php'; // Redirect to admin page after closing modal
        }

        // Function to generate email preview
        function updateEmailPreview() {
            const lastName = document.getElementById('lastName').value.toLowerCase();
            const idNumber = document.getElementById('idNumber').value;
            const emailPreview = document.getElementById('generatedEmailPreview');

            if (lastName && idNumber.length >= 6) {
                const lastSixDigits = idNumber.slice(-6);
                emailPreview.value = `${lastName}.${lastSixDigits}@lucena.sti.edu.ph`;
            } else {
                emailPreview.value = '';
            }
        }

        // Add event listeners for real-time update
        document.getElementById('lastName').addEventListener('input', updateEmailPreview);
        document.getElementById('idNumber').addEventListener('input', updateEmailPreview);

        // Add ID number validation
        document.getElementById('idNumber').addEventListener('input', function (e) {
            const input = e.target;
            // Remove any non-digit characters
            input.value = input.value.replace(/\D/g, '');

            // Limit to 11 digits
            if (input.value.length > 11) {
                input.value = input.value.slice(0, 11);
            }
        });

        // Function to validate name input
        function validateNameInput(input) {
            // Remove any numbers or special characters
            input.value = input.value.replace(/[^A-Za-z\s]/g, '');
        }

        // Add event listeners for name fields
        document.getElementById('firstName').addEventListener('input', function (e) {
            validateNameInput(this);
            this.setCustomValidity('');
            if (!/^[A-Za-z\s]*$/.test(this.value)) {
                this.setCustomValidity('Please enter only letters and spaces');
            }
        });

        document.getElementById('lastName').addEventListener('input', function (e) {
            validateNameInput(this);
            this.setCustomValidity('');
            if (!/^[A-Za-z\s]*$/.test(this.value)) {
                this.setCustomValidity('Please enter only letters and spaces');
            }
            updateEmailPreview();
        });
    </script>
</body>

</html>