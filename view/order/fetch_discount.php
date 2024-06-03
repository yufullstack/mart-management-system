<?php
include('../../config/database.php');

header('Content-Type: application/json');

if (isset($_GET['barcode'])) {
    $barcode = $_GET['barcode'];

    // Correct the SQL query to fetch discount based on the barcode
    $sql = "SELECT discountvalue FROM tbldiscount WHERE productid = (SELECT productid FROM tblproduct WHERE barcode = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $barcode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['discountvalue' => $row['discountvalue']]);
    } else {
        echo json_encode(['discountvalue' => 0]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'No barcode provided']);
}
?>