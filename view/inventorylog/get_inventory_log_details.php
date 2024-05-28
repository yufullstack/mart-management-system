<?php
include("../../config/database.php");

if (isset($_GET['logid'])) {
    $logid = $_GET['logid'];

    // Fetch customer details
    $query = "SELECT * FROM tblinventorylog WHERE logid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $logid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
        echo json_encode($customer);
    } else {
        echo json_encode(['error' => 'Customer not found']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>