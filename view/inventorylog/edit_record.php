<?php
include("../../config/database.php");

// Fetch POST data
$logid = $_POST['logid'];
$productid = $_POST['productid'];
$changeamount = $_POST['changeamount'];
$reason = $_POST['reason'];
$statusid = $_POST['statusid']; // Assuming statusid is also provided in the POST request

// Update tblinventorylog
$sql_inventorylog = "UPDATE tblinventorylog 
                     SET productid='$productid', changeamount='$changeamount', reason='$reason', statusid='$statusid' 
                     WHERE logid='$logid'";

if ($conn->query($sql_inventorylog) === TRUE) {
    echo "Inventory log updated successfully";
} else {
    echo "Error updating inventory log: " . $conn->error;
}

// Close the database connection
$conn->close();
?>