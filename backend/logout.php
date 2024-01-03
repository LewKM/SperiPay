<?php
session_start(); // Start session

// Destroy session data
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Respond with a success message
$response = array("Message" => "Logout successful");
echo json_encode($response);
?>
