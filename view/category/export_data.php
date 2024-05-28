<?php
include("../../config/database.php");

// Fetch data from your database table
$sql = "SELECT c.categoryid, c.categoryname, c.created, s.statusname
        FROM tblcategory c
        JOIN tblstatus s ON c.statusid = s.statusid";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output CSV headers
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=category_data.csv');
    $output = fopen('php://output', 'w');

    // Output CSV column headers
    $columns = array("Category ID", "Category Name", "Created", "Status Name");
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