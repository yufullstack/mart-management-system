<?php
header('Content-Type: application/json');
include("../../config/database.php");

$response = ['success' => false, 'error' => 'An unknown error occurred'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['file']['tmp_name'];

        $handle = fopen($file, "r");

        if ($handle !== FALSE) {
            fgetcsv($handle); // Skip the header row

            $sql = "INSERT INTO tblinventorylog (productid, changeamount, reason, statusid) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt === FALSE) {
                $response['error'] = 'Failed to prepare SQL statement';
            } else {
                $stmt->bind_param("isss", $productid, $changeamount, $reason, $statusid);
                $importSuccess = true;

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    error_log("CSV Data: " . print_r($data, true)); // Log the CSV data for debugging

                    if (isset($data[1], $data[2], $data[4], $data[5])) {
                        $productid = $data[1];
                        $changeamount = (int)$data[2];
                        $reason = $data[4];
                        $statusid = (int)$data[5];

                        // Check if productid exists in tblproduct
                        $checkProductId = $conn->prepare("SELECT COUNT(*) FROM tblproduct WHERE productid = ?");
                        $checkProductId->bind_param("s", $productid);
                        $checkProductId->execute();
                        $checkProductId->bind_result($productCount);
                        $checkProductId->fetch();
                        $checkProductId->close();

                        // Check if statusid exists in tblstatus
                        $checkStatusId = $conn->prepare("SELECT COUNT(*) FROM tblstatus WHERE statusid = ?");
                        $checkStatusId->bind_param("i", $statusid);
                        $checkStatusId->execute();
                        $checkStatusId->bind_result($statusCount);
                        $checkStatusId->fetch();
                        $checkStatusId->close();

                        if ($productCount > 0 && $statusCount > 0) {
                            if (!$stmt->execute()) {
                                $importSuccess = false;
                                $response['error'] = 'Error importing record: ' . $stmt->error;
                                break;
                            }
                        } else {
                            // $importSuccess = false;
                            // $response['error'] = "Error: Invalid productid $productid or statusid $statusid.";
                            break;
                        }
                    } else {
                        $importSuccess = false;
                        $response['error'] = 'Error: Missing required data fields in CSV. Row data: ' . json_encode($data);
                        break;
                    }
                }

                $stmt->close();
                fclose($handle);

                if ($importSuccess) {
                    $response = ['success' => true];
                }
            }
        } else {
            $response['error'] = 'Failed to open CSV file';
        }
    } else {
        $response['error'] = 'No file uploaded or file upload error';
    }
} else {
    $response['error'] = 'Invalid request method';
}

echo json_encode($response);
$conn->close();
?>