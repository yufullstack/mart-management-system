<?php

include("../../config/database.php");

// Fetch data from your database table
$sql = "SELECT s.supplierid, s.suppliername, s.contactname, p.positionname, s.address, s.phonenumber, s.email, s.website, s.telegram, s.photo, st.statusname
FROM tblsupplier s
JOIN tblposition p ON s.positionid = p.positionid
JOIN tblstatus st ON s.statusid = st.statusid
WHERE s.statusid = 1;
";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output CSV headers
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=supplier_data.csv');
    $output = fopen('php://output', 'w');

    // Output CSV column headers
    $columns = array("ID", "Name", "Contact Name", "Position", "Address", "Phone Number", "Email", "Website", "Telegram", "Photo", "Status");
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