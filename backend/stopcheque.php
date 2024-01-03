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
        $chequeNumber = $_POST['chequeNumber']; // Get the cheque number to be stopped

        // Perform the necessary action to stop the cheque (this is a basic example, adjust based on your DB schema)
        // For example, update a field in the database indicating the cheque is stopped
        $ClientAccountNumber = $_SESSION['ClientAccountNumber'];
        $updateQuery = "UPDATE cheques SET status = 'stopped' WHERE ClientAccountNumber = '$ClientAccountNumber' AND chequeNumber = '$chequeNumber'";
        $result = mysqli_query($conn, $updateQuery);

        if ($result) {
            $Message = "Cheque stopped successfully";
            http_response_code(200); // Success
        } else {
            $Message = "Failed to stop cheque";
            http_response_code(500); // Internal server error
        }
    }

    $response = array("Message" => $Message);
    echo json_encode($response);
}
?>
