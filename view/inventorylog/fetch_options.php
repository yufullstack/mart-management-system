<?php
include("../../config/database.php");

// Initialize the options array
$options = array();

// Fetch products
$productResult = $conn->query("SELECT productid, productname FROM tblproduct");
$products = array();
if ($productResult) {
    while ($row = $productResult->fetch_assoc()) {
        $products[] = $row;
    }
    $options['products'] = $products;
} else {
    $options['error'] = "Error fetching products: " . $conn->error;
}

// Fetch statuses
$statusResult = $conn->query("SELECT statusid, statusname FROM tblstatus");
$statuses = array();
if ($statusResult) {
    while ($row = $statusResult->fetch_assoc()) {
        $statuses[] = $row;
    }
    $options['statuses'] = $statuses;
} else {
    $options['error'] = "Error fetching statuses: " . $conn->error;
}

// Close the database connection
$conn->close();

// Output the options as a JSON object
echo json_encode($options);
?>