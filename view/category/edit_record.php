<?php
include("../../config/database.php");

$categoryid = $_POST['categoryid'];
$categoryname = $_POST['categoryname'];
$statusid = $_POST['statusid'];

$sql = "UPDATE tblcategory SET categoryname='$categoryname', statusid='$statusid' WHERE categoryid='$categoryid'";

if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>