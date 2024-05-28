<?php
include("../../config/database.php");

$productid = $_POST['productid'];
$productname = $_POST['productname'];
$categoryid = $_POST['categoryid'];
$supplierid = $_POST['supplierid'];
$quantity = $_POST['quantity'];
$pricein = $_POST['pricein'];
$priceout = $_POST['priceout'];
$instock = $_POST['instock'];
$statusid = $_POST['statusid'];

$targetDir = "../../public/img/";
$productimage = $_FILES['productimage']['name'];
$targetFilePath = $targetDir . basename($productimage);

if (!empty($productimage)) {
    // New file uploaded
    if (move_uploaded_file($_FILES['productimage']['tmp_name'], $targetFilePath)) {
        $sql = "UPDATE tblproduct SET productname='$productname', categoryid='$categoryid', supplierid='$supplierid', quantity='$quantity', pricein='$pricein', priceout='$priceout', instock='$instock', productimage='$productimage', statusid='$statusid' WHERE productid='$productid'";
    } else {
        echo "Error uploading file.";
        exit;
    }
} else {
    // No new file uploaded, keep the old file
    $sql = "UPDATE tblproduct SET productname='$productname', categoryid='$categoryid', supplierid='$supplierid', quantity='$quantity', pricein='$pricein', priceout='$priceout', instock='$instock', statusid='$statusid' WHERE productid='$productid'";
}

if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>