<!-- Top section with filters and actions -->
<div class="filters-section">
    <div class="filters">
        <label>Filter By:</label>
        <select class="category-dropdown">
            <option value="all">All Users</option>
            <option value="students">Students</option>
            <option value="pamo">PAMO</option>
        </select>
    </div>

    <div class="search-container">
        <input type="text" class="search-bar" placeholder="Search..." id="searchInput">
        <i class="fas fa-search search-icon"></i>
    </div>

    <div class="logout-container">
        <button class="btn btn-danger logout-btn" onclick="logout()">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </div>
</div>

<div class="action-buttons">
    <button class="btn btn-primary" onclick="window.location.href='add_account.php'">
        <i class="fas fa-plus"></i> Add Account
    </button>
    <button class="btn btn-secondary" onclick="changePassword()" id="changePasswordBtn" disabled>
        <i class="fas fa-key"></i> Change Password
    </button>
    <button class="btn btn-info" onclick="updateStatus()" id="updateStatusBtn" disabled>
        <i class="fas fa-sync"></i> Update Status
    </button>
</div>
</div>

<!-- Add some spacing before the table -->
<div class="table-container">
    <!-- Users table -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>ID Number</th>
                <th>Email</th>
                <th>Password</th>
                <th>Role</th>
                <th>Status</th>
                <th>Date Created</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require_once '../Includes/connection.php';

            $sql = "SELECT * FROM account";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($accounts as $account) {
                echo "<tr data-id='" . htmlspecialchars($account['id_number']) . "' class='account-row'>";
                echo "<td>" . htmlspecialchars($account['first_name']) . "</td>";
                echo "<td>" . htmlspecialchars($account['last_name']) . "</td>";
                echo "<td>" . htmlspecialchars($account['id_number']) . "</td>";
                echo "<td>" . htmlspecialchars($account['email']) . "</td>";
                echo "<td>********</td>"; // Hide actual password
                echo "<td>" . htmlspecialchars($account['role']) . "</td>";
                echo "<td>" . htmlspecialchars($account['status']) . "</td>";
                echo "<td>" . htmlspecialchars($account['date_created']) . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Update Status Modal -->
<div class="modal" id="updateStatusModal">
    <div class="modal-content">
        <h3>Update Status</h3>
        <form id="updateStatusForm">
            <input type="hidden" id="selectedUserId" name="userId">
            <div class="form-group">
                <label for="statusSelect">Select Status:</label>
                <select name="status" id="statusSelect" class="form-control" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Update Status</button>
                <button type="button" class="btn btn-secondary"
                    onclick="closeModal('updateStatusModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Add this success modal -->
<div class="modal" id="successModal">
    <div class="modal-content">
        <div class="text-center">
            <i class="fas fa-check-circle success-icon"></i>
            <h4>Success!</h4>
            <p>Account status has been updated successfully.</p>
            <button class="btn btn-primary" onclick="closeSuccessModal()">OK</button>
        </div>
    </div>
</div>

<script>
    function logout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = '../Pages/login.php';
        }
    }

    document.getElementById('searchInput').addEventListener('input', function (e) {
        const searchTerm = e.target.value.toLowerCase();
        const tableRows = document.querySelectorAll('table tbody tr');

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    let selectedUserId = null;

    document.querySelectorAll('.account-row').forEach(row => {
        row.addEventListener('click', function () {
            // Remove previous selection
            document.querySelectorAll('.account-row').forEach(r => r.classList.remove('selected'));

            // Add selection to clicked row
            this.classList.add('selected');
            selectedUserId = this.dataset.id;

            // Enable buttons
            document.getElementById('changePasswordBtn').disabled = false;
            document.getElementById('updateStatusBtn').disabled = false;
        });
    });

    function changePassword() {
        if (!selectedUserId) return;
        window.location.href = `change_password.php?id=${selectedUserId}`;
    }

    function updateStatus() {
        if (!selectedUserId) return;
        document.getElementById('selectedUserId').value = selectedUserId;
        document.getElementById('updateStatusModal').style.display = 'block';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    document.getElementById('updateStatusForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('update_status.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close the update modal
                    closeModal('updateStatusModal');

                    // Show success modal
                    document.getElementById('successModal').style.display = 'block';

                    // Update the status in the table
                    const row = document.querySelector(`tr[data-id="${formData.get('userId')}"]`);
                    if (row) {
                        row.cells[6].textContent = formData.get('status');
                    }
                } else {
                    alert('Error updating status: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating status. Please try again.');
            });
    });

    function closeSuccessModal() {
        document.getElementById('successModal').style.display = 'none';
        location.reload(); // Refresh the page to show updated data
    }

    // Add this to your existing styles
    document.head.insertAdjacentHTML('beforeend', `
        <style>
            .account-row {
                cursor: pointer;
            }
            .account-row.selected {
                background-color: #e3f2fd !important;
            }
            .action-buttons button:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }
            .modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.5);
                z-index: 1000;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .modal-content {
                background: white;
                padding: 25px;
                border-radius: 8px;
                box-shadow: 0 2px 15px rgba(0,0,0,0.2);
                width: 90%;
                max-width: 400px;
                position: relative;
                animation: modalFadeIn 0.3s ease-out;
            }
            @keyframes modalFadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            .success-icon {
                color: #28a745;
                font-size: 48px;
                margin-bottom: 15px;
            }
            .modal-content h3 {
                margin-bottom: 20px;
                color: #333;
                text-align: center;
            }
            .modal-content form {
                margin-bottom: 15px;
            }
            .modal-content .form-group {
                margin-bottom: 20px;
            }
            .modal-content .btn {
                margin: 5px;
            }
            .modal-content .mt-3 {
                text-align: center;
            }
        </style>
    `);
</script>
<style>
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        margin-bottom: 20px;
    }

    .search-container {
        position: relative;
        width: 300px;
    }

    .search-bar {
        width: 100%;
        padding: 10px 35px 10px 15px;
        border: 1px solid #ddd;
        border-radius: 20px;
        font-size: 14px;
    }

    .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
    }

    .logout-btn {
        padding: 8px 20px;
        border-radius: 5px;
        font-weight: 500;
    }

    .logout-btn i {
        margin-right: 5px;
    }


    .filters-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .category-dropdown {
        padding: 8px;
        border-radius: 4px;
        border: 1px solid #ddd;
    }

    .action-buttons button {
        margin-left: 10px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: white;
        width: 500px;
        margin: 100px auto;
        padding: 20px;
        border-radius: 5px;
    }

    #addAccountForm {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    #addAccountForm input,
    #addAccountForm select {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .search-container {
        position: relative;
        width: 400px;
        margin: 0 auto;
    }

    .search-bar {
        width: 100%;
        padding: 12px 40px 12px 20px;
        border: 1px solid #ddd;
        border-radius: 25px;
        font-size: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
    }

    .logout-container {
        margin-left: auto;
    }

    .logout-btn {
        padding: 10px 25px;
        border-radius: 5px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .logout-btn:hover {
        background-color: #dc3545;
        transform: translateY(-2px);
    }

    .action-buttons {
        padding: 20px;
        margin: 20px 0 30px 0;
        text-align: center;
    }

    .action-buttons button {
        margin: 0 10px;
        padding: 12px 25px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .action-buttons button:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .table-container {
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .table {
        margin-top: 20px;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table th {
        background-color: #f8f9fa;
        padding: 15px;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    .table td {
        padding: 15px;
        border-bottom: 1px solid #dee2e6;
    }
</style>