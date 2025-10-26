<?php
require_once 'includes/auth.php';

// Require login for profile
requireLogin();
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - dailydo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .profile-picture {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #896C6C;
        }
        .profile-card {
            background: #F1F0E4;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stats-card {
            background: #896C6C;
            color: white;
            border-radius: 12px;
            padding: 16px 20px;
            transition: all 0.2s ease;
            min-height: 90px;
        }
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(137, 108, 108, 0.3);
        }
        .form-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .profile-picture-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #896C6C;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            border: 3px solid #896C6C;
        }
        .file-upload-btn {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        .file-upload-btn input[type=file] {
            position: absolute;
            left: -9999px;
        }
        .badge {
            font-size: 0.75rem;
            padding: 0.4em 0.6em;
        }
        .admin-header {
            background: linear-gradient(135deg, #896C6C, #A67C7C);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
        }
        @media (max-width: 768px) {
            .stats-card {
                min-height: 80px;
                padding: 16px 12px;
            }
            .profile-picture, .profile-picture-placeholder {
                width: 100px;
                height: 100px;
            }
            .form-section {
                padding: 20px;
            }
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
                    <a class="nav-link active" href="profile.php">
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
          <a class="nav-link" href="calendar.php">
            <i class="bi bi-calendar-check me-2"></i>
            Calendar
          </a>
        </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin.php">
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
                    <span class="navbar-text d-none d-lg-inline">Profile</span>
                </div>
            </div>
        </nav>

        <!-- PROFILE CONTENT -->
        <section class="dashboard-section mt-3">
            <div class="container-fluid px-3 py-3" style="max-width: 1400px;">
                <!-- Header -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="admin-header">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h2 class="fw-bold mb-2">
                                        <i class="bi bi-person-circle me-2"></i>Profile
                                    </h2>
                                    <p class="mb-0">Manage your account information and preferences</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <i class="bi bi-person-fill" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alert Messages -->
                <div id="alertContainer"></div>

                <div class="row g-3">
                    <!-- Profile Information Card -->
                    <div class="col-12 col-xl-3 col-lg-4">
                        <div class="profile-card p-4 text-center h-100">
                            <div class="mb-3 d-flex justify-content-center" style="margin-top: 35px;">
                                <img id="profilePicture" src="" alt="Profile Picture" class="profile-picture" style="display: none;">
                                <div id="profilePlaceholder" class="profile-picture-placeholder">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                            </div>
                            <h5 class="fw-bold mb-2" id="displayName">Loading...</h5>
                            <p class="text-muted mb-2 small" id="displayEmail">Loading...</p>
                            <p class="small text-muted mb-3" id="memberSince">Loading...</p>
                            
                            <!-- Profile Picture Upload -->
                            <div class="mb-4">
                                <label class="file-upload-btn btn btn-outline-dark btn-sm">
                                    <i class="bi bi-camera me-1"></i>Update Picture
                                    <input type="file" id="profilePictureInput" accept="image/*">
                                </label>
                            </div>

                            <!-- Activity Stats -->
                            <div class="d-flex justify-content-center" style="margin-top: 35px;">
                                <div class="stats-card py-4 px-4 d-flex flex-row align-items-center justify-content-center gap-3" style="width: 100%; max-width: 280px; min-height: 90px;">
                                     <i class="bi bi-fire fs-1"></i>
                                     <div class="text-start">
                                         <h3 class="fw-bold mb-0" id="currentStreak">0</h3>
                                         <span class="fs-5">Streak</span>
                                         <p class="text-white mb-1 small" id="streakInfo">without missing a task</p>
                                     </div>
                                 </div>
                            </div>
                        </div>
                    </div>

                    <!-- Forms and Bio Section -->
                    <div class="col-12 col-xl-9 col-lg-8">
                        <div class="row g-3">
                            <!-- Bio/Interests Section -->
                            <div class="col-12 col-lg-6">
                                <div class="form-section h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="fw-bold mb-0">
                                            <i class="bi bi-chat-text me-2"></i>Bio & Interests
                                        </h5>
                                        <button class="btn btn-outline-secondary btn-sm" id="editBioBtn" style="border-color: #896C6C; color: #896C6C;">
                                            <i class="bi bi-pencil me-1"></i>Edit
                                        </button>
                                    </div>
                                    
                                    <!-- Bio Display Mode -->
                                    <div id="bioDisplay">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">About Me</label>
                                            <p class="form-control-plaintext" id="displayBio">Tell us about yourself...</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Interests</label>
                                            <div id="displayInterests" class="d-flex flex-wrap gap-1">
                                                <span class="badge bg-secondary">Add your interests</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Bio Edit Mode -->
                                    <div id="bioEdit" style="display: none;">
                                        <form id="bioForm">
                                            <div class="mb-3">
                                                <label for="bio" class="form-label">About Me</label>
                                                <textarea class="form-control" id="bio" rows="3" placeholder="Tell us about yourself..."></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="interests" class="form-label">Interests (comma-separated)</label>
                                                <input type="text" class="form-control" id="interests" placeholder="e.g., Reading, Coding, Travel">
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-sm" style="background: #896C6C; border: none; color: white;">
                                                    <i class="bi bi-check-lg me-1"></i>Save
                                                </button>
                                                <button type="button" class="btn btn-secondary btn-sm" id="cancelBioEdit">
                                                    <i class="bi bi-x-lg me-1"></i>Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Information Display/Edit -->
                            <div class="col-12 col-lg-6">
                                <div class="form-section h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="fw-bold mb-0">
                                            <i class="bi bi-person-gear me-2"></i>Profile Information
                                        </h5>
                                        <button class="btn btn-outline-secondary btn-sm" id="editProfileBtn" style="border-color: #896C6C; color: #896C6C;">
                                            <i class="bi bi-pencil me-1"></i>Edit
                                        </button>
                                    </div>
                            
                                    <!-- Display Mode -->
                                    <div id="profileDisplay">
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-semibold">First Name</label>
                                                <p class="form-control-plaintext" id="displayFirstName">-</p>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-semibold">Last Name</label>
                                                <p class="form-control-plaintext" id="displayLastName">-</p>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Email Address</label>
                                            <p class="form-control-plaintext" id="displayEmailAddress">-</p>
                                        </div>
                                    </div>
                            
                                    <!-- Edit Mode -->
                                    <div id="profileEdit" style="display: none;">
                                        <form id="profileForm">
                                            <div class="mb-3">
                                                <label for="firstName" class="form-label">First Name</label>
                                                <input type="text" class="form-control" id="firstName" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="lastName" class="form-label">Last Name</label>
                                                <input type="text" class="form-control" id="lastName" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email Address</label>
                                                <input type="email" class="form-control" id="email" required>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-sm" style="background: #896C6C; border: none; color: white;">
                                                    <i class="bi bi-check-lg me-1"></i>Save Changes
                                                </button>
                                                <button type="button" class="btn btn-secondary btn-sm" id="cancelProfileEdit">
                                                    <i class="bi bi-x-lg me-1"></i>Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Password Management -->
                            <div class="col-12">
                                <div class="form-section">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="fw-bold mb-0">
                                            <i class="bi bi-shield-lock me-2"></i>Password Management
                                        </h5>
                                        <button class="btn btn-outline-warning btn-sm" id="editPasswordBtn">
                                            <i class="bi bi-key me-1"></i>Change Password
                                        </button>
                                    </div>
                            
                                    <!-- Display Mode -->
                                    <div id="passwordDisplay">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Current Password</label>
                                            <p class="form-control-plaintext">••••••••••••</p>
                                        </div>
                                    </div>
                            
                                    <!-- Edit Mode -->
                                    <div id="passwordEdit" style="display: none;">
                                        <form id="passwordForm">
                                            <div class="mb-3">
                                                <label for="currentPassword" class="form-label">Current Password</label>
                                                <input type="password" class="form-control" id="currentPassword" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="newPassword" class="form-label">New Password</label>
                                                    <input type="password" class="form-control" id="newPassword" minlength="6" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                                    <input type="password" class="form-control" id="confirmPassword" minlength="6" required>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-warning btn-sm">
                                                    <i class="bi bi-key me-1"></i>Change Password
                                                </button>
                                                <button type="button" class="btn btn-secondary btn-sm" id="cancelPasswordEdit">
                                                    <i class="bi bi-x-lg me-1"></i>Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Profile data management
        let profileData = {};

        // Load profile data from API
        async function loadProfileData() {
            try {
                const response = await fetch('api/profile-clean.php');
                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.error || 'Failed to load profile data');
                }
                
                profileData = data.user;
                updateProfileDisplay();
            } catch (error) {
                console.error('Error loading profile data:', error);
                showAlert('Failed to load profile data: ' + error.message, 'error');
            }
        }

        // Save profile data to API
        async function saveProfileData(updateData) {
            try {
                const response = await fetch('api/profile-clean.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(updateData)
                });
                
                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.error || 'Failed to save profile data');
                }
                
                return data;
            } catch (error) {
                console.error('Error saving profile data:', error);
                throw error;
            }
        }

        // Update profile display
        function updateProfileDisplay() {
            // Update header
            document.getElementById('displayName').textContent = `${profileData.first_name || ''} ${profileData.last_name || ''}`.trim();
            document.getElementById('displayEmail').textContent = profileData.email || '';
            
            // Format member since date
            const memberSince = profileData.created_at ? new Date(profileData.created_at).toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long' 
            }) : 'Unknown';
            document.getElementById('memberSince').textContent = `Member since ${memberSince}`;
            
            // Update display mode fields
            document.getElementById('displayFirstName').textContent = profileData.first_name || 'Not set';
            document.getElementById('displayLastName').textContent = profileData.last_name || 'Not set';
            document.getElementById('displayEmailAddress').textContent = profileData.email || '';
            
            // Update form fields
            document.getElementById('firstName').value = profileData.first_name || '';
            document.getElementById('lastName').value = profileData.last_name || '';
            document.getElementById('email').value = profileData.email || '';
            
            // Update bio and interests
            document.getElementById('displayBio').textContent = profileData.bio || 'Tell us about yourself...';
            document.getElementById('bio').value = profileData.bio || '';
            
            // Handle interests (stored as comma-separated string in database)
            const interests = (profileData.interests && typeof profileData.interests === 'string') 
                ? profileData.interests.split(',').map(i => i.trim()).filter(i => i) 
                : [];
            document.getElementById('interests').value = interests.join(', ');
            
            // Update interests display
            const interestsContainer = document.getElementById('displayInterests');
            if (interests.length > 0) {
                interestsContainer.innerHTML = interests.map(interest => 
                    `<span class="badge bg-primary">${interest.trim()}</span>`
                ).join(' ');
            } else {
                interestsContainer.innerHTML = '<span class="badge bg-secondary">Add your interests</span>';
            }
            
            // Update stats
            document.getElementById('currentStreak').textContent = profileData.current_streak || 0;
            
            // Update profile picture
            if (profileData.profile_picture) {
                document.getElementById('profilePicture').src = profileData.profile_picture;
                document.getElementById('profilePicture').style.display = 'block';
                document.getElementById('profilePlaceholder').style.display = 'none';
            }
        }

        // Show alert message
        function showAlert(message, type = 'success') {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const iconClass = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
            
            alertContainer.innerHTML = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="bi ${iconClass} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                const alert = alertContainer.querySelector('.alert');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        }

        // Edit/Cancel button handlers
        document.getElementById('editProfileBtn').addEventListener('click', function() {
            document.getElementById('profileDisplay').style.display = 'none';
            document.getElementById('profileEdit').style.display = 'block';
            this.style.display = 'none';
        });
        
        document.getElementById('cancelProfileEdit').addEventListener('click', function() {
            document.getElementById('profileEdit').style.display = 'none';
            document.getElementById('profileDisplay').style.display = 'block';
            document.getElementById('editProfileBtn').style.display = 'inline-block';
        });
        
        document.getElementById('editBioBtn').addEventListener('click', function() {
            document.getElementById('bioDisplay').style.display = 'none';
            document.getElementById('bioEdit').style.display = 'block';
            this.style.display = 'none';
        });
        
        document.getElementById('cancelBioEdit').addEventListener('click', function() {
            document.getElementById('bioEdit').style.display = 'none';
            document.getElementById('bioDisplay').style.display = 'block';
            document.getElementById('editBioBtn').style.display = 'inline-block';
        });
        
        document.getElementById('editPasswordBtn').addEventListener('click', function() {
            document.getElementById('passwordDisplay').style.display = 'none';
            document.getElementById('passwordEdit').style.display = 'block';
            this.style.display = 'none';
        });
        
        document.getElementById('cancelPasswordEdit').addEventListener('click', function() {
            document.getElementById('passwordEdit').style.display = 'none';
            document.getElementById('passwordDisplay').style.display = 'block';
            document.getElementById('editPasswordBtn').style.display = 'inline-block';
            document.getElementById('passwordForm').reset();
        });

        // Handle profile form submission
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const email = document.getElementById('email').value.trim();
            
            if (firstName && lastName && email) {
                try {
                    const updateData = {
                        first_name: firstName,
                        last_name: lastName,
                        email: email
                    };
                    
                    await saveProfileData(updateData);
                    
                    // Update local data
                    profileData.first_name = firstName;
                    profileData.last_name = lastName;
                    profileData.email = email;
                    
                    updateProfileDisplay();
                    showAlert('Profile updated successfully!');
                    
                    // Switch back to display mode
                    document.getElementById('profileEdit').style.display = 'none';
                    document.getElementById('profileDisplay').style.display = 'block';
                    document.getElementById('editProfileBtn').style.display = 'inline-block';
                } catch (error) {
                    showAlert('Failed to update profile: ' + error.message, 'error');
                }
            } else {
                showAlert('Please fill in all required fields.', 'error');
            }
        });
        
        // Handle bio form submission
        document.getElementById('bioForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const bio = document.getElementById('bio').value.trim();
            const interestsInput = document.getElementById('interests').value.trim();
            
            try {
                const updateData = {
                    bio: bio,
                    interests: interestsInput
                };
                
                await saveProfileData(updateData);
                
                // Update local data
                profileData.bio = bio;
                profileData.interests = interestsInput;
                
                updateProfileDisplay();
                showAlert('Bio and interests updated successfully!');
                
                // Switch back to display mode
                document.getElementById('bioEdit').style.display = 'none';
                document.getElementById('bioDisplay').style.display = 'block';
                document.getElementById('editBioBtn').style.display = 'inline-block';
            } catch (error) {
                showAlert('Failed to update bio: ' + error.message, 'error');
            }
        });

        // Handle password form submission
        document.getElementById('passwordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (!currentPassword || !newPassword || !confirmPassword) {
                showAlert('Please fill in all password fields.', 'error');
                return;
            }
            
            if (newPassword !== confirmPassword) {
                showAlert('New passwords do not match.', 'error');
                return;
            }
            
            if (newPassword.length < 6) {
                showAlert('New password must be at least 6 characters long.', 'error');
                return;
            }
            
            try {
                const response = await fetch('api/profile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        currentPassword: currentPassword,
                        newPassword: newPassword
                    })
                });
                
                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.error || 'Failed to update password');
                }
                
                showAlert('Password changed successfully!');
                document.getElementById('passwordForm').reset();
                
                // Switch back to display mode
                document.getElementById('passwordEdit').style.display = 'none';
                document.getElementById('passwordDisplay').style.display = 'block';
                document.getElementById('editPasswordBtn').style.display = 'inline-block';
            } catch (error) {
                showAlert('Failed to update password: ' + error.message, 'error');
            }
        });

        // Handle profile picture upload
        document.getElementById('profilePictureInput').addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = async function(e) {
                         try {
                             const updateData = {
                                 profile_picture: e.target.result
                             };
                             
                             await saveProfileData(updateData);
                             
                             // Update local data
                             profileData.profile_picture = e.target.result;
                            
                            updateProfileDisplay();
                            showAlert('Profile picture updated successfully!');
                        } catch (error) {
                            showAlert('Failed to update profile picture: ' + error.message, 'error');
                        }
                    };
                    reader.readAsDataURL(file);
                } else {
                    showAlert('Please upload a valid image file.', 'error');
                }
            }
        });

        // Password confirmation validation
        document.getElementById('confirmPassword').addEventListener('input', function() {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = this.value;
            
            if (newPassword !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });

        // Sidebar functionality
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const sidebarToggle = document.getElementById('sidebarToggle');
        
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function() {
                sidebar.classList.add('show');
                sidebarOverlay.classList.add('show');
            });
        }
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
        }
        
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
        }
        
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            }
        });

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

        // Initialize profile on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadProfileData();
        });
    </script>
</body>
</html>