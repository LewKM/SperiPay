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
    $ClientAccountNumber = $decodedData['ClientAccountNumber'];
    $OldClientPIN = md5($decodedData['OldClientPIN']); // Hash of old PIN
    $NewClientPIN = md5($decodedData['NewClientPIN']); // Hash of new PIN
    
    // Check old PIN
    $selectSQL = "SELECT * FROM client WHERE ClientAccountNumber = ?";
    $stmt = $conn->prepare($selectSQL);
    $stmt->bind_param("s", $ClientAccountNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $checkAccount = $result->num_rows;

    if ($checkAccount != 0) {
        $arrayu = $result->fetch_assoc();
        if ($arrayu['ClientPIN'] != $OldClientPIN) {
            $Message = "Old PIN is incorrect";
        } else {
            // Update the PIN
            $updateSQL = "UPDATE client SET ClientPIN = ? WHERE ClientAccountNumber = ?";
            $stmt = $conn->prepare($updateSQL);
            $stmt->bind_param("ss", $NewClientPIN, $ClientAccountNumber);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $Message = "PIN changed successfully";
            } else {
                $Message = "Error changing PIN";
            }
        }
    } else {
        $Message = "No account found";
    }
} else {
    $Message = "Invalid JSON data";
}

// Respond with JSON
header('Content-Type: application/json');
echo json_encode(["Message" => $Message]);
?>

