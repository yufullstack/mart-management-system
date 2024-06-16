<?php
include ('../../config/database.php');

if (isset($_GET['productid'])) {
    $product_id = $_GET['productid'];

    // Fetch the purchase price for the given product ID
    $sql = "SELECT pricein FROM tblproduct WHERE productid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // Return the purchase price as JSON
    echo json_encode($product);
}
?>