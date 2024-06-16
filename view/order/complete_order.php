<?php
include ('../../config/database.php');

if (!isset($_POST['employeeid'], $_POST['totalamount'], $_POST['productid'], $_POST['quantity'], $_POST['unitprice'], $_POST['productdiscount'])) {
    die('Missing required parameters.');
}

$employeeid = $_POST['employeeid'];
$customerid = isset($_POST['customerid']) && !empty($_POST['customerid']) ? $_POST['customerid'] : null; // NULL for walk-in customers
$discount = $_POST['discount'] ?? 0;
$totalamount = $_POST['totalamount'];
$orderdate = date('Y-m-d');
$duedate = date('Y-m-d', strtotime('+7 days'));
$statusid = 1;

$conn->begin_transaction();

try {
    // Insert order
    if ($customerid === null) {
        $stmt = $conn->prepare("INSERT INTO tblorder (employeeid, discount, totalamount, orderdate, duedate, statusid) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare statement for order failed: " . $conn->error);
        }
        $stmt->bind_param("iddssi", $employeeid, $discount, $totalamount, $orderdate, $duedate, $statusid);
    } else {
        $stmt = $conn->prepare("INSERT INTO tblorder (employeeid, customerid, discount, totalamount, orderdate, duedate, statusid) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare statement for order failed: " . $conn->error);
        }
        $stmt->bind_param("iiddssi", $employeeid, $customerid, $discount, $totalamount, $orderdate, $duedate, $statusid);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Execute statement for order failed: " . $stmt->error);
    }
    $orderid = $stmt->insert_id;
    $stmt->close();

    // Prepare statements for order details, stock update, and inventory logging
    $stmt = $conn->prepare("INSERT INTO tblorderdetail (orderid, productid, quantity, unitprice, discount, statusid) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare statement for order details failed: " . $conn->error);
    }

    $updateStockStmt = $conn->prepare("UPDATE tblinventory SET stocklevel = stocklevel - ? WHERE productid = ?");
    if (!$updateStockStmt) {
        throw new Exception("Prepare statement for stock update failed: " . $conn->error);
    }

    $logStmt = $conn->prepare("INSERT INTO tblinventorylog (productid, changeamount, reason, statusid) VALUES (?, ?, ?, ?)");
    if (!$logStmt) {
        throw new Exception("Prepare statement for inventory log failed: " . $conn->error);
    }

    // Insert order details, update stock level, and log inventory changes
    $productids = $_POST['productid'];
    $quantities = $_POST['quantity'];
    $unitprices = $_POST['unitprice'];
    $productdiscounts = $_POST['productdiscount'];

    for ($i = 0; $i < count($productids); $i++) {
        $productid = $productids[$i];
        $quantity = $quantities[$i];
        $unitprice = $unitprices[$i];
        $productdiscount = $productdiscounts[$i];

        // Check if there is enough stock
        $stockCheckStmt = $conn->prepare("SELECT stocklevel FROM tblinventory WHERE productid = ?");
        if (!$stockCheckStmt) {
            throw new Exception("Prepare statement for stock check failed: " . $conn->error);
        }
        $stockCheckStmt->bind_param("i", $productid);
        $stockCheckStmt->execute();
        $stockCheckStmt->bind_result($stocklevel);
        $stockCheckStmt->fetch();
        $stockCheckStmt->close();

        if ($stocklevel < $quantity) {
            throw new Exception("Insufficient stock for product ID $productid");
        }

        // Insert order details
        $stmt->bind_param("iiiddi", $orderid, $productid, $quantity, $unitprice, $productdiscount, $statusid);
        if (!$stmt->execute()) {
            throw new Exception("Execute statement for order details failed: " . $stmt->error);
        }

        // Update stock level
        $updateStockStmt->bind_param("ii", $quantity, $productid);
        if (!$updateStockStmt->execute()) {
            throw new Exception("Execute statement for stock update failed: " . $updateStockStmt->error);
        }

        // Log inventory change
        $changeAmount = -$quantity; // Negative because stock is being reduced
        $reason = "Sale";
        $logStmt->bind_param("iisi", $productid, $changeAmount, $reason, $statusid);
        if (!$logStmt->execute()) {
            throw new Exception("Execute statement for inventory log failed: " . $logStmt->error . " | Params: productid=$productid, changeamount=$changeAmount, reason=$reason, statusid=$statusid");
        }
    }
    $stmt->close();
    $updateStockStmt->close();
    $logStmt->close();

    $conn->commit();
    echo json_encode(['success' => true, 'orderid' => $orderid]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => "Failed to add order and products: " . $e->getMessage()]);
}

$conn->close();
?>