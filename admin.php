<?php
require_once 'includes/auth.php';

// Require admin access
requireAdmin();
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - DailyDo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .user-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }
        .user-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #896C6C;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .admin-header {
            background: linear-gradient(135deg, #896C6C, #A67C7C);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .stats-card {
            background: #F1F0E4;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            border: 2px solid #896C6C;
        }
        .modal-header {
            background: #896C6C;
            color: white;
        }
        .info-label {
            font-weight: 600;
            color: #896C6C;
        }
    </style>
</head>
<body>
    <!-- SIDEBAR OVERLAY -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4 class="sidebar-brand">DailyDo</h4>
            <button class="sidebar-toggle d-lg-none" id="sidebarToggle">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">
                        <i class="bi bi-person-circle me-2"></i>
                        Profile
                    </a>
                </li>
                <?php if (!isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="tasks.php">
                        <i class="bi bi-list-task me-2"></i>
                        Task List
                    </a>
                </li>
                <li class="nav-item">
          <a class="nav-link" href="calendar.php">
            <i class="bi bi-calendar-check me-2"></i>
            Calendar
          </a>
        </li>
                <?php endif; ?>
                <?php if (isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link active" href="admin.php">
                        <i class="bi bi-people me-2"></i>
                        User Management
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a class="nav-link logout-link" href="#" onclick="logout()">
                <i class="bi bi-box-arrow-right me-2"></i>
                Logout
            </a>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <!-- NAVBAR -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div class="container-fluid">
                <button class="navbar-toggler d-lg-none me-2" type="button" id="mobileMenuToggle">
                    <i class="bi bi-list"></i>
                </button>
                <a class="navbar-brand fw-bold d-lg-none" href="dashboard.php">DailyDo</a>
                <div class="ms-auto">
                    <span class="navbar-text d-none d-lg-inline">Admin Panel</span>
                </div>
            </div>
        </nav>

        <!-- ADMIN CONTENT -->
        <section class="dashboard-section mt-3">
            <div class="container-fluid px-3 py-3" style="max-width: 1400px;">
                <!-- Admin Header -->
                <div class="admin-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="fw-bold mb-2">
                                <i class="bi bi-shield-check me-2"></i>User Management
                            </h2>
                            <p class="mb-0">Manage and monitor all registered users</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <i class="bi bi-people-fill" style="font-size: 2.5rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>

                <!-- Stats Row -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <i class="bi bi-people fs-1 mb-2" style="color: #896C6C;"></i>
                            <h3 class="fw-bold" id="totalUsers">0</h3>
                            <p class="mb-0">Total Users</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <i class="bi bi-person-check fs-1 mb-2" style="color: #896C6C;"></i>
                            <h3 class="fw-bold" id="activeUsers">0</h3>
                            <p class="mb-0">Active Users</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <i class="bi bi-calendar-plus fs-1 mb-2" style="color: #896C6C;"></i>
                            <h3 class="fw-bold" id="newUsers">0</h3>
                            <p class="mb-0">New This Month</p>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="searchUsers" placeholder="Search users by name or email...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-primary" onclick="refreshUserList()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                        </button>
                    </div>
                </div>

                <!-- Users List -->
                <div class="row">
                    <div class="col-12">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-list-ul me-2"></i>Registered Users
                        </h5>
                        <div id="usersList">
                            <!-- Users will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="footer">
            <p>&copy; 2025 DailyDo. All rights reserved.</p>
        </footer>
    </div>

    <!-- User Details Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-person-lines-fill me-2"></i>User Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- View Mode -->
                    <div id="viewMode">
                        <div class="row">
                            <div class="col-md-4 text-center mb-4">
                                <div class="user-avatar mx-auto mb-3" id="modalUserAvatar" style="width: 100px; height: 100px; font-size: 2.5rem;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <h5 id="modalUserName">-</h5>
                                <p class="text-muted" id="modalUserEmail">-</p>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-sm-6 mb-3">
                                        <label class="info-label">First Name:</label>
                                        <p id="modalFirstName">-</p>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label class="info-label">Last Name:</label>
                                        <p id="modalLastName">-</p>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label class="info-label">Email Address:</label>
                                        <p id="modalEmail">-</p>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label class="info-label">Registration Date:</label>
                                        <p id="modalRegDate">-</p>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label class="info-label">Status:</label>
                                        <p id="modalStatus"><span class="badge bg-success">Active</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Mode -->
                    <div id="editMode" style="display: none;">
                        <form id="editUserForm">
                            <input type="hidden" id="editUserId" name="id">
                            <div class="row">
                                <div class="col-md-4 text-center mb-4">
                                    <div class="user-avatar mx-auto mb-3" id="editUserAvatar" style="width: 100px; height: 100px; font-size: 2.5rem;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <h6 class="text-muted">User Avatar</h6>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label for="editUsername" class="form-label info-label">Username:</label>
                                            <input type="text" class="form-control" id="editUsername" name="username" required>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="editEmail" class="form-label info-label">Email Address:</label>
                                            <input type="email" class="form-control" id="editEmail" name="email" required>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <label class="info-label">Registration Date:</label>
                                            <p id="editRegDate" class="form-control-plaintext">-</p>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <label class="info-label">Status:</label>
                                            <p class="form-control-plaintext"><span class="badge bg-success">Active</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- View Mode Buttons -->
                    <div id="viewModeButtons">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i>Close
                        </button>
                        <button type="button" class="btn btn-primary" onclick="enterEditMode()">
                            <i class="bi bi-pencil me-1"></i>Edit User
                        </button>
                    </div>
                    
                    <!-- Edit Mode Buttons -->
                    <div id="editModeButtons" style="display: none;">
                        <button type="button" class="btn btn-secondary" onclick="cancelEdit()">
                            <i class="bi bi-x-lg me-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-danger" onclick="deleteUser()">
                            <i class="bi bi-trash me-1"></i>Delete User
                        </button>
                        <button type="button" class="btn btn-success" onclick="saveUser()">
                            <i class="bi bi-check-lg me-1"></i>Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Real user data from database
        let users = [];
        let currentUser = null;

        // Initialize admin panel
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();
            loadStats();
            setupSearch();
        });

        // Load users from database via API
        async function loadUsers() {
            try {
                const response = await fetch('api/admin.php?action=users');
                const data = await response.json();
                
                if (data.success) {
                    users = data.users;
                    displayUsers();
                } else {
                    console.error('Failed to load users:', data.message);
                    showError('Failed to load users: ' + data.message);
                }
            } catch (error) {
                console.error('Error loading users:', error);
                showError('Error loading users. Please try again.');
            }
        }

        // Load statistics from database via API
        async function loadStats() {
            try {
                const response = await fetch('api/admin.php?action=stats');
                const data = await response.json();
                
                if (data.success) {
                    updateStats(data.stats);
                } else {
                    console.error('Failed to load stats:', data.message);
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        // Show error message
        function showError(message) {
            const usersList = document.getElementById('usersList');
            usersList.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-triangle" style="font-size: 4rem; color: #dc3545;"></i>
                    <h5 class="text-danger mt-3">Error</h5>
                    <p class="text-muted">${message}</p>
                    <button class="btn btn-primary" onclick="loadUsers()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Retry
                    </button>
                </div>
            `;
        }

        // Display users in the list
        function displayUsers(filteredUsers = null) {
            const usersList = document.getElementById('usersList');
            const usersToShow = filteredUsers || users;
            
            if (usersToShow.length === 0) {
                usersList.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-people" style="font-size: 4rem; color: #ccc;"></i>
                        <h5 class="text-muted mt-3">No users found</h5>
                        <p class="text-muted">No registered users match your search criteria.</p>
                    </div>
                `;
                return;
            }
            
            usersList.innerHTML = usersToShow.map(user => {
                // Get initials for avatar
                const firstInitial = user.firstName ? user.firstName.charAt(0).toUpperCase() : '';
                const lastInitial = user.lastName ? user.lastName.charAt(0).toUpperCase() : '';
                const avatarText = firstInitial + lastInitial || user.username.charAt(0).toUpperCase();
                
                // Get display name
                const displayName = user.firstName && user.lastName 
                    ? `${user.firstName} ${user.lastName}`
                    : user.username;
                
                return `
                    <div class="user-card" onclick="showUserDetails(${user.id})">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="user-avatar">
                                    ${avatarText}
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="mb-1 fw-bold">${displayName}</h6>
                                <p class="mb-1 text-muted">${user.email}</p>
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>Joined: ${user.registrationDate}
                                </small>
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-success">${user.status}</span>
                                <i class="bi bi-chevron-right ms-2 text-muted"></i>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Update statistics
        function updateStats(stats = null) {
            if (stats) {
                // Use stats from API
                document.getElementById('totalUsers').textContent = stats.totalUsers;
                document.getElementById('activeUsers').textContent = stats.activeUsers;
                document.getElementById('newUsers').textContent = stats.newUsers;
            } else {
                // Fallback to calculating from current users array
                document.getElementById('totalUsers').textContent = users.length;
                document.getElementById('activeUsers').textContent = users.filter(u => u.status === 'Active').length;
                
                // Count new users this month
                const currentMonth = new Date().getMonth();
                const currentYear = new Date().getFullYear();
                const newThisMonth = users.filter(user => {
                    const regDate = new Date(user.registrationDate);
                    return regDate.getMonth() === currentMonth && regDate.getFullYear() === currentYear;
                }).length;
                document.getElementById('newUsers').textContent = newThisMonth;
            }
        }

        // Show user details in modal
        function showUserDetails(userId) {
            currentUser = users.find(u => u.id === userId);
            if (!currentUser) return;
            
            // Reset modal to view mode
            document.getElementById('viewMode').style.display = 'block';
            document.getElementById('editMode').style.display = 'none';
            document.getElementById('viewModeButtons').style.display = 'block';
            document.getElementById('editModeButtons').style.display = 'none';
            
            // Get initials for avatar
            const firstInitial = currentUser.firstName ? currentUser.firstName.charAt(0).toUpperCase() : '';
            const lastInitial = currentUser.lastName ? currentUser.lastName.charAt(0).toUpperCase() : '';
            const avatarText = firstInitial + lastInitial || currentUser.username.charAt(0).toUpperCase();
            
            // Get display name
            const displayName = currentUser.firstName && currentUser.lastName 
                ? `${currentUser.firstName} ${currentUser.lastName}`
                : currentUser.username;
            
            // Populate modal with user data
            document.getElementById('modalUserAvatar').innerHTML = avatarText;
            document.getElementById('modalUserName').textContent = displayName;
            document.getElementById('modalUserEmail').textContent = currentUser.email;
            document.getElementById('modalFirstName').textContent = currentUser.firstName || 'Not provided';
            document.getElementById('modalLastName').textContent = currentUser.lastName || 'Not provided';
            document.getElementById('modalEmail').textContent = currentUser.email;
            document.getElementById('modalRegDate').textContent = currentUser.registrationDate;
            document.getElementById('modalStatus').innerHTML = 
                `<span class="badge bg-success">${currentUser.status}</span>`;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('userModal'));
            modal.show();
        }

        // Setup search functionality
        function setupSearch() {
            const searchInput = document.getElementById('searchUsers');
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                if (searchTerm === '') {
                    displayUsers();
                } else {
                    const filteredUsers = users.filter(user => 
                        user.firstName.toLowerCase().includes(searchTerm) ||
                        user.lastName.toLowerCase().includes(searchTerm) ||
                        user.email.toLowerCase().includes(searchTerm)
                    );
                    displayUsers(filteredUsers);
                }
            });
        }

        // Refresh user list
        function refreshUserList() {
            loadUsers();
            loadStats();
            document.getElementById('searchUsers').value = '';
        }

        // Enter edit mode
        function enterEditMode() {
            // Hide view mode elements and show edit mode elements
            document.getElementById('viewMode').style.display = 'none';
            document.getElementById('editMode').style.display = 'block';
            document.getElementById('viewModeButtons').style.display = 'none';
            document.getElementById('editModeButtons').style.display = 'block';
            
            // Populate edit form with current user data
            document.getElementById('editUsername').value = currentUser.username;
            document.getElementById('editEmail').value = currentUser.email;
        }

        // Cancel edit mode
        function cancelEdit() {
            // Show view mode elements and hide edit mode elements
            document.getElementById('viewMode').style.display = 'block';
            document.getElementById('editMode').style.display = 'none';
            document.getElementById('viewModeButtons').style.display = 'block';
            document.getElementById('editModeButtons').style.display = 'none';
        }

        // Save user changes
        async function saveUser() {
            const username = document.getElementById('editUsername').value.trim();
            const email = document.getElementById('editEmail').value.trim();
            
            // Validate input
            if (!username || !email) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please fill in all required fields.'
                });
                return;
            }
            
            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Email',
                    text: 'Please enter a valid email address.'
                });
                return;
            }
            
            try {
                const response = await fetch('api/admin.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: currentUser.id,
                        username: username,
                        email: email
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'User updated successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Update current user data
                    currentUser.username = username;
                    currentUser.email = email;
                    
                    // Update the view mode display
                    document.getElementById('modalUserEmail').textContent = email;
                    document.getElementById('modalEmail').textContent = email;
                    
                    // Exit edit mode
                    cancelEdit();
                    
                    // Refresh the user list
                    loadUsers();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to update user.'
                    });
                }
            } catch (error) {
                console.error('Error updating user:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the user.'
                });
            }
        }

        // Delete user
        async function deleteUser() {
            // Confirm deletion
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: `This will permanently delete the user "${currentUser.username}" and all their tasks. This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete user!',
                cancelButtonText: 'Cancel'
            });
            
            if (!result.isConfirmed) {
                return;
            }
            
            try {
                const response = await fetch('api/admin.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: currentUser.id
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'User has been deleted successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('userModal'));
                    modal.hide();
                    
                    // Refresh the user list
                    loadUsers();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to delete user.'
                    });
                }
            } catch (error) {
                console.error('Error deleting user:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while deleting the user.'
                });
            }
        }

        // Logout function
        async function logout() {
            try {
                await fetch('api/logout.php', { method: 'POST' });
                localStorage.removeItem('profileData');
                localStorage.removeItem('userSession');
                window.location.href = 'index.php';
            } catch (error) {
                console.error('Logout error:', error);
                window.location.href = 'index.php';
            }
        }
    </script>
</body>
</html>