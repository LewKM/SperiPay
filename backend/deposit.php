<?php
// Assuming you have included your database connection in db.php
include('db.php');
session_start(); // Start session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in by verifying their session
    if (!isset($_SESSION['ClientAccountNumber'])) {
        $Message = "User not logged in";
        http_response_code(401); // Unauthorized
    } else {
        $amount = $_POST['amount']; // Get the deposit amount

        // Add the deposit amount to the user's account (this is a basic example, adjust based on your DB schema)
        $ClientAccountNumber = $_SESSION['ClientAccountNumber'];
        $updateQuery = "UPDATE client SET balance = balance + $amount WHERE ClientAccountNumber = '$ClientAccountNumber'";
        $result = mysqli_query($conn, $updateQuery);

        if ($result) {
            $Message = "Deposit successful";
            http_response_code(200); // Success
        } else {
            $Message = "Deposit failed";
            http_response_code(500); // Internal server error
        }
    }

    $response = array("Message" => $Message);
    echo json_encode($response);
}
?>
