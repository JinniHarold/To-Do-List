<?php
require_once 'includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>dailydo - Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <!-- NAVBAR -->
  <nav class="navbar">
    <div class="navbar-container">
      <a href="<?php echo isLoggedIn() ? 'dashboard.php' : 'index.php'; ?>" class="navbar-logo">dailydo</a>
      <button class="navbar-toggle">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </button>
      <ul class="navbar-menu">
        <li><a href="index.php">Home</a></li>
        <li><a href="register.php">Register</a></li>
        <?php if (isLoggedIn()): ?>
        <li><a href="#" onclick="logout()" style="color: #ff6b6b;">Logout</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>
  
  <!-- LOGIN FORM -->
  <section class="login-section">
    <div class="login-container">
      <h2>Welcome Back!</h2>
      <p>Log in to manage your tasks</p>
      <form id="loginForm" class="login-form">
        <div class="form-group">
          <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group">
          <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" class="login-btn">Login</button>
      </form>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <p>&copy; 2025 dailydo. All rights reserved.</p>
  </footer>

  <script>
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
      e.preventDefault();

      const formData = new FormData(e.target);
      const email = formData.get('email');
      const password = formData.get('password');

      try {
        const res = await fetch('api/login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email, password })
        });

        const data = await res.json();

        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Login Successful',
            text: data.message
          }).then(() => {
            window.location.href = 'dashboard.php';
          });
        } else {
          Swal.fire({ icon: 'error', title: 'Login Failed', text: data.message });
        }
      } catch (err) {
        Swal.fire({ icon: 'error', title: 'Server Error', text: err.message });
      }
    });

    // Logout function
    async function logout() {
      try {
        const res = await fetch('api/logout.php', { method: 'POST' });
        const data = await res.json();
        
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Logged Out',
            text: 'You have been logged out successfully'
          }).then(() => {
            window.location.reload();
          });
        }
      } catch (error) {
        console.error('Logout error:', error);
        window.location.reload();
      }
    }
  </script>
</body>
</html>
