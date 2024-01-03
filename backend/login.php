<?php
include('db.php');
session_start(); // Start session

$ClientAccountNumber = $decodedData['AccountNumber'];
$ClientPIN = ($decodedData['PIN']); //password is hashed

$SQL = "SELECT * FROM client WHERE ClientAccountNumber = '$ClientAccountNumber'";
$exeSQL = mysqli_query($conn, $SQL);
$checkEmail =  mysqli_num_rows($exeSQL);

if ($checkEmail != 0) {
    $arrayu = mysqli_fetch_array($exeSQL);
    if ($arrayu['ClientPIN'] != $ClientPIN) {
        $Message = "PIN is wrong";
    } else {
        // Store necessary information in session
        $_SESSION['ClientAccountNumber'] = $arrayu['ClientAccountNumber'];
        $_SESSION['ClientName'] = $arrayu['ClientName']; // Add more as needed
        
        $Message = "Success login";
    }
} else {
    $Message = "No account yet registered";
}

$response[] = array("Message" => $Message);
echo json_encode($response);
?>
