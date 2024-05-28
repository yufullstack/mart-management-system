<?php

include("../../config/database.php");

// Fetch data from your database table
$sql = "SELECT e.employeeid, e.employeename, p.positionname, se.sexen, e.dob, e.address, e.phonenumber, e.email, e.telegram, e.photo, e.created, st.statusname
FROM tblemployee e
JOIN tblposition p ON e.positionid = p.positionid
JOIN tblsex se ON e.sexid = se.sexid
JOIN tblstatus st ON e.statusid = st.statusid
WHERE e.statusid = 1;
";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output CSV headers
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=employee_data.csv');
    $output = fopen('php://output', 'w');

    // Output CSV column headers
    $columns = array("ID", "Name", "Position", "Gender", "Date of Birth", "Address", "Phone Number", "Email", "Telegram", "Photo", "Created", "Status");
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