<?php
include("../../config/database.php");

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productid = $_POST['productid'];
    $discountvalue = $_POST['discountvalue'];
    $startdate = $_POST['startdate'];
    $enddate = $_POST['enddate'];

    // Prepare the SQL statement
    $sql = "UPDATE tbldiscount 
            SET discountvalue = ?, startdate = ?, enddate = ? 
            WHERE productid = ?";

    // Prepare and bind
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssss", $discountvalue, $startdate, $enddate, $productid);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Record updated successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>