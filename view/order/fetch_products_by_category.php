<?php
include('../../config/database.php');

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Initialize the base query
$query = "SELECT tblproduct.*, tblcategory.categoryname, COALESCE(tblinventory.stocklevel, 0) AS stocklevel
          FROM tblproduct 
          JOIN tblcategory ON tblproduct.categoryid = tblcategory.categoryid
          LEFT JOIN tblinventory ON tblproduct.productid = tblinventory.productid";
$params = [];
$types = '';

if ($category) {
    $query .= " WHERE tblcategory.categoryname = ?";
    $params[] = $category;
    $types .= 's';
}

if ($search) {
    $query .= ($category ? " AND " : " WHERE ") . "tblproduct.productname LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= 's';
}

// Prepare and execute the statement
$stmt = $conn->prepare($query);

if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Return the products as JSON
header('Content-Type: application/json');
echo json_encode($products);

$stmt->close();
$conn->close();
?>