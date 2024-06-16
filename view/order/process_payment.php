<?php
include ('../../config/database.php');

$orderid = $_POST['orderid'];
$paymentmethod = $_POST['paymentmethod'];
$paymentdetails = $_POST['paymentdetails'];
$totalamount = $_POST['totalamount'];
$customerpayamount = $_POST['customerpayamount'];

// Insert payment information
$query = "INSERT INTO tblpayment (orderid, paymentmethod, paymentdetails, totalamount, customerpayamount, paymentdate) VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($query);
$stmt->bind_param('issdd', $orderid, $paymentmethod, $paymentdetails, $totalamount, $customerpayamount);

if ($stmt->execute()) {
    // Update order status
    $updateQuery = "UPDATE tblorder SET statusid = 2 WHERE orderid = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param('i', $orderid);
    $updateStmt->execute();
    $updateStmt->close();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error while processing payment']);
}

$stmt->close();
$conn->close();
?>