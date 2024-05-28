<?php
include('../../config/database.php');

// Check connection
if (!$conn) {
    die(json_encode(array("error" => "Connection failed: " . mysqli_connect_error())));
}

// Define the query with JOINs
$query = "
    SELECT 
        il.*, 
        p.productname, 
        s.statusname 
    FROM 
        tblinventorylog il
    LEFT JOIN 
        tblproduct p ON il.productid = p.productid
    LEFT JOIN 
        tblstatus s ON il.statusid = s.statusid
";

// Execute the query
$result = mysqli_query($conn, $query);

// Check if the query execution was successful
if ($result) {
    $data = array();
    // Fetch data and store it in the array
    while($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    // Output the data as a JSON object
    echo json_encode(array("data" => $data));
} else {
    // Output an error message as a JSON object
    echo json_encode(array("error" => "Query failed: " . mysqli_error($conn)));
}

// Close the database connection
mysqli_close($conn);
?>