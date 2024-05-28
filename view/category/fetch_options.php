<?php
include("../../config/database.php");

// Initialize the options array
$options = array();

// Fetch categories
$categoryResult = $conn->query("SELECT categoryid, categoryname FROM tblcategory");
$categories = array();
while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row;
}
$options['categories'] = $categories;

// Fetch statuses
$statusResult = $conn->query("SELECT statusid, statusname FROM tblstatus");
$statuses = array();
while ($row = $statusResult->fetch_assoc()) {
    $statuses[] = $row;
}
$options['statuses'] = $statuses;

// Close the database connection
$conn->close();

// Output the options as a JSON object
echo json_encode($options);
?>