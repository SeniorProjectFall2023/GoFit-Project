<?php
session_start();

if (isset($_POST['message']) && isset($_POST['sender'])) {
    $message = $_POST['message'];
    $sender = $_POST['sender'];

    $chatLog = isset($_SESSION['chatLog']) ? $_SESSION['chatLog'] : array();
    $chatLog[] = array("message" => $message, "sender" => $sender);

    $_SESSION['chatLog'] = $chatLog;
}
