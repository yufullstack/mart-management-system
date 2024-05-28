<?php
include("../../config/database.php");

$sql = "SELECT p.productid, p.productname, c.categoryname, s.suppliername, p.quantity, p.pricein, p.priceout, p.instock, p.productimage, p.productdate, st.statusname
FROM tblproduct p
JOIN tblcategory c ON p.categoryid = c.categoryid
JOIN tblsupplier s ON p.supplierid = s.supplierid
JOIN tblstatus st ON p.statusid = st.statusid
WHERE p.statusid = 1;
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