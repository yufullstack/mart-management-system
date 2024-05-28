<?php
include("../../config/database.php");

$sql = "SELECT e.employeeid, e.employeename, p.positionname, se.sexen, e.dob, e.address, e.phonenumber, e.email, e.telegram, e.photo, e.created, st.statusname
FROM tblemployee e
JOIN tblposition p ON e.positionid = p.positionid
JOIN tblsex se ON e.sexid = se.sexid
JOIN tblstatus st ON e.statusid = st.statusid
WHERE e.statusid = 1;
";
$result = $conn->query($sql);

$data = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
$conn->close();

echo json_encode(array("data" => $data));
?>