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

            $sql = "INSERT INTO tblcategory (categoryname, created, statusid) 
                    VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt === FALSE) {
                $response['error'] = 'Failed to prepare SQL statement';
            } else {
                $stmt->bind_param("ssi", $categoryname, $created, $statusid);
                $importSuccess = true;

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    error_log("CSV Data: " . print_r($data, true)); // Log the CSV data for debugging

                    if (isset($data[0], $data[1], $data[2], $data[3])) {
                        $categoryname = $data[1];
                        $created = $data[2];
                        $statusid = (int)$data[3];

                        // Check if statusid exists in tblstatus
                        $checkStatusId = $conn->prepare("SELECT COUNT(*) FROM tblstatus WHERE statusid = ?");
                        $checkStatusId->bind_param("i", $statusid);
                        $checkStatusId->execute();
                        $checkStatusId->bind_result($count);
                        $checkStatusId->fetch();
                        $checkStatusId->close();

                        if ($count > 0) {
                            if (!$stmt->execute()) {
                                $importSuccess = false;
                                $response['error'] = 'Error importing record: ' . $stmt->error;
                                break;
                            }
                        } else {
                            $importSuccess = false;
                            $response['error'] = "Error: Invalid statusid $statusid.";
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