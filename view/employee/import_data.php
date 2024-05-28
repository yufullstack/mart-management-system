<?php
header('Content-Type: application/json');
include("../../config/database.php");

$response = ['success' => false, 'error' => 'An unknown error occurred'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['file']['tmp_name'];

        $handle = fopen($file, "r");

        if ($handle !== FALSE) {
            fgetcsv($handle); 

            $sql = "INSERT INTO tblemployee (employeename, positionid, sexid, dob, address, phonenumber, email, telegram, photo, statusid) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt === FALSE) {
                $response['error'] = 'Failed to prepare SQL statement';
            } else {
                $stmt->bind_param("siissssssi", $name, $positionId, $sexId, $dob, $address, $phoneNumber, $email, $telegram, $photo, $statusId);
                $importSuccess = true;

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (isset($data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[11])) {
                        $name = $data[1];
                        $positionId = (int)$data[2];
                        $sexId = (int)$data[3];
                        $dob = date('Y-m-d', strtotime($data[4]));
                        $address = $data[5];
                        $phoneNumber = $data[6];
                        $email = $data[7];
                        $telegram = $data[8];
                        $photo = $data[9];
                        $statusId = (int)$data[11];

                        error_log("Read positionId: " . $positionId);

                        $checkPosition = $conn->prepare("SELECT COUNT(*) FROM tblposition WHERE positionid = ?");
                        $checkPosition->bind_param("i", $positionId);
                        $checkPosition->execute();
                        $checkPosition->bind_result($count);
                        $checkPosition->fetch();
                        $checkPosition->close();

                        if ($count > 0) {
                            if (!$stmt->execute()) {
                                $importSuccess = false;
                                $response['error'] = 'Error importing record: ' . $stmt->error;
                                break;
                            }
                        } else {
                            // $importSuccess = false;
                            // $response['error'] = "Error: Invalid positionid $positionId.";
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