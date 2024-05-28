<?php
include("../../config/database.php");

$employeeid = $_POST['employeeid'];

$sql = "UPDATE tblemployee SET statusid = 2 WHERE employeeid='$employeeid'";

if ($conn->query($sql) === TRUE) {
    echo "Status updated successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>