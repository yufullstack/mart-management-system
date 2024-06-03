<?php
include("../../config/database.php");

// Check if Barcode is set and not empty
if (isset($_GET['Barcode']) && !empty($_GET['Barcode'])) {
    // Sanitize Barcode to prevent SQL injection
    $Barcode = $conn->real_escape_string($_GET['Barcode']);

    // Fetch product price, name, and productid from tblproduct
    $sqlProduct = "SELECT priceout, productname, productid, productimage FROM tblproduct WHERE barcode = '$Barcode'";
    $resultProduct = $conn->query($sqlProduct);

    if ($resultProduct) {
        if ($resultProduct->num_rows > 0) {
            // Product found
            $rowProduct = $resultProduct->fetch_assoc();
            $productPrice = $rowProduct['priceout'];
            $productName = $rowProduct['productname'];
            $productId = $rowProduct['productid'];
            $productImage = $rowProduct['productimage'];

            // Fetch discount value from tbldiscount based on productid
            $sqlDiscount = "SELECT discountvalue FROM tbldiscount WHERE productid = '$productId'";
            $resultDiscount = $conn->query($sqlDiscount);

            $discountValue = 0; // Default discount value
            if ($resultDiscount && $resultDiscount->num_rows > 0) {
                $rowDiscount = $resultDiscount->fetch_assoc();
                $discountValue = $rowDiscount['discountvalue'];
            }

            // Send product information as JSON response
            $productInfo = array(
                'productName' => $productName,
                'unitPrice' => $productPrice,
                'discount' => $discountValue,
                'productid' => $productId,
                'productimages' => $productImage
            );

            header('Content-Type: application/json');
            echo json_encode($productInfo);
            exit();
        } else {
            // Product not found
            echo json_encode(array('error' => 'Product not found'));
            exit();
        }
    } else {
        // Error fetching product
        echo json_encode(array('error' => 'Error fetching product'));
        exit();
    }
} else {
    // Barcode not provided or empty
    echo json_encode(array('error' => 'Barcode not provided'));
    exit();
}

// Close database connection
$conn->close();
?>