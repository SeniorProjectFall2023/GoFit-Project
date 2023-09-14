const express = require('express');
const mysql = require('mysq2');
const app = express();
require('dotenv').config(); 
const port = process.env.PORT || 3000;
const DB_HOST = process.env.DB_HOST
const DB_USER = process.env.DB_USER
const DB_PASSWORD = process.env.DB_PASSWORD
const DB_DATABASE = process.env.DB_DATABASE
const DB_PORT = process.env.DB_PORT

// Create a MySQL connection pool (configure with your database credentials)
const pool = mysql.createPool({
  connectionLimit: 100, 
  host: DB_HOST,
  user: DB_USER,
  password: DB_PASSWORD,
  database: DB_DATABASE,
  port: DB_PORT
});

app.use(express.json());
const bcrypt = require('bcrypt');

// Define routes for user registration and other functionality
app.post('/register', async (req, res) => {
  const userData = req.body;

  try {
    // Hash the user's password before storing it in the database
    const hashedPassword = await bcrypt.hash(userData.password, 10);
    userData.password = hashedPassword;

    // Insert the user data into the database
    pool.query('INSERT INTO user SET ?', userData, (err, results) => {
      if (err) {
        console.error(err);
        res.status(500).json({ error: 'Registration failed' });
      } else {
        // Redirect to signin.html upon successful registration
        res.redirect('../signin/signin.html');
      }
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Registration failed' });
  }
});

// Add a login route for user authentication
app.post('/login', async (req, res) => {
  const { username, password } = req.body;

  try {
    // Query the database to retrieve the user's hashed password
    const result = await pool.query('SELECT * FROM users WHERE username = ?', [username]);

    if (result.length === 1) {
      const user = result[0];
      const isPasswordValid = await bcrypt.compare(password, user.password);

      if (isPasswordValid) {
        // Authentication successful
        // Redirect to index.html (home page) upon successful login
        res.redirect('../frontend/index.html');
      } else {
        // Authentication failed
        res.status(401).json({ error: 'Authentication failed' });
      }
    } else {
      // User not found
      res.status(401).json({ error: 'Authentication failed' });
    }
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Authentication failed' });
  }
});

// Route to render the home page
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'frontend', 'index.html'));
});

// Route to render the chatbot page
app.get('/chatbot', (req, res) => {
  res.sendFile(path.join(__dirname, 'frontend', 'chatbot.html'));
});

// Route to render the contact page
app.get('/contact', (req, res) => {
  res.sendFile(path.join(__dirname, 'frontend', 'contact.html'));
});

// Route to render the FAQs page
app.get('/faqs', (req, res) => {
  res.sendFile(path.join(__dirname, 'frontend', 'faqs.html'));
});

// Route to render the settings page
app.get('/settings', (req, res) => {
  res.sendFile(path.join(__dirname, 'frontend', 'settings.html'));
});

app.listen(3000)

// Start the server
app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});