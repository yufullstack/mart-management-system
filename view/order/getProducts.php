<?php
include('../../config/database.php');

header('Content-Type: application/json');

$query = $_GET['q'];

// Secure the query to prevent SQL injection
$escaped_query = $conn->real_escape_string($query);

// Perform the SQL query
$sql = "SELECT DISTINCT productname, productid, barcode 
        FROM tblproduct 
        WHERE barcode = '$escaped_query' OR productname LIKE '%$escaped_query%' 
        ORDER BY (barcode = '$escaped_query') DESC, productname";

$result = $conn->query($sql);

$products = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // If an exact barcode match is found, return only that product
        if ($row['barcode'] == $escaped_query) {
            $products = array($row);
            break;
        }
        $products[] = $row;
    }
}

// Encode the result as JSON
echo json_encode($products);

// Close the database connection
$conn->close();
?>