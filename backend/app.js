const express = require('express');
const mysql = require('mysql2');
const app = express();
const port = process.env.PORT || 3000;

// Create a MySQL connection pool (configure with your database credentials)
const pool = mysql.createPool({
  host: 'ubuntu-cosc4360-01',
  user: 'GoFit@localhost',
  password: 'cosc4360-McCurry',
  database: 'GoFit',
});

app.use(express.json());

// Define routes for user registration and other functionality
app.post('/register', (req, res) => {
  // Handle user registration here, insert data into the database
  const userData = req.body; // Assuming the frontend sends user data in the request body

  // Insert the user data into the database using the MySQL connection pool
  pool.query('INSERT INTO user SET ?', userData, (err, results) => {
    if (err) {
      console.error(err);
      res.status(500).json({ error: 'Registration failed' });
    } else {
      // Redirect to signin.html upon successful registration
      res.redirect('frontend/signin/signin.html');
    }
  });
});

// Add a login route for user authentication
app.post('/login', (req, res) => {
  const { username, password } = req.body;

  // Perform authentication logic here
  // You should query the database to check if the provided username and password are valid

  // Example: Check if the user exists in the database
  pool.query('SELECT * FROM users WHERE username = ? AND password = ?', [username, password], (err, results) => {
      if (err) {
          console.error(err);
          res.status(500).json({ error: 'Authentication failed' });
      } else {
          if (results.length === 1) {
              // Authentication successful
              // Redirect to index.html (home page) upon successful login
              res.redirect('frontend/index.html');
          } else {
              // Authentication failed
              res.status(401).json({ error: 'Authentication failed' });
          }
      }
  });
});

// Start the server
app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});
