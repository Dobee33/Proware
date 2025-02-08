<!-- Top section with filters and actions -->
<div class="filters-section">
    <div class="filters">
        <div class="filter-group">
            <label>Role:</label>
            <select class="filter-dropdown" id="roleFilter" onchange="filterUsers()">
                <option value="all">All Roles</option>
                <option value="shs">SHS</option>
                <option value="college student">College Student</option>
                <option value="employee">Employee</option>
            </select>
        </div>

        <div class="filter-group">
            <label>Program/Position:</label>
            <select class="filter-dropdown" id="programFilter" onchange="filterUsers()">
                <option value="all">All Programs/Positions</option>
                <option value="stem">STEM</option>
                <option value="humms">HUMMS</option>
                <option value="abm">ABM</option>
                <option value="mawd">MAWD</option>
                <option value="da">DA</option>
                <option value="toper">Toper</option>
                <option value="ca">CA</option>
                <option value="bscs">BSCS</option>
                <option value="bsit">BSIT</option>
                <option value="bscpe">BSCPE</option>
                <option value="bscm">BSCM</option>
                <option value="bstm">BSTM</option>
                <option value="bsba">BSBA</option>
                <option value="bmma">BMMA</option>
                <option value="teacher">Teacher</option>
                <option value="pamo">PAMO</option>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
            </select>
        </div>

        <div class="filter-group">
            <label>Status:</label>
            <select class="filter-dropdown" id="statusFilter" onchange="filterUsers()">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <div class="search-container">
        <input type="text" class="search-bar" placeholder="Search by name..." id="searchInput">
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
                <th onclick="sortTable(0)">First Name</th>
                <th onclick="sortTable(1)">Last Name</th>
                <th onclick="sortTable(2)">ID Number</th>
                <th onclick="sortTable(3)">Role</th>
                <th onclick="sortTable(4)">Program/Position</th>
                <th onclick="sortTable(5)">Email</th>
                <th>Password</th>
                <th onclick="sortTable(7)">Status</th>
                <th onclick="sortTable(8)">Date Created</th>
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
                echo "<td>" . htmlspecialchars($account['role_category']) . "</td>";
                echo "<td>" . htmlspecialchars($account['program_or_position']) . "</td>";
                echo "<td>" . htmlspecialchars($account['email']) . "</td>";
                echo "<td>********</td>";
                echo "<td>" . htmlspecialchars($account['status']) . "</td>";
                echo "<td>" . htmlspecialchars($account['date_created']) . "</td>";

                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

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

    function filterUsers() {
        const roleFilter = document.getElementById('roleFilter').value.toLowerCase();
        const programFilter = document.getElementById('programFilter').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const tableRows = document.querySelectorAll('.table tbody tr');

        tableRows.forEach(row => {
            const firstName = row.cells[0].textContent.toLowerCase();
            const lastName = row.cells[1].textContent.toLowerCase();
            const role = row.cells[3].textContent.toLowerCase();
            const program = row.cells[4].textContent.toLowerCase();
            const status = row.cells[7].textContent.toLowerCase();

            const matchesRole = roleFilter === 'all' || role === roleFilter;
            const matchesProgram = programFilter === 'all' || program === programFilter;
            const matchesStatus = statusFilter === 'all' || status === statusFilter;
            const matchesSearch = firstName.includes(searchTerm) || lastName.includes(searchTerm);

            if (matchesRole && matchesProgram && matchesStatus && matchesSearch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function sortTable(columnIndex) {
        const table = document.querySelector('.table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const currentOrder = tbody.dataset.order || 'asc';

        rows.sort((a, b) => {
            const aValue = a.cells[columnIndex].textContent.toLowerCase();
            const bValue = b.cells[columnIndex].textContent.toLowerCase();

            if (currentOrder === 'asc') {
                return aValue.localeCompare(bValue);
            } else {
                return bValue.localeCompare(aValue);
            }
        });

        // Toggle sort order for next click
        tbody.dataset.order = currentOrder === 'asc' ? 'desc' : 'asc';

        // Clear tbody and append sorted rows
        tbody.innerHTML = '';
        rows.forEach(row => tbody.appendChild(row));
    }

    // Add this to your existing DOMContentLoaded event listener
    document.addEventListener('DOMContentLoaded', function () {
        // Initial filter
        filterUsers();

        // Add event listeners for all filters
        document.getElementById('searchInput').addEventListener('input', filterUsers);
        document.getElementById('roleFilter').addEventListener('change', filterUsers);
        document.getElementById('programFilter').addEventListener('change', filterUsers);
        document.getElementById('statusFilter').addEventListener('change', filterUsers);
    });

    let selectedUserId = null;

    document.querySelectorAll('.account-row').forEach(row => {
        row.addEventListener('click', function () {
            // Check if this row is already selected
            const isSelected = this.classList.contains('selected');

            // Remove selection from all rows
            document.querySelectorAll('.account-row').forEach(r => r.classList.remove('selected'));

            if (isSelected) {
                // If row was already selected, deselect it
                this.classList.remove('selected');
                selectedUserId = null;
                // Disable buttons
                document.getElementById('changePasswordBtn').disabled = true;
                document.getElementById('updateStatusBtn').disabled = true;
            } else {
                // If row wasn't selected, select it
                this.classList.add('selected');
                selectedUserId = this.dataset.id;
                // Enable buttons
                document.getElementById('changePasswordBtn').disabled = false;
                document.getElementById('updateStatusBtn').disabled = false;
            }
        });
    });

    function changePassword() {
        if (!selectedUserId) {
            alert('Please select an account first.');
            return;
        }
        window.location.href = `change_password.php?id=${selectedUserId}`;
    }

    function updateStatus() {
        if (!selectedUserId) {
            alert('Please select an account first.');
            return;
        }
        document.getElementById('selectedUserId').value = selectedUserId;
        document.getElementById('updateStatusModal').style.display = 'block';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    function closeSuccessModal() {
        document.getElementById('successModal').style.display = 'none';
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

                    // Update the status in the table immediately without reload
                    const row = document.querySelector(`tr[data-id="${formData.get('userId')}"]`);
                    if (row) {
                        row.cells[7].textContent = formData.get('status'); // Make sure the index matches your status column
                    }

                    // Clear selection
                    selectedUserId = null;
                    document.querySelectorAll('.account-row').forEach(r => r.classList.remove('selected'));
                    document.getElementById('changePasswordBtn').disabled = true;
                    document.getElementById('updateStatusBtn').disabled = true;
                } else {
                    alert('Error updating status: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating status. Please try again.');
            });
    });

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
            }
            .modal-content {
                background: white;
                padding: 25px;
                border-radius: 8px;
                box-shadow: 0 2px 15px rgba(0,0,0,0.2);
                width: 90%;
                max-width: 400px;
                position: relative;
                margin: 15% auto;
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

    .filters {
        display: flex;
        gap: 20px;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-dropdown {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        min-width: 150px;
    }

    .sort-icon {
        font-size: 18px;
        vertical-align: middle;
        margin-left: 5px;
        cursor: pointer;
    }

    th {
        cursor: pointer;
        position: relative;
        padding: 12px;
        transition: background-color 0.2s;
    }

    th:hover {
        background-color: #f5f5f5;
    }

    /* Remove any existing sort icon styles */
    .sort-icon {
        display: none;
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
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">