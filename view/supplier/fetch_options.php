<?php
include("../../config/database.php");

// Initialize the options array
$options = array();

// Fetch positions
$positionResult = $conn->query("SELECT positionid, positionname, statusid FROM tblposition WHERE statusid = 1");
$positions = array();
while ($row = $positionResult->fetch_assoc()) {
    $positions[] = $row;
}
$options['positions'] = $positions;


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