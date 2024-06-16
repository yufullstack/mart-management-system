<?php
include("../../config/database.php");

if (isset($_POST['discountid'])) {
    $discountid = $_POST['discountid'];

    // Delete the record from tbldiscount
    $sql = "DELETE FROM tbldiscount WHERE discountid = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $discountid);
        if ($stmt->execute()) {
            $response['discount'] = "Discount deleted successfully";
        } else {
            $response['discount'] = "Error deleting discount: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $response['discount'] = "Error preparing statement: " . $conn->error;
    }
}

if (empty($response)) {
    echo json_encode(['error' => 'Invalid request']);
} else {
    echo json_encode($response);
}

$conn->close();
?>