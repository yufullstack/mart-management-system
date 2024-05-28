<?php
include('../../config/database.php');
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=inventory_log.csv');
$output = fopen('php://output', 'w');

// Write the column headers
fputcsv($output, array('Log ID', 'Product ID', 'Change Amount', 'Change Date', 'Reason', 'Status Name'));

// Select data from tblinventorylog and join with the status table to get status names
$query = "SELECT l.logid, l.productid, l.changeamount, l.changedate, l.reason, s.statusname 
          FROM tblinventorylog l
          JOIN tblstatus s ON l.statusid = s.statusid";
$result = mysqli_query($conn, $query);

// Write data to the CSV file
while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row);
}

fclose($output);
?>