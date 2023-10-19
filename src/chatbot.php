<?php

// Initialize a PHP session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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
    $sql = "INSERT INTO chat_messages (user_message, chatbot_response) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $userMessage, $chatbotResponse);

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

    // Store the chat history in a session variable
    if (!isset($_SESSION['chat_history'])) {
        $_SESSION['chat_history'] = [];
    }

    // Append the user message and chatbot response to the chat history
    $_SESSION['chat_history'][] = [
        'user_message' => $userMessage,
        'chatbot_response' => $chatbotResponse,
    ];
}

$conn->close();
?>
