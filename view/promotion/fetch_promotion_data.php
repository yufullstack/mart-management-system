<?php
include("../../config/database.php");

$sql = "SELECT d.discountid, p.productname, d.discountvalue, d.startdate, d.enddate
        FROM tbldiscount d
        JOIN tblproduct p ON d.productid = p.productid";

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