<?php
include("../../config/database.php");

// Initialize the options array
$options = array();

// Fetch employees
$employeeResult = $conn->query("SELECT employeeid, employeename, statusid FROM tblemployee");
$employees = array();
while ($row = $employeeResult->fetch_assoc()) {
    $employees[] = $row;
}
$options['employees'] = $employees;

// Fetch customers
$customerResult = $conn->query("SELECT customerid, customername FROM tblcustomer");
$customers = array();
while ($row = $customerResult->fetch_assoc()) {
    $customers[] = $row;
}
$options['customers'] = $customers;

// Fetch statuses
$statusResult = $conn->query("SELECT statusid, statusname FROM tblstatus");
$statuses = array();
while ($row = $statusResult->fetch_assoc()) {
    $statuses[] = $row;
}
$options['statuses'] = $statuses;


$resultProduct = $conn->query("SELECT productid, productname FROM tblproduct");
$products = array();
while ($row = $resultProduct->fetch_assoc()) {
    $products[] = $row;
}
$options['products'] = $products;

// Close the database connection
$conn->close();

// Output the options as a JSON object
echo json_encode($options);
?>