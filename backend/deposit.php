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

if (isset($_SESSION['ClientAccountNumber'])) {
    $ClientAccountNumber = $_SESSION['ClientAccountNumber'];

    $json = file_get_contents('php://input');
    $decodedData = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        header("HTTP/1.1 400 Bad Request");
        exit(json_encode(["error" => "Invalid JSON data"]));
    }

    if ($decodedData) {
        $DepositAmount = $decodedData['DepositAmount']; // Assuming this field exists in your JSON
        
        // Prepared statement to prevent SQL injection
        $selectSQL = "SELECT * FROM client WHERE ClientAccountNumber = ?";
        $stmt = $conn->prepare($selectSQL);
        $stmt->bind_param("s", $ClientAccountNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $checkAccount = $result->num_rows;

        if ($checkAccount != 0) {
            $arrayu = $result->fetch_assoc();
            $currentBalance = $arrayu['ClientBalance'];
            
            // Update balance with deposit amount
            $updatedBalance = $currentBalance + $DepositAmount;

            $updateSQL = "UPDATE client SET ClientBalance = ? WHERE ClientAccountNumber = ?";
            $stmt = $conn->prepare($updateSQL);
            $stmt->bind_param("ds", $updatedBalance, $ClientAccountNumber);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $Message = "Deposit successful";
            } else {
                $Message = "Error updating balance";
            }
        } else {
            $Message = "No account found";
        }
    } else {
        $Message = "Invalid JSON data";
    }
} else {
    $Message = "No active session or invalid account";
}

// Respond with JSON
header('Content-Type: application/json');
echo json_encode(["Message" => $Message]);
?>

