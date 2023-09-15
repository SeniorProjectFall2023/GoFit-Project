<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from the registration form
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $fitness_goal = $_POST['meal_preference'];
    $gender = $_POST['gender'];
    $weight = $_POST['weight'];
    $dateofbirth = $_POST['dateofbirth'];

    // TODO: Implement validation and sanitation for the data.

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Connect to the MySQL database (Replace with your database credentials)
    $db = new mysqli('localhost', 'your_db_username', 'your_db_password', 'your_db_name');

    if ($db->connect_error) {
        die('Connection failed: ' . $db->connect_error);
    }

    // Insert user data into the database
    $query = "INSERT INTO users (name, username, email, password, fitness_goal, gender, weight, dateofbirth) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);

    if ($stmt) {
        $stmt->bind_param('ssssssss', $name, $username, $email, $hashedPassword, $fitness_goal, $gender, $weight, $dateofbirth);
        if ($stmt->execute()) {
            // Registration successful
            $response = ['success' => true];

            // Redirect to the sign-in page
            header('Location: ../signin/signin.html');
            exit(); // Make sure to exit to prevent further script execution
        } else {
            // Registration failed
            $response = ['success' => false, 'message' => 'Registration failed. Please try again later.'];
        }
        $stmt->close();
    } else {
        // Registration failed
        $response = ['success' => false, 'message' => 'Registration failed. Please try again later.'];
    }

    $db->close();

    // Send a JSON response to the front-end
    header('Content-type: application/json');
    echo json_encode($response);
} else {
    // Handle non-POST requests here if needed
}
?>
