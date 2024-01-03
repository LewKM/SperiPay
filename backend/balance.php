<?php
// Assuming you have included your database connection in db.php
include('db.php');
session_start(); // Start session

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Check if the user is logged in by verifying their session
    if (!isset($_SESSION['ClientAccountNumber'])) {
        $Message = "User not logged in";
        http_response_code(401); // Unauthorized
    } else {
        $ClientAccountNumber = $_SESSION['ClientAccountNumber'];

        // Fetch account details (this is a basic example, adjust based on your DB schema)
        $query = "SELECT AccountNumber, AccountHolder, Balance FROM client WHERE ClientAccountNumber = '$ClientAccountNumber'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $accountDetails = mysqli_fetch_assoc($result);
            // Remove sensitive information like PIN from the response
            unset($accountDetails['ClientPIN']);
            $Message = "Account details fetched successfully";
            http_response_code(200); // Success
            echo json_encode($accountDetails);
            exit();
        } else {
            $Message = "Failed to fetch account details";
            http_response_code(500); // Internal server error
        }
    }

    $response = array("Message" => $Message);
    echo json_encode($response);
}
?>
