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

if (isset($_SESSION['ClientAccountNumber']) && $decodedData) {
    $ClientAccountNumber = $_SESSION['ClientAccountNumber'];
    $ChequeNumber = $decodedData['ChequeNumber'];
    
    // Insert the stopped cheque into StoppedCheques table
    $insertSQL = "INSERT INTO StoppedCheques (ClientAccountNumber, ChequeNumber) VALUES (?, ?)";
    $stmt = $conn->prepare($insertSQL);
    $stmt->bind_param("ss", $ClientAccountNumber, $ChequeNumber);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $Message = "Cheque stopped successfully";
    } else {
        $Message = "Error stopping cheque";
    }
} else {
    $Message = "Invalid request or user not logged in";
}

// Respond with JSON
header('Content-Type: application/json');
echo json_encode(["Message" => $Message]);
?>

