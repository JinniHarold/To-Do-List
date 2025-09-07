const express = require('express');
const mysql = require('mysql2');
const bcrypt = require('bcryptjs');
const bodyParser = require('body-parser');
const session = require('express-session');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 8000;

// Middleware
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json()); // <--- add this // ✅ allow JSON requests
app.use(express.static(__dirname)); // serve static files

// Sessions
app.use(session({
  secret: 'supersecretkey',
  resave: false,
  saveUninitialized: false
}));

// MySQL connection
const db = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: '', // set if you have a MySQL password in XAMPP
  database: 'todo_app'
});

db.connect(err => {
  if (err) {
    console.error('Database connection failed:', err);
  } else {
    console.log('Connected to MySQL.');
  }
});

// Routes
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'index.html'));
});

// Register route
app.post('/register', (req, res) => {
  const { username, email, password } = req.body;
  const hashedPassword = bcrypt.hashSync(password, 10);

  db.query(
    'INSERT INTO users (username, email, password) VALUES (?, ?, ?)',
    [username, email, hashedPassword],
    (err) => {
      if (err) {
        console.error(err);
        return res.status(500).json({ success: false, message: 'Error registering user.' });
      }
      res.json({ success: true, message: 'Registration successful!' });
    }
  );
});

// Login route
app.post('/login', (req, res) => {
  const { email, password } = req.body;

  db.query('SELECT * FROM users WHERE email = ?', [email], (err, results) => {
    if (err || results.length === 0) {
      return res.status(401).json({ success: false, message: 'User not found.' });
    }

    const user = results[0];
    if (bcrypt.compareSync(password, user.password)) {
      req.session.user = user;
      res.json({ success: true, message: `Welcome ${user.username}!` });
    } else {
      res.status(401).json({ success: false, message: 'Incorrect password.' });
    }
  });
});

// Dashboard route
app.get('/dashboard', (req, res) => {
  if (!req.session.user) {
    return res.redirect('/login.html');
  }
  res.sendFile(path.join(__dirname, 'dashboard.html'));
});

// Logout route
app.get('/logout', (req, res) => {
  req.session.destroy(err => {
    if (err) {
      console.error(err);
      return res.send('Error logging out.');
    }
    res.redirect('/index.html'); // go back to home page
  });
});

// Start server
app.listen(PORT, () => {
  console.log(`Server running at http://localhost:${PORT}/`);
});
