<?php
include ('../../config/database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['productid'];
    $change_amount = $_POST['changeamount'];
    $adjustment_type = $_POST['adjustmentType'];
    $reason = $_POST['reason'];
    $current_date = date('Y-m-d');  // Use current date for log entry

    // Fetch supplier ID and purchase price based on product ID
    $sql_fetch_product = "SELECT supplierid, pricein FROM tblproduct WHERE productid = ?";
    $stmt_fetch_product = $conn->prepare($sql_fetch_product);
    if ($stmt_fetch_product === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt_fetch_product->bind_param("i", $product_id);
    if (!$stmt_fetch_product->execute()) {
        die('Execute failed: ' . htmlspecialchars($stmt_fetch_product->error));
    }
    $result_product = $stmt_fetch_product->get_result();
    $product_data = $result_product->fetch_assoc();

    if ($adjustment_type == 'add') {
        // Handle as a purchase
        $supplier_id = $product_data['supplierid'];
        $purchase_price = $product_data['pricein'];
        $purchase_date = $current_date;

        // Insert purchase record into tblpurchase
        $sql_insert_purchase = "INSERT INTO tblpurchase (productid, supplierid, quantity, purchaseprice, purchasedate) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert_purchase = $conn->prepare($sql_insert_purchase);
        if ($stmt_insert_purchase === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }
        $stmt_insert_purchase->bind_param("iiids", $product_id, $supplier_id, $change_amount, $purchase_price, $purchase_date);
        if (!$stmt_insert_purchase->execute()) {
            die('Execute failed: ' . htmlspecialchars($stmt_insert_purchase->error));
        }
    } elseif ($adjustment_type == 'subtract') {
        // Handle as a stock adjustment due to damage
        $change_amount = -$change_amount;
    }

    // Update the stock level in the inventory
    $sql_update = "UPDATE tblinventory SET stocklevel = stocklevel + ? WHERE productid = ?";
    $stmt_update = $conn->prepare($sql_update);
    if ($stmt_update === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt_update->bind_param("ii", $change_amount, $product_id);
    if (!$stmt_update->execute()) {
        die('Execute failed: ' . htmlspecialchars($stmt_update->error));
    }

    // Log the inventory change in tblinventorylog
    $sql_insert_log = "INSERT INTO tblinventorylog (productid, changeamount, changedate, reason, statusid) VALUES (?, ?, NOW(), ?, ?)";
    $status_id = $adjustment_type == 'add' ? 1 : 2;  // Assuming 1 for add, 2 for subtract
    $stmt_insert_log = $conn->prepare($sql_insert_log);
    if ($stmt_insert_log === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt_insert_log->bind_param("iisi", $product_id, $change_amount, $reason, $status_id);
    if (!$stmt_insert_log->execute()) {
        die('Execute failed: ' . htmlspecialchars($stmt_insert_log->error));
    }

    echo json_encode(["message" => "Stock level updated and change logged successfully."]);

    $stmt_update->close();
    $stmt_insert_log->close();
    if (isset($stmt_insert_purchase)) {
        $stmt_insert_purchase->close();
    }
    $stmt_fetch_product->close();
    $conn->close();
}
?>