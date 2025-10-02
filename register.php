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
  <title>dailydo - Register</title>
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
        <li><a href="login.php">Login</a></li>
      </ul>
    </div>
  </nav>
  
  <!-- REGISTER FORM -->
  <section class="register-section">
    <div class="register-container">
      <h2>Welcome!</h2>
      <p>Create an account to organize your tasks</p>
      <form id="registerForm" class="register-form">
        <div class="form-group">
          <input type="text" name="firstName" placeholder="First Name" required>
        </div>
        <div class="form-group">
          <input type="text" name="lastName" placeholder="Last Name" required>
        </div>
        <div class="form-group">
          <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group">
          <input type="password" name="password" placeholder="Password" required>
        </div>
        <div class="form-group">
          <input type="password" name="confirm" placeholder="Confirm Password" required>
        </div>
        <button type="submit" class="register-btn">Register</button>
      </form>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <p>&copy; 2025 dailydo. All rights reserved.</p>
  </footer>

  <script>
    document.getElementById('registerForm').addEventListener('submit', async (e) => {
      e.preventDefault();

      const formData = new FormData(e.target);
      const firstName = formData.get('firstName');
      const lastName = formData.get('lastName');
      const email = formData.get('email');
      const password = formData.get('password');
      const confirm = formData.get('confirm');

      if (password !== confirm) {
        Swal.fire({ icon: 'error', title: 'Oops...', text: 'Passwords do not match!' });
        return;
      }

      try {
        const res = await fetch('api/register.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ firstName, lastName, email, password })
        });

        const data = await res.json();

        if (data.success) {
          // Save registration data to localStorage for profile page
          const registrationData = {
            firstName: firstName,
            lastName: lastName,
            email: email,
            registrationDate: new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' })
          };
          localStorage.setItem('registrationData', JSON.stringify(registrationData));
          
          Swal.fire({
            icon: 'success',
            title: 'Registered!',
            text: data.message
          }).then(() => {
            window.location.href = 'dashboard.php';
          });
        } else {
          Swal.fire({ icon: 'error', title: 'Registration Failed', text: data.message });
        }
      } catch (err) {
        Swal.fire({ icon: 'error', title: 'Server Error', text: err.message });
      }
    });
  </script>
</body>
</html>
