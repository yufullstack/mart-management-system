<?php
include ('../../config/database.php');

$orderid = $_GET['orderid'];

$query = "SELECT totalamount FROM tblorder WHERE orderid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $orderid);
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();

$response = [];

if ($total !== null) {
    $response['success'] = true;
    $response['total'] = $total;
} else {
    $response['success'] = false;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>