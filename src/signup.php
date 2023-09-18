<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establish a database connection
    $db = new mysqli("localhost", "gofit", "cosc4360-McCurry", "GoFit");

    // Check for connection errors
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Validate and sanitize user input
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // No need to sanitize, but validate password complexity here if needed.
    $meal_preference = $_POST['meal_preference'];
    $gender = $_POST['gender'];
    $weight = filter_var($_POST['weight'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $dateofbirth = $_POST['dateofbirth'];

    // Perform additional validation as needed

    // Insert user data into the database
    $query = "INSERT INTO user (name, username, email, password, meal_preference, gender, weight, dateofbirth)
              VALUES ('$name', '$username', '$email', '$password', '$meal_preference', '$gender', '$weight', '$dateofbirth')";
    
    if ($db->query($query) === TRUE) {
        // Registration successful, display a success message
        echo "<h1>Registration Successful!</h1>";
        echo "<p>Thank you for signing up with GoFit. You can now <a href='/signin/signin.html'>sign in here</a>.</p>";
    } else {
        echo "Error: " . $query . "<br>" . $db->error;
    }

    // Close the database connection
    $db->close();
} else {
    // Display a message if the page is accessed without a POST request
    echo "<h1>This page is working.</h1>";
}
?>
