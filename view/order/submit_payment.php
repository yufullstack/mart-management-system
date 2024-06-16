<?php
include ('../../config/database.php');

if (isset($_POST['orderid'], $_POST['paymentmethodid'], $_POST['paymentamount'], $_POST['totalpayment'])) {
    $orderid = $_POST['orderid'];
    $paymentmethods = $_POST['paymentmethodid'];
    $paymentamounts = $_POST['paymentamount'];
    $totalpayment = $_POST['totalpayment'];
    $paymentdate = date('Y-m-d H:i:s');
    $paymentstatus = 'completed';

    // Function to generate transaction ID based on payment method
    function generateTransactionId($paymentmethodid) {
        $prefix = '';
        switch ($paymentmethodid) {
            case 1:
                $prefix = 'CASH';
                break;
            case 2:
                $prefix = 'ABA';
                break;
            case 3:
                $prefix = 'WING';
                break;
            case 4:
                $prefix = 'ACLEDA';
                break;
            default:
                $prefix = 'UNKNOWN';
        }
        return $prefix . '-' . uniqid();
    }

    $conn->begin_transaction();
    try {
        $totalGiven = 0;
        $paymentCount = count($paymentmethods);

        // Calculate total given amount
        for ($i = 0; $i < $paymentCount; $i++) {
            $totalGiven += $paymentamounts[$i];
        }

        $amountRemaining = $totalpayment;

        for ($i = 0; $i < $paymentCount; $i++) {
            $paymentmethodid = $paymentmethods[$i];
            $paymentamount = $paymentamounts[$i];
            $transactionid = generateTransactionId($paymentmethodid); // Generate transaction ID

            if ($amountRemaining > 0) {
                // Only insert up to the amount remaining
                $amountToInsert = min($paymentamount, $amountRemaining);
                $sql = "INSERT INTO tblpayment (orderid, paymentmethodid, amount, paymentstatus, paymentdate, transactionid) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('iidsss', $orderid, $paymentmethodid, $amountToInsert, $paymentstatus, $paymentdate, $transactionid);
                $stmt->execute();

                // Reduce the remaining amount by the inserted amount
                $amountRemaining -= $amountToInsert;
            }
        }

        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Required parameters missing.']);
}
?>