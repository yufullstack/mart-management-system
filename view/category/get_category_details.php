<?php
include("../../config/database.php");

if (isset($_GET['categoryid'])) {
    $categoryid = $_GET['categoryid'];

    // Fetch category details
    $query = "SELECT * FROM tblcategory WHERE categoryid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $categoryid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
        echo json_encode($category);
    } else {
        echo json_encode(['error' => 'Category not found']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>