<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('db.php');

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
    $ClientFirstName = $decodedData['ClientFirstName'];
    $ClientLastName = $decodedData['ClientLastName'];
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
        $Message = "Already registered";
    } else {
        $insertSQL = "INSERT INTO client(ClientFirstName, ClientLastName, ClientAccountNumber, ClientPIN, ClientBalance) VALUES(?, ?, ?, ?, 0)";
        $stmt = $conn->prepare($insertSQL);
        $stmt->bind_param("ssss", $ClientFirstName, $ClientLastName, $ClientAccountNumber, $ClientPIN);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $Message = "Complete--! Your account has been created";
        } else {
            $Message = "Error creating account";
        }
    }
} else {
    $Message = "Invalid JSON data";
}

// Respond with JSON
header('Content-Type: application/json');
echo json_encode(["Message" => $Message]);
?>

