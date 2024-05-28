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

            $sql = "INSERT INTO tblsupplier (suppliername, contactname, positionid, address, phonenumber, email, website, telegram, photo, statusid)  
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt === FALSE) {
                $response['error'] = 'Failed to prepare SQL statement: ' . $conn->error;
            } else {
                $stmt->bind_param("ssissssssi", $supplierName, $contactName, $positionId, $address, $phoneNumber, $email, $website, $telegram, $photo, $statusId);
                $importSuccess = true;

                $rowNumber = 1;
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $rowNumber++;
                    if (isset($data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10])) {
                        $supplierName = $data[1];
                        $contactName = $data[2];
                        $positionId = (int)$data[3];
                        $address = $data[4];
                        $phoneNumber = $data[5];
                        $email = $data[6];
                        $website = $data[7];
                        $telegram = $data[8];
                        $photo = $data[9];
                        $statusId = (int)$data[10];

                        // Check for duplicates
                        $checkDuplicate = $conn->prepare("SELECT COUNT(*) FROM tblsupplier WHERE suppliername = ? OR email = ?");
                        if ($checkDuplicate === FALSE) {
                            $importSuccess = false;
                            $response['error'] = 'Failed to prepare duplicate check statement: ' . $conn->error;
                            break;
                        }
                        $checkDuplicate->bind_param("ss", $supplierName, $email);
                        $checkDuplicate->execute();
                        $checkDuplicate->bind_result($count);
                        $checkDuplicate->fetch();
                        $checkDuplicate->close();

                        if ($count > 0) {
                            $importSuccess = false;
                            $response['error'] = "Duplicate entry found for supplier: $supplierName or email: $email at row $rowNumber.";
                            break;
                        }

                        // Validate positionId
                        if ($positionId <= 0) {
                            $importSuccess = false;
                            $response['error'] = "Error: Invalid positionid $positionId at row $rowNumber for supplier: $supplierName.";
                            break;
                        }

                        // Insert data if no duplicates and positionId is valid
                        if (!$stmt->execute()) {
                            $importSuccess = false;
                            $response['error'] = 'Error importing record for supplier: ' . $supplierName . ' - ' . $stmt->error;
                            break;
                        }
                    } else {
                        $importSuccess = false;
                        $response['error'] = 'Error: Missing required data fields in CSV at row ' . $rowNumber . '.';
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