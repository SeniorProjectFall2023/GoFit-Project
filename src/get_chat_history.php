<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['chat_history'])) {
    echo json_encode($_SESSION['chat_history']);
} else {
    echo json_encode([]);
}
?>