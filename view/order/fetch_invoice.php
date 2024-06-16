<?php
// Include the database connection file
include ('../../config/database.php');

// Fetch the last order with names instead of IDs
$sql = "
    SELECT 
        o.orderid, o.orderdate, o.duedate, o.totalamount, o.discount AS order_discount, o.statusid AS order_statusid,
        od.orderdetailid, od.productid, od.quantity, od.unitprice, od.discount AS detail_discount, od.statusid AS detail_statusid,
        p.paymentid, p.paymentmethodid, p.amount, p.paymentdate, p.transactionid,
        e.employeename, e.email, e.phonenumber AS employee_phone, 
        c.customername, c.address, c.phonenumber AS customer_phone
    FROM 
        tblorder o
    JOIN 
        tblorderdetail od ON o.orderid = od.orderid
    JOIN 
        tblpayment p ON o.orderid = p.orderid
    JOIN 
        tblemployee e ON o.employeeid = e.employeeid
    LEFT JOIN 
        tblcustomer c ON o.customerid = c.customerid
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
    <div class='container'>
        <div class='card-header border-bottom border-2'>
            <div class='row'>
                <div class='col-6'>
                    <h2 class='text-primary'>Blue Mart</h2>
                    Invoice #: {$order['orderid']}
                </div>
                <div class='col-6 d-flex justify-content-end'>
                    <div class='text-start'>
                        <p>Order Date: {$order['orderdate']}</p>
                        <p>Due Date: {$order['duedate']}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class='card-body'>
            <div class='row border-bottom border-2 mb-4 py-4'>
                <div class='col-6'>
                    <h5 class='m-0 pb-2'>From:</h5>
                    <p class='m-0'>{$order['employeename']}</p>
                    <p class='m-0'>{$order['email']}</p>
                    <p class='m-0'>+885{$order['employee_phone']}</p>
                </div>
                <div class='col-6 d-flex justify-content-end'>
                    <div class='text-start'>
                        <h5 class='m-0 pb-2'>To:</h5>";

    // Check if customer is a walk-in customer
    if ($order['customername'] === null) {
        echo "
                        <p class='m-0'>Walk-in Customer</p>";
    } else {
        echo "
                        <p class='m-0'>{$order['customername']}</p>
                        <p class='m-0'>{$order['address']}</p>
                        <p class='m-0'>+885{$order['customer_phone']}</p>";
    }

    echo "
                    </div>
                </div>
            </div>
            
            <div class='table-responsive'>
                <table class='table'>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th>Discount</th>
                            <th>Total</th>
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
    $sub_total = 0;
    while ($detail = $details_result->fetch_assoc()) {
        // Calculate the total for each product considering the discount is in percentage
        $product_total = $detail['unitprice'] * $detail['quantity'] * (1 - $detail['discount'] / 100);
        $sub_total += $product_total;
        echo "<tr>
                <td>{$detail['productname']}</td>
                <td>{$detail['unitprice']}</td>
                <td>{$detail['quantity']}</td>
                <td>{$detail['discount']}%</td>
                <td>" . number_format($product_total, 2) . "</td>
              </tr>";
    }

    // Calculate the overall total after applying the customer discount
    $overall_total = $sub_total * (1 - $order['order_discount'] / 100);

    echo "          </tbody>
                </table>
            </div>
            <div class='d-flex justify-content-end'>
                <h4>Subtotal: " . number_format($sub_total, 2) . "</h4>
            </div>
            <div class='d-flex justify-content-end'>
                <h4>Customer Discount: {$order['order_discount']}%</h4>
            </div>
            <div class='d-flex justify-content-end'>
                <h4>Total: " . number_format($overall_total, 2) . "</h4>
            </div>
        </div>
    </div>";
} else {
    echo "<div class='container my-5'><div class='alert alert-warning'>No orders found.</div></div>";
}

$conn->close();
?>