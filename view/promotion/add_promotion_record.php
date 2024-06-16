<?php
include("../../config/database.php");

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productid = $_POST['productid'];
    $discountvalue = $_POST['discountvalue'];
    $startdate = $_POST['startdate'];
    $enddate = $_POST['enddate'];

    // Prepare the SQL statement
    $sql = "INSERT INTO tbldiscount (productid, discountvalue, startdate, enddate) 
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                discountvalue = VALUES(discountvalue),
                startdate = VALUES(startdate),
                enddate = VALUES(enddate)";

    // Prepare and bind
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssss", $productid, $discountvalue, $startdate, $enddate);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Record inserted/updated successfully";
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