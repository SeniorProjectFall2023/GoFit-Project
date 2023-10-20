<?php
// Initialize a PHP session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and has a valid user ID
if (isset($_SESSION['userID']) && is_numeric($_SESSION['userID'])) {
    $userId = $_SESSION['userID'];
    
    // Your database connection code here
    $db_host = "localhost";
    $db_user = "root";
    $db_password = "cosc4360-McCurry";
    $db_name = "GoFit";

    // Create a database connection
    $conn = new mysqli($db_host, $db_user, $db_password, $db_name);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve chat data from the POST request
    $data = file_get_contents("php://input");
    $messageData = json_decode($data);

    if ($messageData) {
        $userMessage = $messageData->userMessage;
        $chatbotResponse = $messageData->chatbotResponse;

        // Prepare an SQL statement to insert the messages into the chat_messages table
        $sql = "INSERT INTO chat_messages (user_message, chatbot_response, user_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssi", $userMessage, $chatbotResponse, $userId);

            // Execute the SQL statement
            if ($stmt->execute()) {
                echo "Messages saved successfully!";
            } else {
                echo "Error executing the SQL statement: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error preparing the SQL statement: " . $conn->error;
        }
    }

    $conn->close();
} else {
    echo "User is not logged in or has an invalid user ID.";
}
?>
