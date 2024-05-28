<?php
include("../../config/database.php");

$sql = "SELECT c.customerid, c.customername, c.address, se.sexen, c.phonenumber, c.created, st.statusname
FROM tblcustomer c
JOIN tblsex se ON c.sexid = se.sexid
JOIN tblstatus st ON c.statusid = st.statusid
WHERE c.statusid = 1";

$result = $conn->query($sql);

$data = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$conn->close();

echo json_encode(array("data" => $data));
?>