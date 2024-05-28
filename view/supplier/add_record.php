<?php
include("../../config/database.php");

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
    $photo = basename($_FILES["photo"]["name"]);
    $targetFilePath = $targetDir . $photo;

    // Upload file to server
    if(move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFilePath)){
        // Insert data into database
        $sql = "INSERT INTO tblsupplier (supplierid, suppliername, contactname, positionid, address, phonenumber, email, website, telegram, photo, statusid) 
                VALUES ('$supplierid', '$suppliername', '$contactname', '$positionid', '$address', '$phonenumber', '$email', '$website', '$telegram', '$photo', '$statusid')";
        if ($conn->query($sql) === TRUE) {
            echo "New supplier record created successfully";
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