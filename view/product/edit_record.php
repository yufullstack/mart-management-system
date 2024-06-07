<?php
include("../../config/database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productid = $_POST['productid'];
    $productname = $_POST['productname'];
    $categoryid = $_POST['categoryid'];
    $supplierid = $_POST['supplierid'];
    $quantity = $_POST['quantity'];
    $pricein = $_POST['pricein'];
    $priceout = $_POST['priceout'];
    $instock = $_POST['instock'];
    $barcode = $_POST['barcode'];
    $statusid = $_POST['statusid'];

    $targetDir = "../../public/img/";
    $productimage = basename($_FILES["productimage"]["name"]);
    $targetFilePath = $targetDir . $productimage;

    $sql = "";

    if (!empty($productimage)) {
        // New file uploaded
        if (move_uploaded_file($_FILES['productimage']['tmp_name'], $targetFilePath)) {
            $sql = "UPDATE tblproduct SET 
                        productname='$productname', 
                        categoryid='$categoryid', 
                        supplierid='$supplierid', 
                        pricein='$pricein', 
                        priceout='$priceout', 
                        productimage='$productimage', 
                        statusid='$statusid', 
                        barcode='$barcode' 
                    WHERE productid='$productid'";
        } else {
            echo "Error uploading file.";
            exit;
        }
    } else {
        // No new file uploaded, keep the old file
        $sql = "UPDATE tblproduct SET 
                    productname='$productname', 
                    categoryid='$categoryid', 
                    supplierid='$supplierid', 
                    pricein='$pricein', 
                    priceout='$priceout',
                    statusid='$statusid', 
                    barcode='$barcode' 
                WHERE productid='$productid'";
    }

    if ($conn->query($sql) === TRUE) {
        // Check if the purchase record exists
        $purchase_date = date('Y-m-d H:i:s');
        $sql_check_purchase = "SELECT * FROM tblpurchase WHERE productid='$productid' AND supplierid='$supplierid'";
        $result_check_purchase = $conn->query($sql_check_purchase);

        if ($result_check_purchase->num_rows > 0) {
            // If record exists, update it
            $sql_purchase = "UPDATE tblpurchase SET 
                             quantity='$quantity', 
                             purchaseprice='$pricein', 
                             purchasedate='$purchase_date' 
                             WHERE productid='$productid' AND supplierid='$supplierid'";
        } else {
            // If record does not exist, insert new record
            $sql_purchase = "INSERT INTO tblpurchase (productid, supplierid, quantity, purchaseprice, purchasedate) 
                             VALUES ('$productid', '$supplierid', '$quantity', '$pricein', '$purchase_date')";
        }

        if ($conn->query($sql_purchase) === TRUE) {
            // Update tblinventory
            $sql_inventory = "UPDATE tblinventory SET stocklevel='$instock' WHERE productid='$productid'";
            if ($conn->query($sql_inventory) === TRUE) {
                // Optionally, insert into tblinventorylog
                $change_reason = 'Edited';
                $changedate = date('Y-m-d H:i:s');
                $sql_inventorylog = "INSERT INTO tblinventorylog (productid, changeamount, changedate, reason, statusid) 
                                     VALUES ('$productid', '$quantity', '$changedate', '$change_reason', '$statusid')";
                $conn->query($sql_inventorylog);

                echo "Record updated successfully";
            } else {
                echo "Error updating inventory: " . $conn->error;
            }
        } else {
            echo "Error updating purchase: " . $conn->error;
        }
    } else {
        echo "Error updating product: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>