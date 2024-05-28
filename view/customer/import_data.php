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

            $sql = "INSERT INTO tblcustomer (customername, sexid, address, phonenumber, statusid) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt === FALSE) {
                $response['error'] = 'Failed to prepare SQL statement';
            } else {
                $stmt->bind_param("sissi", $name, $sexId, $address, $phoneNumber, $statusId);
                $importSuccess = true;

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    error_log("CSV Data: " . print_r($data, true)); // Log the CSV data for debugging

                    if (isset($data[1], $data[2], $data[3], $data[4], $data[6])) {
                        $name = $data[1];
                        $sexId = (int)$data[2];
                        $address = $data[3];
                        $phoneNumber = $data[4];
                        $statusId = (int)$data[6];

                        // Check if sexId exists in tblsex
                        $checkSexId = $conn->prepare("SELECT COUNT(*) FROM tblsex WHERE sexid = ?");
                        $checkSexId->bind_param("i", $sexId);
                        $checkSexId->execute();
                        $checkSexId->bind_result($count);
                        $checkSexId->fetch();
                        $checkSexId->close();

                        if ($count > 0) {
                            if (!$stmt->execute()) {
                                $importSuccess = false;
                                $response['error'] = 'Error importing record: ' . $stmt->error;
                                break;
                            }
                        } else {
                            $importSuccess = false;
                            $response['error'] = "Error: Invalid sexid $sexId.";
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