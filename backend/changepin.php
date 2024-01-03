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
        $oldPin = $_POST['oldPin']; // Get the old PIN
        $newPin = $_POST['newPin']; // Get the new PIN

        // Perform PIN change logic (this is a basic example, adjust based on your DB schema)
        $ClientAccountNumber = $_SESSION['ClientAccountNumber'];
        $updateQuery = "UPDATE client SET ClientPIN = '$newPin' WHERE ClientAccountNumber = '$ClientAccountNumber' AND ClientPIN = '$oldPin'";
        $result = mysqli_query($conn, $updateQuery);

        if ($result) {
            $Message = "PIN changed successfully";
            http_response_code(200); // Success
        } else {
            $Message = "Failed to change PIN";
            http_response_code(500); // Internal server error
        }
    }

    $response = array("Message" => $Message);
    echo json_encode($response);
}
?>
