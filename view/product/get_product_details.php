<?php
include("../../config/database.php");

if (isset($_GET['productid'])) {
    $productid = $_GET['productid'];

    // Fetch product details
    $query = "SELECT p.productid, p.productname, p.categoryid, p.supplierid, pu.quantity, p.pricein, p.priceout, i.stocklevel AS instock, p.productimage, p.productdate, p.barcode, p.statusid
    FROM tblproduct p
    JOIN tblstatus st ON p.statusid = st.statusid
    JOIN tblinventory i ON p.productid = i.productid
    JOIN tblpurchase pu ON p.productid = pu.productid
    WHERE p.productid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $productid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>