<?php
include("../../config/database.php");

$sql = "SELECT c.categoryid, c.categoryname, c.created, st.statusname
FROM tblcategory c
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