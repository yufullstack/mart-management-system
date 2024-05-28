<?php

include("../../config/database.php");

// Fetch data from your database table
$sql = "SELECT 
            p.productid, 
            p.productname, 
            c.categoryname, 
            s.suppliername, 
            p.quantity, 
            p.pricein, 
            p.priceout, 
            p.instock, 
            p.productimage, 
            p.productdate, 
            st.statusname
        FROM tblproduct p
        JOIN tblcategory c ON p.categoryid = c.categoryid
        JOIN tblsupplier s ON p.supplierid = s.supplierid
        JOIN tblstatus st ON p.statusid = st.statusid
        WHERE p.statusid = 1;
";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output CSV headers
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=product_data.csv');
    $output = fopen('php://output', 'w');

    // Output CSV column headers
    $columns = array(
        "Product ID", "Product Name", "Category", "Supplier", "Quantity", 
        "Price In", "Price Out", "In Stock", "Product Image", "Product Date", "Status"
    );
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