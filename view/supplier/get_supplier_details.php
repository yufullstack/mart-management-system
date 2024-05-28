<?php
include("../../config/database.php");

if (isset($_GET['supplierid'])) {
    $supplierid = $_GET['supplierid'];

    // Fetch supplier details
    $query = "SELECT * FROM tblsupplier WHERE supplierid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $supplierid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $supplier = $result->fetch_assoc();
        echo json_encode($supplier);
    } else {
        echo json_encode(['error' => 'Supplier not found']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>