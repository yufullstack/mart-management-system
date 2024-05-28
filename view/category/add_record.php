<?php
include("../../config/database.php");

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoryname = $_POST['categoryname'];
    // Assuming sexid and statusid are still relevant fields in tblcategory
    $statusid = $_POST['statusid'];

    // Insert data into database
    $sql = "INSERT INTO tblcategory (categoryname, statusid) 
            VALUES ('$categoryname', '$statusid')";
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