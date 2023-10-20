<?php
session_start();

header('Content-Type: application/json');

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

    // Prepare an SQL statement to retrieve chat history for the user
    $sql = "SELECT user_message, chatbot_response FROM chat_messages WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $userId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $chatHistory = [];

            while ($row = $result->fetch_assoc()) {
                $chatHistory[] = [
                    'user_message' => $row['user_message'],
                    'chatbot_response' => $row['chatbot_response']
                ];
            }

            echo json_encode($chatHistory);
        } else {
            // Debugging statement
            echo json_encode(['error' => 'Execution of SQL statement failed.']);
        }

        $stmt->close();
    } else {
        // Debugging statement
        echo json_encode(['error' => 'SQL statement preparation failed.']);
    }

    $conn->close();
} else {
    // Debugging statement
    echo json_encode(['error' => 'User not logged in or invalid user ID.']);
}
?>

