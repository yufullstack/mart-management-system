<?php
include("../../config/database.php");

if (isset($_GET['customerid'])) {
    $customerid = $_GET['customerid'];

    // Fetch customer details
    $query = "SELECT * FROM tblcustomer WHERE customerid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $customerid);
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