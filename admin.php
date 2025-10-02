<?php
require_once 'includes/auth.php';

// Require login for admin
requireLogin();
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
                    <a class="nav-link" href="#">
                        <i class="bi bi-check-circle me-2"></i>
                        Completed
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="admin.php">
                        <i class="bi bi-people me-2"></i>
                        User Management
                    </a>
                </li>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Close
                    </button>
                    <button type="button" class="btn btn-primary" onclick="editUser()">
                        <i class="bi bi-pencil me-1"></i>Edit User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sample user data structure
        let users = [];
        let currentUser = null;

        // Initialize admin panel
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();
            setupSearch();
        });

        // Load users from localStorage and generate sample data if needed
        function loadUsers() {
            // Try to load existing users from localStorage
            const storedUsers = localStorage.getItem('allUsers');
            if (storedUsers) {
                users = JSON.parse(storedUsers);
            } else {
                // Generate sample users if none exist
                generateSampleUsers();
            }
            
            // Also check for registration data
            const registrationData = localStorage.getItem('registrationData');
            if (registrationData) {
                const regData = JSON.parse(registrationData);
                // Check if this user already exists
                const existingUser = users.find(u => u.email === regData.email);
                if (!existingUser) {
                    const newUser = {
                        id: Date.now(),
                        firstName: regData.firstName,
                        lastName: regData.lastName,
                        email: regData.email,
                        phone: generateRandomPhone(),
                        address: generateRandomAddress(),
                        registrationDate: regData.registrationDate || new Date().toLocaleDateString(),
                        status: 'Active'
                    };
                    users.push(newUser);
                    saveUsers();
                }
            }
            
            displayUsers();
            updateStats();
        }

        // Generate random phone number for demo
        function generateRandomPhone() {
            const areaCode = Math.floor(Math.random() * 900) + 100;
            const exchange = Math.floor(Math.random() * 900) + 100;
            const number = Math.floor(Math.random() * 9000) + 1000;
            return `+1 (${areaCode}) ${exchange}-${number}`;
        }

        // Generate random address for demo
        function generateRandomAddress() {
            const streets = ['Main St', 'Oak Ave', 'Pine Rd', 'Elm St', 'Maple Dr', 'Cedar Ln', 'Park Ave', 'First St'];
            const cities = ['New York, NY', 'Los Angeles, CA', 'Chicago, IL', 'Houston, TX', 'Phoenix, AZ', 'Philadelphia, PA'];
            const streetNum = Math.floor(Math.random() * 9999) + 1;
            const street = streets[Math.floor(Math.random() * streets.length)];
            const city = cities[Math.floor(Math.random() * cities.length)];
            const zip = Math.floor(Math.random() * 90000) + 10000;
            return `${streetNum} ${street}, ${city} ${zip}`;
        }

        // Generate sample users for demonstration
        function generateSampleUsers() {
            users = [
                {
                    id: 1,
                    firstName: 'John',
                    lastName: 'Doe',
                    email: 'john.doe@example.com',
                    phone: '+1 (555) 123-4567',
                    address: '123 Main St, New York, NY 10001',
                    registrationDate: '2024-12-15',
                    status: 'Active'
                },
                {
                    id: 2,
                    firstName: 'Jane',
                    lastName: 'Smith',
                    email: 'jane.smith@example.com',
                    phone: '+1 (555) 987-6543',
                    address: '456 Oak Ave, Los Angeles, CA 90210',
                    registrationDate: '2024-12-20',
                    status: 'Active'
                },
                {
                    id: 3,
                    firstName: 'Mike',
                    lastName: 'Johnson',
                    email: 'mike.johnson@example.com',
                    phone: '+1 (555) 456-7890',
                    address: '789 Pine Rd, Chicago, IL 60601',
                    registrationDate: '2025-01-05',
                    status: 'Active'
                }
            ];
            saveUsers();
        }

        // Save users to localStorage
        function saveUsers() {
            localStorage.setItem('allUsers', JSON.stringify(users));
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
            
            usersList.innerHTML = usersToShow.map(user => `
                <div class="user-card" onclick="showUserDetails(${user.id})">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="user-avatar">
                                ${user.firstName.charAt(0)}${user.lastName.charAt(0)}
                            </div>
                        </div>
                        <div class="col">
                            <h6 class="mb-1 fw-bold">${user.firstName} ${user.lastName}</h6>
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
            `).join('');
        }

        // Update statistics
        function updateStats() {
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

        // Show user details in modal
        function showUserDetails(userId) {
            currentUser = users.find(u => u.id === userId);
            if (!currentUser) return;
            
            // Populate modal with user data
            document.getElementById('modalUserAvatar').innerHTML = 
                `${currentUser.firstName.charAt(0)}${currentUser.lastName.charAt(0)}`;
            document.getElementById('modalUserName').textContent = 
                `${currentUser.firstName} ${currentUser.lastName}`;
            document.getElementById('modalUserEmail').textContent = currentUser.email;
            document.getElementById('modalFirstName').textContent = currentUser.firstName;
            document.getElementById('modalLastName').textContent = currentUser.lastName;
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
            document.getElementById('searchUsers').value = '';
        }

        // Edit user function (placeholder)
        function editUser() {
            alert('Edit user functionality would be implemented here.');
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