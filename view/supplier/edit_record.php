<?php
include("../../config/database.php");

$supplierid = $_POST['supplierid'];
$suppliername = $_POST['suppliername'];
$contactname = $_POST['contactname'];
$positionid = $_POST['positionid'];
$address = $_POST['address'];
$phonenumber = $_POST['phonenumber'];
$email = $_POST['email'];
$website = $_POST['website'];
$telegram = $_POST['telegram'];
$statusid = $_POST['statusid'];

$targetDir = "../../public/img/";
$photo = $_FILES['photo']['name'];
$targetFilePath = $targetDir . basename($photo);

if (!empty($photo)) {
    // New file uploaded
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFilePath)) {
        $sql = "UPDATE tblsupplier SET 
                suppliername='$suppliername', 
                contactname='$contactname', 
                positionid='$positionid', 
                address='$address', 
                phonenumber='$phonenumber', 
                email='$email', 
                website='$website', 
                telegram='$telegram', 
                statusid='$statusid', 
                photo='$photo' 
                WHERE supplierid='$supplierid'";
    } else {
        echo "Error uploading file.";
        exit;
    }
} else {
    // No new file uploaded, keep the old file
    $sql = "UPDATE tblsupplier SET 
            suppliername='$suppliername', 
            contactname='$contactname', 
            positionid='$positionid', 
            address='$address', 
            phonenumber='$phonenumber', 
            email='$email', 
            website='$website', 
            telegram='$telegram', 
            statusid='$statusid' 
            WHERE supplierid='$supplierid'";
}

if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>