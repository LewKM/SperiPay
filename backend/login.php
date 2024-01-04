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
    $ClientAccountNumber = $decodedData['ClientAccountNumber'];
    $ClientPIN = md5($decodedData['ClientPIN']); // Hashing PIN
    
    // Prepared statement to prevent SQL injection
    $selectSQL = "SELECT * FROM client WHERE ClientAccountNumber = ?";
    $stmt = $conn->prepare($selectSQL);
    $stmt->bind_param("s", $ClientAccountNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $checkAccount = $result->num_rows;

    if ($checkAccount != 0) {
        $arrayu = $result->fetch_assoc();
        if ($arrayu['ClientPIN'] != $ClientPIN) {
            $Message = "PIN is wrong";
        } else {
            // Store necessary information in session
            $_SESSION['ClientAccountNumber'] = $arrayu['ClientAccountNumber'];
           // $_SESSION['ClientName'] = $arrayu['ClientName'];  // Add more as needed

            $Message = "Success login";
        }
    } else {
        $Message = "No account yet registered";
    }
} else {
    $Message = "Invalid JSON data";
}

// Respond with JSON
header('Content-Type: application/json');
echo json_encode(["Message" => $Message]);
?>

