<?php
include('../../config/database.php');

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build the query
$query = "SELECT tblproduct.*, tblcategory.categoryname FROM tblproduct 
          JOIN tblcategory ON tblproduct.categoryid = tblcategory.categoryid";

if ($category) {
    $query .= " WHERE tblcategory.categoryname = '" . mysqli_real_escape_string($conn, $category) . "'";
}

if ($search) {
    $query .= ($category ? " AND " : " WHERE ") . "tblproduct.productname LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'";
}

$result = mysqli_query($conn, $query);

$products = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}

// Return the products as JSON
header('Content-Type: application/json');
echo json_encode($products);
?>