<?php
include ('../../config/database.php');

// Check if required parameters are set
if (!isset($_POST['employeeid'], $_POST['customerid'], $_POST['totalamount'], $_POST['products'])) {
    die(json_encode(['success' => false, 'message' => 'Missing required parameters.']));
}

$employeeid = $_POST['employeeid'];
$customerid = $_POST['customerid'];
$discount = $_POST['discount'] ?? 0;
$totalamount = $_POST['totalamount'];
$products = json_decode($_POST['products'], true); // Decode JSON string to array
$statusid = 1;

$conn->begin_transaction();

try {
    // Insert order
    $stmt = $conn->prepare("INSERT INTO tblorder (employeeid, customerid, discount, totalamount, statusid, orderdate) VALUES (?, ?, ?, ?, ?, NOW())");
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    $stmt->bind_param("iisdi", $employeeid, $customerid, $discount, $totalamount, $statusid);
    if (!$stmt->execute()) {
        throw new Exception("Execute statement failed: " . $stmt->error);
    }
    $orderid = $stmt->insert_id;
    $stmt->close();

    // Insert order details
    $stmt = $conn->prepare("INSERT INTO tblorderdetail (orderid, productid, quantity, unitprice, discount, statusid) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare statement for order details failed: " . $conn->error);
    }

    foreach ($products as $product) {
        $productid = $product['id'];
        $quantity = $product['quantity'];
        $unitprice = $product['price'];
        $productdiscount = $product['discount'] ?? 0;

        $stmt->bind_param("iiidii", $orderid, $productid, $quantity, $unitprice, $productdiscount, $statusid);
        if (!$stmt->execute()) {
            throw new Exception("Execute statement for order details failed: " . $stmt->error);
        }
    }
    $stmt->close();

    $conn->commit();
    echo json_encode(['success' => true, 'orderid' => $orderid]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Failed to add order and products: ' . $e->getMessage()]);
}

$conn->close();
?>