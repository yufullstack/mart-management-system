<?php
include('../../config/database.php');

if (!isset($_GET['orderId'])) {
    die('Missing orderId parameter.');
}

$orderId = $_GET['orderId'];

// Fetch order details from the database
$query = "SELECT * FROM tblorder WHERE orderid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $orderId);

if (!$stmt->execute()) {
    die('Error executing query: ' . $stmt->error);
}

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $orderDetails = array(
        'orderid' => $row['orderid'],
        'totalamount' => $row['totalamount']
    );
    echo json_encode(array('success' => true, 'order' => $orderDetails));
} else {
    echo json_encode(array('success' => false, 'message' => 'Order not found.'));
}

$stmt->close();
$conn->close();
?>