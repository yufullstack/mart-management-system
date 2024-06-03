<?php
include("../../config/database.php");

$sql = "SELECT 
            o.orderid, 
            o.orderdate, 
            o.employeeid, 
            e.employeename, 
            o.customerid, 
            c.customername, 
            o.discount, 
            o.totalamount, 
            s.statusname 
        FROM 
            tblorder o
        JOIN 
            tblemployee e ON o.employeeid = e.employeeid
        JOIN 
            tblcustomer c ON o.customerid = c.customerid
        JOIN 
            tblstatus s ON o.statusid = s.statusid
        WHERE 
            o.statusid = 1;
";

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