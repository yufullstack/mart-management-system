<?php
include("../../config/database.php");

if (isset($_GET['employeeid'])) {
    $employeeid = $_GET['employeeid'];

    // Fetch employee details
    $query = "SELECT * FROM tblemployee WHERE employeeid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $employeeid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        echo json_encode($employee);
    } else {
        echo json_encode(['error' => 'Employee not found']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>