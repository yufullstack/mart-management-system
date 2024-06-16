<?php
include("../../config/database.php");

if (isset($_GET['discountid'])) {
    $discountid = $_GET['discountid'];

    // Fetch discount details
    $query = "SELECT * FROM tbldiscount WHERE discountid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $discountid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $discount = $result->fetch_assoc();
        echo json_encode($discount);
    } else {
        echo json_encode(['error' => 'Discount not found']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>