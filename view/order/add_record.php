<?php
include("../../config/database.php");

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employeename = $_POST['employeename'];
    $positionid = $_POST['positionid'];
    $sexid = $_POST['sexid'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $phonenumber = $_POST['phonenumber'];
    $email = $_POST['email'];
    $telegram = $_POST['telegram'];
    $statusid = $_POST['statusid'];

    $targetDir = "../../public/img/";
    $photo = basename($_FILES["photo"]["name"]);
    $targetFilePath = $targetDir . $photo;

    // Upload file to server
    if(move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFilePath)){
        // Insert data into database
        $sql = "INSERT INTO tblemployee (employeename, positionid, sexid, dob, address, phonenumber, email, telegram, statusid, photo) 
                VALUES ('$employeename', '$positionid', '$sexid', '$dob', '$address', '$phonenumber', '$email', '$telegram', '$statusid', '$photo')";
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>