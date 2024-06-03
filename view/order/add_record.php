<?php
include('../../config/database.php');

if (!isset($_POST['employeeid'], $_POST['customerid'], $_POST['totalamount'])) {
    die('Missing required parameters.');
}

$employeeid = $_POST['employeeid'];
$customerid = $_POST['customerid'];
$discount = $_POST['discount'] ?? 0;
$totalamount = $_POST['totalamount'];
$statusid = 1;

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO tblorder (employeeid, customerid, discount, totalamount, statusid) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    $stmt->bind_param("ssdsi", $employeeid, $customerid, $discount, $totalamount, $statusid);
    if (!$stmt->execute()) {
        throw new Exception("Execute statement failed: " . $stmt->error);
    }
    $orderid = $stmt->insert_id;
    $stmt->close();

    $productids = $_POST['productid'];
    $quantities = $_POST['quantity'];
    $unitprices = $_POST['unitprice'];
    $productdiscounts = $_POST['productdiscount'];

    $stmt = $conn->prepare("INSERT INTO tblorderdetail (orderid, productid, quantity, unitprice, discount, statusid) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare statement for order details failed: " . $conn->error);
    }

    for ($i = 0; $i < count($productids); $i++) {
        $productid = $productids[$i];
        $quantity = $quantities[$i];
        $unitprice = $unitprices[$i];
        $productdiscount = $productdiscounts[$i];

        $stmt->bind_param("isiddi", $orderid, $productid, $quantity, $unitprice, $productdiscount, $statusid);
        if (!$stmt->execute()) {
            throw new Exception("Execute statement for order details failed: " . $stmt->error);
        }
    }
    $stmt->close();

    $conn->commit();
    echo "Order and products added successfully.";
} catch (Exception $e) {
    $conn->rollback();
    echo "Failed to add order and products: " . $e->getMessage();
}

$conn->close();
?>