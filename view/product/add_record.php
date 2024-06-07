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
    $barcode = $_POST['barcode'];
    $statusid = 1;

    $targetDir = "../../public/img/";
    $productimage = basename($_FILES["productimage"]["name"]);
    $targetFilePath = $targetDir . $productimage;

    // Upload file to server
    if (move_uploaded_file($_FILES["productimage"]["tmp_name"], $targetFilePath)) {
        // Insert data into tblproduct
        $product_date = date('Y-m-d H:i:s');
        $sql_product = "INSERT INTO tblproduct (productname, categoryid, supplierid, pricein, priceout, productimage, productdate, statusid, barcode) 
                        VALUES ('$productname', '$categoryid', '$supplierid', '$pricein', '$priceout', '$productimage', '$product_date', '$statusid', '$barcode')";

        if ($conn->query($sql_product) === TRUE) {
            $productid = $conn->insert_id; // Get the product ID of the newly inserted product

            // Insert data into tblpurchase
            $purchase_date = date('Y-m-d H:i:s');
            $sql_purchase = "INSERT INTO tblpurchase (productid, supplierid, quantity, purchaseprice, purchasedate) 
                             VALUES ('$productid', '$supplierid', '$quantity', '$pricein', '$purchase_date')";
            if ($conn->query($sql_purchase) === TRUE) {

                // Update tblinventory
                $sql_inventory = "INSERT INTO tblinventory (productid, stocklevel) 
                                  VALUES ('$productid', '$quantity') 
                                  ON DUPLICATE KEY UPDATE stocklevel = stocklevel + '$quantity'";
                if ($conn->query($sql_inventory) === TRUE) {

                    // Optionally, insert into tblinventorylog
                    $change_reason = 'Purchased';
                    $sql_inventorylog = "INSERT INTO tblinventorylog (productid, changeamount, changedate, reason, statusid) 
                                         VALUES ('$productid', '$quantity', '$purchase_date', '$change_reason', 2)";
                    $conn->query($sql_inventorylog);

                    echo "Product added, purchase recorded, and inventory updated successfully.";
                } else {
                    echo "Error updating inventory: " . $conn->error;
                }
            } else {
                echo "Error recording purchase: " . $conn->error;
            }
        } else {
            echo "Error adding product: " . $conn->error;
        }
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>