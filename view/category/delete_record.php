<?php
include("../../config/database.php");

$categoryid = $_POST['categoryid'];

$sql = "UPDATE tblcategory SET statusid = 2 WHERE categoryid='$categoryid'";

if ($conn->query($sql) === TRUE) {
    echo "Status updated successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>