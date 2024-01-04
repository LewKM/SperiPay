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
    
    // Prepared statement to prevent SQL injection and fetch necessary data
    $selectSQL = "SELECT ClientAccountNumber, CONCAT(ClientFirstName, ' ', ClientLastName) AS ClientName, ClientBalance FROM client WHERE ClientAccountNumber = ?";
    $stmt = $conn->prepare($selectSQL);
    $stmt->bind_param("s", $ClientAccountNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $checkAccount = $result->num_rows;

    if ($checkAccount != 0) {
        $arrayu = $result->fetch_assoc();
        $balanceData = [
            "ClientAccountNumber" => $arrayu['ClientAccountNumber'],
            "ClientName" => $arrayu['ClientName'],
            "ClientBalance" => $arrayu['ClientBalance']
        ];

        // Respond with JSON excluding PIN
        header('Content-Type: application/json');
        echo json_encode($balanceData);
    } else {
        $Message = "No account found";
    }
} else {
    $Message = "No active session or invalid account";
}

// Respond with error message (if any)
if (isset($Message)) {
    header('Content-Type: application/json');
    echo json_encode(["Message" => $Message]);
}
?>

