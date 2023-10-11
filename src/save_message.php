<?php
session_start();

// Retrieve the message and sender from the AJAX request
$message = $_POST['message'];
$sender = $_POST['sender'];

// Retrieve the chat log from the session
$chatLog = $_SESSION['chat_log'];

// Add the new message to the chat log
$chatLog[] = ['message' => $message, 'sender' => $sender];

// Update the chat log in the session
$_SESSION['chat_log'] = $chatLog;

// Respond to the client (you can customize the response)
echo "Message saved in the session";
