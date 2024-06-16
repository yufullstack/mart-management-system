<?php
include ('../../config/database.php');

$sql = "SELECT paymentmethodid, paymentmethodname FROM tblpaymentmethod";
$result = $conn->query($sql);

$methods = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $methods[] = $row;
    }
}

echo json_encode(['success' => true, 'methods' => $methods]);

$conn->close();
?>