<?php
include("../../config/database.php");

$sql = "SELECT s.supplierid, s.suppliername, p.positionname, s.contactname, s.address, s.phonenumber, s.email, s.website, s.telegram, s.photo, st.statusname
FROM tblsupplier s
JOIN tblstatus st ON s.statusid = st.statusid
JOIN tblposition p ON s.positionid = p.positionid
WHERE s.statusid = 1;
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