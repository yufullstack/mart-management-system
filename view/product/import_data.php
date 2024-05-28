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

            $sql = "INSERT INTO tblproduct (productname, categoryid, supplierid, quantity, pricein, priceout, instock, productimage, productdate, statusid) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt === FALSE) {
                $response['error'] = 'Failed to prepare SQL statement';
            } else {
                $stmt->bind_param("siiiddisss", $productname, $categoryid, $supplierid, $quantity, $pricein, $priceout, $instock, $productimage, $productdate, $statusid);
                $importSuccess = true;

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (isset($data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10])) {
                       
                        $productname = $data[1];
                        $categoryid = (int)$data[2];
                        $supplierid = (int)$data[3];
                        $quantity = (int)$data[4];
                        $pricein = (float)$data[5];
                        $priceout = (float)$data[6];
                        $instock = (int)$data[7];
                        $productimage = $data[8];
                        $productdate = date('Y-m-d', strtotime($data[9]));
                        $statusid = (int)$data[10];

                        // Validate categoryid and supplierid
                        $checkCategory = $conn->prepare("SELECT COUNT(*) FROM tblcategory WHERE categoryid = ?");
                        $checkCategory->bind_param("i", $categoryid);
                        $checkCategory->execute();
                        $checkCategory->bind_result($categoryCount);
                        $checkCategory->fetch();
                        $checkCategory->close();

                        $checkSupplier = $conn->prepare("SELECT COUNT(*) FROM tblsupplier WHERE supplierid = ?");
                        $checkSupplier->bind_param("i", $supplierid);
                        $checkSupplier->execute();
                        $checkSupplier->bind_result($supplierCount);
                        $checkSupplier->fetch();
                        $checkSupplier->close();

                        if ($categoryCount > 0 && $supplierCount > 0) {
                            if (!$stmt->execute()) {
                                $importSuccess = false;
                                $response['error'] = 'Error importing record: ' . $stmt->error;
                                break;
                            }
                        } else {
                            $importSuccess = false;
                            $response['error'] = "Error: Invalid categoryid $categoryid or supplierid $supplierid.";
                            break;
                        }
                    } else {
                        $importSuccess = false;
                        $response['error'] = 'Error: Missing required data fields in CSV.';
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