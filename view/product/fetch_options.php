<?php
include("../../config/database.php");

// Initialize the options array for products
$productOptions = array();

// Fetch categories for products
$categoryResult = $conn->query("SELECT categoryid, categoryname, statusid FROM tblcategory WHERE statusid = 1");
$categories = array();
while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row;
}
$productOptions['categories'] = $categories;

// Fetch suppliers for products
$supplierResult = $conn->query("SELECT supplierid, suppliername, statusid FROM tblsupplier WHERE statusid = 1");
$suppliers = array();
while ($row = $supplierResult->fetch_assoc()) {
    $suppliers[] = $row;
}
$productOptions['suppliers'] = $suppliers;

// Fetch statuses for products
$statusResult = $conn->query("SELECT statusid, statusname FROM tblstatus");
$statuses = array();
while ($row = $statusResult->fetch_assoc()) {
    $statuses[] = $row;
}
$productOptions['statuses'] = $statuses;

// Close the database connection
$conn->close();


// Output the options as a JSON object
echo json_encode($productOptions);
?>