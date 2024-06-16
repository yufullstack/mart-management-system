<?php
include ('../../includes/script.php');
include ('../../config/database.php');

// Fetch categories and product counts from the database
$query = "SELECT tblcategory.categoryid, tblcategory.categoryname, COUNT(tblproduct.productid) AS productcount 
          FROM tblcategory 
          LEFT JOIN tblproduct ON tblcategory.categoryid = tblproduct.categoryid 
          GROUP BY tblcategory.categoryid, tblcategory.categoryname";
$result = mysqli_query($conn, $query);
$categories = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = [
            'id' => $row['categoryid'],
            'name' => $row['categoryname'],
            'count' => $row['productcount'],
        ];
    }
}
$sql_customer = "SELECT * FROM tblcustomer";
$result_customer = mysqli_query($conn, $sql_customer);

// Add 'All Categories' option
$totalProductCount = array_sum(array_column($categories, 'count'));
array_unshift($categories, [
    'id' => 0,
    'name' => 'All Categories',
    'count' => $totalProductCount,
]);

// Fetch all products for initial display
$query = "SELECT tblproduct.*, tblcategory.categoryname FROM tblproduct 
          JOIN tblcategory ON tblproduct.categoryid = tblcategory.categoryid";
$result = mysqli_query($conn, $query);
$allProducts = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $allProducts[] = $row;
    }
}



?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Order</title>

    <meta name="description" content="" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">


</head>
<style>
body {
    width: 100%;
    height: 100vh;
}

.main-content {
    width: 100%;
    display: flex;
    height: 90%;
}

header {
    width: 100%;
}


.list-img {
    width: 50px;
    height: 60px;
}

.list-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.quantity-group .quantity-input {
    display: none;
}


.product-section {
    flex: 1;
    padding: 20px;
    overflow-y: scroll;
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.product-section::-webkit-scrollbar {
    display: none;
}


.cart-section {
    width: 700px;
    display: flex;
    padding: 20px;
}



#cart {
    flex-grow: 1;
    overflow-y: auto;
    -ms-overflow-style: none;
    /* IE and Edge */
    scrollbar-width: none;
    /* Firefox */
}

#cart::-webkit-scrollbar {
    display: none;
    /* Chrome, Safari, Opera */
}

.check-out {
    background-color: white;
    /* Optional: To match the bg-navbar-theme or give a different color */
    /* padding: 15px; */
    width: 100%;
}

.form-container {
    display: flex;
    flex-direction: column;
    height: 100%;
}


.category-btn-group {
    background-color: rgba(0, 0, 0, 0.1);
    padding: 5px 10px;
    border-radius: 8px;
    background-color: #ffffff;
}

.category-btn {
    border: none;
    color: #6c757d;
    font-weight: 500;
    border-radius: 8px;
    transition: all 0.3s ease-in-out;
}

.category-btn:hover {
    background-color: #e9ecef;
}

.category-btn.active {
    background-color: #696CFF;
    color: white;
    border: 1px solid #007bff;
}


.card {
    padding: 10px 0;
    width: 100%;
    height: 220px;
}

.card-top {
    width: 100%;
    height: 60%;
}

.card-body {
    width: 100%;
    height: 40%;
}

.img-cover {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.choices__inner {
    border: 0;
    padding: 7px 0 0 20px;
    color: #000;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    border-radius: 0.375rem;
    border: 1.5px solid #D9DEE3;
}

.choices__inner:focus-within {
    outline: none;
    border-color: #000;
    box-shadow: 0 0 0 0.25rem rgba(236, 72, 153, 0.25);
    border-radius: 0.375rem;
}


.choices__list--dropdown .choices__item--selectable {
    color: #000;

}

.form-input {
    background: transparent;
    border: none;
    outline: none;
}

.no-border {
    border: none;
}

.no-border:focus {
    outline: none !important;
}

/* invoice prin */
.invoice-box {
    max-width: 800px;
    margin: auto;
    padding: 30px;
    font-size: 16px;
    line-height: 24px;
    font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    color: #555;
}

.invoice-box table {
    width: 100%;
    line-height: inherit;
    text-align: left;
}

.invoice-box table td {
    padding: 5px;
    vertical-align: top;
}

.invoice-box table tr td:nth-child(2) {
    text-align: right;
}

.invoice-box table tr.top table td {
    padding-bottom: 20px;
}

.invoice-box table tr.information table td {
    padding-bottom: 40px;
}

.invoice-box table tr.heading td {
    background: #eee;
    border-bottom: 1px solid #ddd;
    font-weight: bold;
}

.invoice-box table tr.details td {
    padding-bottom: 20px;
}

.invoice-box table tr.item td {
    border-bottom: 1px solid #eee;
}

.invoice-box table tr.item.last td {
    border-bottom: none;
}

.invoice-box table tr.total td:nth-child(2) {
    border-top: 2px solid #eee;
    font-weight: bold;
}

@media only screen and (max-width: 600px) {
    .invoice-box table tr.top table td {
        width: 100%;
        display: block;
        text-align: center;
    }

    .invoice-box table tr.information table td {
        width: 100%;
        display: block;
        text-align: center;
    }
}
</style>

<body>
    <header>
        <div class="row bg-light border-bottom py-3 px-4 m-0">
            <div class="col-3 text-white align-self-center  fs-4">
                <input type="text" id="productSearch" class="form-control search"
                    placeholder="Scan barcode or type product name" autocomplete="off">
            </div>
            <div class="col-3 align-self-center  fs-4">
                <button type="button" class="btn btn-primary mx-2" onclick="exportData()">
                    <i class="fas fa-file-export p-0 pe-2"></i> Today's Sale
                </button>
            </div>
            <div class="col-6 align-self-center text-end">
                <a href="/view/order/index.php" class=" fs-4"><i class="fa-solid fa-x"></i></a>
            </div>
        </div>
    </header>

    <div class="main-content">
        <div class="product-section">
            <div class="category-btn-group mb-1 d-flex flex-wrap m-0">
                <?php foreach ($categories as $category): ?>
                <button type="button" class="btn category-btn mx-1 my-1" data-category="<?= $category['name'] ?>">
                    <?= $category['name'] ?>
                </button>
                <?php endforeach; ?>
            </div>
            <div class="row" id="productList">
                <!-- display product -->
            </div>
        </div>


        <!-- Add Modal -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Add New Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addForm_customer" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="addName" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="addName" name="customername" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="addGender" class="form-label">Gender</label>
                                    <select class="form-control" id="addGender" name="sexid" required></select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="addAddress" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="addAddress" name="address" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="addPhoneNumber" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="addPhoneNumber" name="phonenumber"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="addStatus" class="form-label">Status</label>
                                    <select class="form-control" id="addStatus" name="statusid" required></select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Cart Section -->
        <div class="cart-section border-start border-4 border-primary bg-navbar-theme mt-3 me-3 rounded">
            <form id="addForm" enctype="multipart/form-data" class="form-container">
                <div class="row">
                    <!-- <div class="col-5">
                        <label for="employeeid" class="form-label">Employee</label>
                    </div> -->
                    <input type="hidden" class="form-control" value="1" name="employeeid" required>
                    <div class="col-5">
                        <label for="customerid" class="form-label">Customer</label>
                        <select id="customerid" name="customerid" class="product-select">
                            <option value="">Select Customer</option>
                            <?php
                        while ($row = mysqli_fetch_assoc($result_customer)) {
                                        echo "<option value='{$row['customerid']}'>{$row['customername']} - {$row['phonenumber']}</option>";
                        }
                    ?>
                        </select>
                    </div>
                    <div class="col-2 pt-4 align-self-center">
                        <button type="button" class="btn bg-transparent" data-bs-toggle="modal"
                            data-bs-target="#addModal">
                            <i class="fa-solid fa-user-plus fs-3 text-primary"></i>
                        </button>
                    </div>
                    <div class="col-5">
                        <label for="addDiscount" class="form-label">Customer's Discount %</label>
                        <input type="number" class="form-control pt-2 pb-2" name="discount" id="addDiscount" value="0">
                    </div>
                </div>
                <div class="row m-2">
                    <span class="col-2">Items</span>
                    <span class="col-2">Price</span>
                    <span class="col-2">Discount</span>
                    <span class="col-3">Quantity</span>
                    <span class="col-3 text-center">Total</span>
                    <hr>
                </div>
                <div id="cart" class="mb-3">
                </div>
                <div class="mt-auto check-out">
                    <div class="row">
                        <div class="col-2">
                            <span for="totalBeforeDiscount" class="">SubTotal</span>
                            <input type="hidden" id="totalBeforeDiscount" class="form-input text-end" readonly>
                        </div>
                        <div class="col-3">
                            <span for="productDiscountAmount" class="">Product's Discount</span>
                            <input type="hidden" id="productDiscountAmount" class="form-input text-end" readonly>
                        </div>
                        <div class="col-4">
                            <span for="customerDiscountAmount" class="">Customer's Discount</span>
                            <input type="hidden" id="customerDiscountAmount" class="form-input text-end" readonly>
                        </div>
                        <div class="col-3">
                            <span for="totalDiscountAmount" class="">Total Discount</span>
                            <input type="hidden" id="totalDiscountAmount" class="form-input text-end" readonly>
                        </div>
                        <hr class="my-2">
                        <div class="col-2">
                            <span for="totalbeforediscountdisplay" id="totalbeforediscountdisplay" class=""></span>
                        </div>
                        <div class="col-3">
                            <span for="productDiscountAmountdisplay" id="productDiscountAmountdisplay" class=""></span>
                        </div>
                        <div class="col-4">
                            <span for="customerDiscountAmountdisplay" id="customerDiscountAmountdisplay"
                                class=""></span>
                        </div>
                        <div class="col-3">
                            <span for="totalDiscountAmountdisplay" id="totalDiscountAmountdisplay" class=""></span>
                        </div>
                        <hr class="my-2">
                        <div class="col-12 d-flex justify-content-evenly align-items-center">
                            <span class="px-5 border-start">PAYABLE </span>
                            <span class="px-5 border-end" for="totalAmountdisplay" id="totalAmountdisplay"></span>
                            <input type="hidden" class="form-input text-end" id="totalAmount" name="totalamount"
                                step="0.01" readonly>
                        </div>
                        <hr class="my-2">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 m-0">Checkout Payment</button>
                </div>
            </form>
        </div>



        <!-- Payment Modal -->
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">Complete Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <hr>
                    <div class="modal-body">
                        <form id="paymentForm">
                            <input type="hidden" id="paymentOrderId" name="orderid">
                            <div class="row">
                                <div class="col-10">
                                    <div id="paymentMethodsContainer">
                                        <div class="payment-entry">
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <label for="paymentAmount1" class="form-label">Payment Amount
                                                        1</label>
                                                    <input type="number" class="form-control" id="paymentAmount1"
                                                        name="paymentamount[]" required step="0.01">
                                                </div>
                                                <div class="col-6">
                                                    <label for="paymentMethod1" class="form-label">Payment Method
                                                        1</label>
                                                    <select class="form-select" id="paymentMethod1"
                                                        name="paymentmethodid[]" required></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3 m-0 p-0 pt-5">
                                        <div class="col-12 p-0 m-0 mb-3">
                                            <button type="button" class="btn btn-primary w-100"
                                                id="addPaymentMethod">Add
                                                More Payment</button>
                                        </div>
                                        <div
                                            class="col-6 d-flex   justify-content-between align-items-center border border-end-0 border-primary p-3 m-0">
                                            <span>Total Items</span>
                                            <span id="totalItems">0.00</span>
                                        </div>
                                        <div
                                            class="col-6 d-flex   justify-content-between align-items-center border border-primary p-3 m-0">
                                            <span>Refunds</span>
                                            <span id="totalrefund">0.00</span>
                                            <input type="hidden" id="refundAmount" name="refund" readonly>
                                        </div>
                                        <div
                                            class="col-12 d-flex   justify-content-center align-items-center border border-top-0 border-primary p-3 m-0">
                                            <span class="me-2">Total Payable</span>
                                            <span id="totalamount_display">0.00</span>
                                            <input type="hidden" id="totalPayment" name="totalpayment" readonly>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary w-100">Submit
                                                Payment</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <label for="" class="form-label">Quick Cash</label>
                                    <div class="list-group border border-primary rounded-0">
                                        <!-- exact amount -->
                                        <button type="button" id="exactAmountButton"
                                            class="btn btn-primary list-group-item border-bottom border-primary text-white"></button>
                                        <button type="button"
                                            class="btn btn-light list-group-item border-bottom border-primary quick-cash-btn"
                                            data-amount="10">10.00</button>
                                        <button type="button"
                                            class="btn btn-light list-group-item border-bottom border-primary quick-cash-btn"
                                            data-amount="20">20.00</button>
                                        <button type="button"
                                            class="btn btn-light list-group-item border-bottom border-primary quick-cash-btn"
                                            data-amount="50">50.00</button>
                                        <button type="button"
                                            class="btn btn-light list-group-item border-bottom border-primary quick-cash-btn"
                                            data-amount="100">100.00</button>
                                        <button type="button"
                                            class="btn btn-light list-group-item border-bottom border-primary quick-cash-btn"
                                            data-amount="500">500.00</button>
                                        <button type="button"
                                            class="btn btn-light list-group-item border-bottom border-primary quick-cash-btn"
                                            data-amount="1000">1000.00</button>
                                        <button type="button" class="btn btn-light list-group-item quick-cash-btn"
                                            data-amount="5000">5000.00</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Modal -->
        <div class='modal fade' id='invoiceModal' tabindex='-1' aria-labelledby='invoiceModalLabel' aria-hidden='true'>
            <div class='modal-dialog modal-lg'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body' id='invoice-box'>
                        <!-- Invoice content will be populated here -->
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                        <button type='button' class='btn btn-primary' id='printInvoice'>Print Invoice</button>
                    </div>
                </div>
            </div>
        </div>





    </div>
    <?php
    include ("../../public/js/bootstrap_datatable.php");
    ?>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script src="../../public/js/test.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script src="../../public/vendor/js/menu.js"></script>


    <!-- Main JS -->
    <script src="../../public/js/main.js"></script>
    <!-- GitHub Buttons JS -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectElements = document.querySelectorAll('.product-select');
        selectElements.forEach(function(selectElement) {
            new Choices(selectElement, {
                removeItemButton: true,
                searchEnabled: true,
                searchChoices: true,
                shouldSort: false,
                placeholder: true,
                placeholderValue: 'Select a product',
            });
        });
    });
    </script>
</body>

</html>