<?php
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>dailydo - Home</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="stylesheet" href="assets/style.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
        <script defer src="assets/script.js"></script>
    </head>
    <body>
        <!--NAVBAR-->
        <nav class="navbar">
            <div class="navbar-container">
              <a href="index.php" class="navbar-logo">dailydo</a>
              <button class="navbar-toggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
              </button>
              <ul class="navbar-menu">
                <?php if (isLoggedIn()): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="#" onclick="logout(); return false;" class="logout-btn">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
              </ul>
            </div>
        </nav>
        <!-- HERO SECTION -->
        <section class="hero">
            <div class="hero-content">
            <h1><i class="bi bi-stars me-2"></i>Organize Your Day, Stress-Free</h1>
            <p>Stay productive and never miss a task again with our simple and powerful To-Do List app.</p>
            </div>
        </section>

        <!-- FEATURES -->
        <section class="features container py-5">
            <div class="row text-center">
            <div class="col-md-4">
                <h3><i class="bi bi-check-circle-fill text-success me-2"></i>Easy to Use</h3>
                <p>Create, edit, and manage tasks in just a few clicks.</p>
            </div>
            <div class="col-md-4">
                <h3><i class="bi bi-calendar-check text-primary me-2"></i>Stay on Track</h3>
                <p>Set deadlines, reminders, and priorities to never miss a task.</p>
            </div>
            <div class="col-md-4">
                <h3><i class="bi bi-phone text-info me-2"></i>Accessible Anywhere</h3>
                <p>Use it on desktop, tablet, or mobile seamlessly.</p>
            </div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="footer">
            <p>&copy; 2025 dailydo. All rights reserved.</p>
        </footer>

        <script>
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