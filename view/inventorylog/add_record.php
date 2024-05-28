<?php
include("../../config/database.php");

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customername = $_POST['customername'];
    $sexid = $_POST['sexid'];
    $address = $_POST['address'];
    $phonenumber = $_POST['phonenumber'];
    $statusid = $_POST['statusid'];

    // Insert data into database
    $sql = "INSERT INTO tblcustomer (customername, sexid, address, phonenumber, statusid) 
            VALUES ('$customername', '$sexid', '$address', '$phonenumber', '$statusid')";
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>