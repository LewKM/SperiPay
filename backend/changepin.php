<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('db.php');
session_start(); // Start session

// Check if content type is JSON
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if ($contentType !== 'application/json') {
    header("HTTP/1.1 400 Bad Request");
    exit(json_encode(["error" => "Invalid Content-Type. Expected application/json"]));
}

$json = file_get_contents('php://input');
$decodedData = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    header("HTTP/1.1 400 Bad Request");
    exit(json_encode(["error" => "Invalid JSON data"]));
}

if ($decodedData) {
    // Fetch ClientAccountNumber from session
    $ClientAccountNumber = isset($_SESSION['ClientAccountNumber']) ? $_SESSION['ClientAccountNumber'] : null;

    if ($ClientAccountNumber) {
        $OldClientPIN = md5($decodedData['OldClientPIN']); // Hashed old PIN
        $NewClientPIN = md5($decodedData['NewClientPIN']); // Hashed new PIN

        // Verify old PIN before changing
        $selectSQL = "SELECT * FROM client WHERE ClientAccountNumber = ? AND ClientPIN = ?";
        $stmt = $conn->prepare($selectSQL);
        $stmt->bind_param("ss", $ClientAccountNumber, $OldClientPIN);
        $stmt->execute();
        $result = $stmt->get_result();
        $checkPIN = $result->num_rows;

        if ($checkPIN != 0) {
            // Update PIN
            $updateSQL = "UPDATE client SET ClientPIN = ? WHERE ClientAccountNumber = ?";
            $stmt = $conn->prepare($updateSQL);
            $stmt->bind_param("ss", $NewClientPIN, $ClientAccountNumber);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $Message = "PIN changed successfully";
            } else {
                $Message = "Error changing PIN";
            }
        } else {
            $Message = "Incorrect old PIN";
        }
    } else {
        $Message = "No active session or invalid account";
    }
} else {
    $Message = "Invalid JSON data";
}

// Respond with JSON
header('Content-Type: application/json');
echo json_encode(["Message" => $Message]);
?>

