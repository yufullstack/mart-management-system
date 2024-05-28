<?php
include("../../config/database.php");

$employeeid = $_POST['employeeid'];
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
$photo = $_FILES['photo']['name'];
$targetFilePath = $targetDir . basename($photo);

if (!empty($photo)) {
    // New file uploaded
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFilePath)) {
        $sql = "UPDATE tblemployee SET employeename='$employeename', positionid='$positionid', sexid='$sexid', dob='$dob', address='$address', phonenumber='$phonenumber', email='$email', telegram='$telegram', statusid='$statusid', photo='$photo' WHERE employeeid='$employeeid'";
    } else {
        echo "Error uploading file.";
        exit;
    }
} else {
    // No new file uploaded, keep the old file
    $sql = "UPDATE tblemployee SET employeename='$employeename', positionid='$positionid', sexid='$sexid', dob='$dob', address='$address', phonenumber='$phonenumber', email='$email', telegram='$telegram', statusid='$statusid' WHERE employeeid='$employeeid'";
}

if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>