<?php

include("../../config/database.php");

// Fetch data from your database table
$sql = "SELECT c.customerid, c.customername, se.sexen, c.address, c.phonenumber, c.created, st.statusname
FROM tblcustomer c
JOIN tblsex se ON c.sexid = se.sexid
JOIN tblstatus st ON c.statusid = st.statusid
WHERE c.statusid = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output CSV headers
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=customer_data.csv');
    $output = fopen('php://output', 'w');

    // Output CSV column headers
    $columns = array("ID", "Name", "Gender", "Address", "Phone Number", "Created", "Status");
    fputcsv($output, $columns);

    // Output CSV data rows
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
} else {
    echo "No data available";
}

$conn->close();
?>