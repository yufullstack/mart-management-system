<?php
include ('../../config/database.php');

if (isset($_POST['orderid'], $_POST['refundamount'])) {
    $orderid = $_POST['orderid'];
    $refundamount = $_POST['refundamount'];
    $refunddate = date('Y-m-d H:i:s');

    $sql = "INSERT INTO tblrefund (orderid, refundamount, refunddate) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ids', $orderid, $refundamount, $refunddate);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Required parameters missing.']);
}
?>