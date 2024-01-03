<?php
include('db.php');

$ClientFirstName = $decodedData['Email'];
$ClientLastName = $decodedData['Password']; 
$ClientAccountNumber = $decodedData['AccountNumber'];
$ClientPIN = md5($decodedData['PIN']); //PIN is hashed

$SQL = "SELECT * FROM client WHERE ClientAccountNumber = '$ClientAccountNumber'";
$exeSQL = mysqli_query($conn, $SQL);
$checkEmail =  mysqli_num_rows($exeSQL);

if ($checkEmail != 0) {
    $Message = "Already registered";
} else {

    $InsertQuerry = "INSERT INTO client(ClientFirstName, ClientLastName, ClientAccountNumber, ClientPIN) VALUES('$ClientFirstName', '$ClientLastName', '$ClientAccountNumber', '$ClientPIN')";

    $R = mysqli_query($conn, $InsertQuerry);

    if ($R) {
        $Message = "Complete--! Your account has been created";
    } else {
        $Message = "Error creating account";
    }
}
$response[] = array("Message" => $Message);

echo json_encode($response);