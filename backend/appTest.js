const express = require('express');
const mysql = require('mysql2');
const bcrypt = require('bcrypt');
const bodyParser = require('body-parser');

const app = express();
const port = 3000;

// Create a MySQL connection pool
const db = mysql.createPool({
  host: 'localhost',
  user: 'root',
  password: 'cosc4360-McCurry',
  database: 'GoFit',
  connectionLimit: 10, // Adjust the limit as needed
});

// Middleware
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());

// Routes for signup and sign-in

// Signup route
app.post('/signup', (req, res) => {
  const { username, password } = req.body;

  // Hash the password
  bcrypt.hash(password, 10, (err, hash) => {
    if (err) {
      console.error(err);
      return res.status(500).json({ error: 'Error hashing password' });
    }

    // Store user data in the database
    db.query(
      'INSERT INTO users (username, password) VALUES (?, ?)',
      [username, hash],
      (err, result) => {
        if (err) {
          console.error(err);
          return res.status(500).json({ error: 'Error registering user' });
        }

        return res.status(201).json({ message: 'User registered successfully' });
      }
    );
  });
});

// Sign-in route
app.post('/signin', (req, res) => {
  const { username, password } = req.body;

  // Retrieve user data from the database
  db.query('SELECT * FROM users WHERE username = ?', [username], (err, results) => {
    if (err) {
      console.error(err);
      return res.status(500).json({ error: 'Error querying database' });
    }

    if (results.length === 0) {
      return res.status(401).json({ error: 'Invalid username or password' });
    }

    const user = results[0];

    // Compare the hashed password
    bcrypt.compare(password, user.password, (err, match) => {
      if (err) {
        console.error(err);
        return res.status(500).json({ error: 'Error comparing passwords' });
      }

      if (!match) {
        return res.status(401).json({ error: 'Invalid username or password' });
      }

      // User authentication successful
      return res.status(200).json({ message: 'User signed in successfully' });
    });
  });
});

app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});
