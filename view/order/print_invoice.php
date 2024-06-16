<?php
// Include the database connection file
include ('../../config/database.php');

// Fetch the last order with names instead of IDs
$sql = "
    SELECT 
        o.orderid, o.orderdate, o.totalamount, o.statusid AS order_statusid,
        od.orderdetailid, od.productid, od.quantity, od.unitprice, od.discount AS detail_discount, od.statusid AS detail_statusid,
        p.paymentid, p.paymentmethodid, p.amount, p.paymentstatus, p.paymentdate, p.transactionid,
        e.employeename, c.customername, pm.paymentmethodname
    FROM 
        tblorder o
    JOIN 
        tblorderdetail od ON o.orderid = od.orderid
    JOIN 
        tblpayment p ON o.orderid = p.orderid
    JOIN 
        tblemployee e ON o.employeeid = e.employeeid
    JOIN 
        tblcustomer c ON o.customerid = c.customerid
    JOIN 
        tblpaymentmethod pm ON p.paymentmethodid = pm.paymentmethodid
    ORDER BY 
        o.orderid DESC 
    LIMIT 1";

// Execute the query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch the order details
    $order = $result->fetch_assoc();
    
    // Display the invoice
    echo "
    <div class='container my-5'>
        <div class='card'>
            <div class='card-header'>
                <h2>Blue Mart</h2>
                <div class='d-flex justify-content-between'>
                    <p>Invoice #: {$order['orderid']}</p>
                    <p>Created: {$order['orderdate']}</p>
                </div>
            </div>
            <div class='card-body'>
                <div class='row mb-3'>
                    <div class='col'>
                        <h5>Customer</h5>
                        <p>{$order['customername']}</p>
                    </div>
                    <div class='col'>
                        <h5>Employee</h5>
                        <p>{$order['employeename']}</p>
                    </div>
                </div>
                <div class='row mb-3'>
                    <div class='col'>
                        <h6>Payment Method</h6>
                        <p>{$order['paymentmethodname']}</p>
                    </div>
                    <div class='col'>
                        <h6>Payment Status</h6>
                        <p>{$order['paymentstatus']}</p>
                    </div>
                </div>
                <div class='table-responsive'>
                    <table class='table table-bordered'>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Discount</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>";
    
    // Fetch order details with product names
    $details_sql = "
        SELECT 
            od.productid, od.quantity, od.unitprice, pr.productname, od.discount
        FROM 
            tblorderdetail od
        JOIN 
            tblproduct pr ON od.productid = pr.productid
        WHERE 
            od.orderid = {$order['orderid']}
    ";
    $details_result = $conn->query($details_sql);
    while($detail = $details_result->fetch_assoc()) {
        echo "<tr>
                <td>{$detail['productname']}</td>
                <td>{$detail['quantity']}</td>
                <td>{$detail['discount']}</td>
                <td>{$detail['unitprice']}</td>
              </tr>";
    }

    echo "          </tbody>
                    </table>
                </div>
                <div class='d-flex justify-content-end'>
                    <h4>Total: {$order['totalamount']}</h4>
                </div>
            </div>
        </div>
    </div>
    ";
} else {
    echo "<div class='container my-5'><div class='alert alert-warning'>No orders found.</div></div>";
}

$conn->close();
?>