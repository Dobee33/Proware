<?php
require_once '../Includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $idNumber = $_POST['idNumber'];
    $role_category = $_POST['role_category'];
    $program_or_position = $_POST['program_position'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Validate ID number
    if (strlen($idNumber) !== 11) {
        echo json_encode(['error' => 'ID Number must be exactly 11 digits']);
        exit;
    }

    // Generate email
    $lastSixDigits = substr($idNumber, -6);
    $email = strtolower(str_replace(' ', '', $lastName . '.' . $lastSixDigits . '@lucena.sti.edu.ph'));

    // Start session before using it
    session_start();

    $sql = "INSERT INTO account (first_name, last_name, id_number, email, password, role_category, program_or_position, status, date_created) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'active', CURRENT_TIMESTAMP)";

    try {
        $stmt = $conn->prepare($sql);

        $result = $stmt->execute([
            $firstName,
            $lastName,
            $idNumber,
            $email,
            $password,
            $role_category,
            $program_or_position
        ]);

        if ($result) {
            $_SESSION['success_message'] = "Account successfully created!";
            $_SESSION['generated_email'] = str_replace(' ', '', $email);
            header("Location: add_account.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error creating account. Please try again.";
            header("Location: add_account.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
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
    <link rel="stylesheet" href="../ADMIN CSS/add_account.css">
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
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="firstName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="lastName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="idNumber">ID Number (11 digits)</label>
                <input type="text" id="idNumber" name="idNumber" class="form-control" pattern="\d{11}" required>
            </div>
            <div class="form-group">
                <label for="generatedEmailPreview">Generated Email</label>
                <input type="text" id="generatedEmailPreview" class="form-control" readonly
                    style="background-color: #f8f9fa; color: #495057;">
            </div>
            <div class="form-group">
                <label for="roleCategory">Role Category</label>
                <select id="roleCategory" name="role_category" class="form-control" required
                    onchange="updateProgramPositions()">
                    <option value="">Select Role Category</option>
                    <option value="SHS">SHS</option>
                    <option value="COLLEGE STUDENT">COLLEGE STUDENT</option>
                    <option value="EMPLOYEE">EMPLOYEE</option>
                </select>
            </div>
            <div class="form-group">
                <label for="programPosition">Program/Position</label>
                <select id="programPosition" name="program_position" class="form-control" required>
                    <option value="">Select Program/Position</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
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
            
            // Get the selected role category
            const roleCategory = document.getElementById('roleCategory').value;
            
            // Redirect based on role category
            if (roleCategory === 'EMPLOYEE' && document.getElementById('programPosition').value === 'TEACHER') {
                window.location.href = '../home.php';
            } else {
                window.location.href = 'admin_page.php';
            }
        }

        // Function to generate email preview
        function updateEmailPreview() {
            const lastName = document.getElementById('lastName').value.toLowerCase();
            const idNumber = document.getElementById('idNumber').value;
            const emailPreview = document.getElementById('generatedEmailPreview');

            if (lastName && idNumber.length >= 6) {
                const lastSixDigits = idNumber.slice(-6);
                emailPreview.value = `${lastName}.${lastSixDigits}@lucena.sti.edu.ph`.replace(/\s+/g, '');
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

        function updateProgramPositions() {
            const roleCategory = document.getElementById('roleCategory').value;
            const programPositionSelect = document.getElementById('programPosition');

            // Clear existing options
            programPositionSelect.innerHTML = '<option value="">Select Program/Position</option>';

            // Define options for each role category
            const options = {
                'SHS': ['Science, Technology, Engineering, and Mathematics (STEM)', 'Humanities and Social Sciences (HUMMS)', 'Accountancy, Business, and Management (ABM)', 'Mobile App and Web Development (MAWD)', 'Digital Arts (DA)', 'Tourism Operations (TOPER)', 'Culinary Arts (CA)'],
                'COLLEGE STUDENT': ['Bachelor of Science in Computer Science (BSCS)', 'Bachelor of Science in Information Technology (BSIT)', 'Bachelor of Science in Computer Engineering (BSCPE)', 'Bachelor of Science in Culinary Management (BSCM)', 'Bachelor of Science in Tourism Management (BSTM)', 'Bachelor of Science in Business Administration (BSBA)', 'Bachelor of Science in Multimedia Arts (BMMA)'],
                'EMPLOYEE': ['TEACHER', 'PAMO', 'ADMIN', 'STAFF']
            };

            // Add new options based on selected role
            if (roleCategory in options) {
                options[roleCategory].forEach(option => {
                    const newOption = document.createElement('option');
                    newOption.value = option;
                    newOption.textContent = option;
                    programPositionSelect.appendChild(newOption);
                });
            }
        }

        // Make sure this is called when the page loads
        document.addEventListener('DOMContentLoaded', updateProgramPositions);
    </script>
</body>

</html>