<?php
// Include the database connection file
include ('../../config/database.php');

// SQL query to join the tables
$sql = "SELECT 
            o.orderid, o.orderdate, o.employeeid, o.customerid, o.discount AS order_discount, o.totalamount, o.statusid AS order_statusid,
            od.orderdetailid, od.productid, od.quantity, od.unitprice, od.discount AS detail_discount, od.statusid AS detail_statusid,
            p.paymentid, p.paymentmethodid, p.amount, p.paymentstatus, p.paymentdate, p.transactionid
        FROM 
            tblorder o
        JOIN 
            tblorderdetail od ON o.orderid = od.orderid
        JOIN 
            tblpayment p ON o.orderid = p.orderid";

// Execute the query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    echo "<table border='1'>
            <tr>
                <th>Order ID</th>
                <th>Order Date</th>
                <th>Employee ID</th>
                <th>Customer ID</th>
                <th>Order Discount</th>
                <th>Total Amount</th>
                <th>Order Status ID</th>
                <th>Order Detail ID</th>
                <th>Product ID</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Detail Discount</th>
                <th>Detail Status ID</th>
                <th>Payment ID</th>
                <th>Payment Method ID</th>
                <th>Amount</th>
                <th>Payment Status</th>
                <th>Payment Date</th>
                <th>Transaction ID</th>
            </tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>".$row["orderid"]."</td>
                <td>".$row["orderdate"]."</td>
                <td>".$row["employeeid"]."</td>
                <td>".$row["customerid"]."</td>
                <td>".$row["order_discount"]."</td>
                <td>".$row["totalamount"]."</td>
                <td>".$row["order_statusid"]."</td>
                <td>".$row["orderdetailid"]."</td>
                <td>".$row["productid"]."</td>
                <td>".$row["quantity"]."</td>
                <td>".$row["unitprice"]."</td>
                <td>".$row["detail_discount"]."</td>
                <td>".$row["detail_statusid"]."</td>
                <td>".$row["paymentid"]."</td>
                <td>".$row["paymentmethodid"]."</td>
                <td>".$row["amount"]."</td>
                <td>".$row["paymentstatus"]."</td>
                <td>".$row["paymentdate"]."</td>
                <td>".$row["transactionid"]."</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

$conn->close();
?>