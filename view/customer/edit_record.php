<?php
include("../../config/database.php");

$customerid = $_POST['customerid'];
$customername = $_POST['customername'];
$sexid = $_POST['sexid'];
$address = $_POST['address'];
$phonenumber = $_POST['phonenumber'];
$statusid = $_POST['statusid'];


    $sql = "UPDATE tblcustomer SET customername='$customername', sexid='$sexid', address='$address', phonenumber='$phonenumber', statusid='$statusid' WHERE customerid='$customerid'";


if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>