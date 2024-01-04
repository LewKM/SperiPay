<?php
session_start(); // Start the session

if (isset($_SESSION['ClientAccountNumber'])) {
    $ClientAccountNumber = $_SESSION['ClientAccountNumber'];

    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Respond with JSON for successful logout
    header('Content-Type: application/json');
    echo json_encode(["Message" => "Logout successful for account: $ClientAccountNumber"]);
} else {
    // Respond with JSON for no active session
    header('Content-Type: application/json');
    echo json_encode(["Message" => "No active session to logout"]);
}
?>

