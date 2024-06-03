<?php
include("../../config/database.php");

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productname = $_POST['productname'];
    $categoryid = $_POST['categoryid'];
    $supplierid = $_POST['supplierid'];
    $quantity = $_POST['quantity'];
    $pricein = $_POST['pricein'];
    $priceout = $_POST['priceout'];
    $instock = $_POST['instock'];
    $statusid = 1;

    // Function to generate a unique barcode
    function generateBarcode($length = 12) {
        $characters = '0123456789';
        $barcode = '';
        for ($i = 0; $i < $length; $i++) {
            $barcode .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $barcode;
    }

    $barcode = generateBarcode();

    $targetDir = "../../public/img/";
    $productimage = basename($_FILES["productimage"]["name"]);
    $targetFilePath = $targetDir . $productimage;

    // Upload file to server
    if (move_uploaded_file($_FILES["productimage"]["tmp_name"], $targetFilePath)) {
        // Insert data into database
        $sql = "INSERT INTO tblproduct (productname, categoryid, supplierid, quantity, pricein, priceout, instock, productimage, statusid, barcode) 
                VALUES ('$productname', '$categoryid', '$supplierid', '$quantity', '$pricein', '$priceout', '$instock', '$productimage', $statusid, '$barcode')";
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully with barcode: $barcode";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>