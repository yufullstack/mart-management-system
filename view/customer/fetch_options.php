<?php
include("../../config/database.php");

// Initialize the options array
$options = array();


// Fetch genders
$genderResult = $conn->query("SELECT sexid, sexen FROM tblsex");
$genders = array();
while ($row = $genderResult->fetch_assoc()) {
    $genders[] = $row;
}
$options['genders'] = $genders;

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