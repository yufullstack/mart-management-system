<?php
include("../../config/database.php");

$sql = "SELECT i.inventoryid, p.productname, i.stocklevel
        FROM tblinventory i
        JOIN tblproduct p ON i.productid = p.productid";

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