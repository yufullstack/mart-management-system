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
}

.main-content {
    width: 100%;
    display: flex;
    height: 100vh;
}

.quantity-group .quantity-display {
    border: none;
    background: transparent;
    pointer-events: none;
    width: 10px;
}

.quantity-group {
    border: 1.5px solid #000;
    border-radius: 5px;
}

.btn-add {
    background: transparent;
    border: none;
    padding: 0 10px;
    border-left: 1.5px solid #000;
    font-size: 20px;
    font-weight: 500;
}

.btn-sub {
    background: transparent;
    border: none;
    padding: 0 10px;
    border-right: 1.5px solid #000;
    font-size: 20px;
    font-weight: 500;
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
    /* Hide scrollbar for IE, Edge and Firefox */
    -ms-overflow-style: none;
    /* IE and Edge */
    scrollbar-width: none;
    /* Firefox */
}

.product-section::-webkit-scrollbar {
    display: none;
    /* Hide scrollbar for Chrome, Safari and Opera */
}

/* .cart-section {
    display: flex;
    position: sticky;
    top: 0;
    height: 100%;
    overflow-y: scroll;
    -ms-overflow-style: none;
    scrollbar-width: none;
} */

.cart-section {
    width: 550px;
    display: flex;
    padding: 20px;
    flex-direction: column;
    height: 100vh;
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
    padding: 15px;
    width: 100%;
}

.form-container {
    display: flex;
    flex-direction: column;
    height: 100%;
}



/* .check-out {
    position: sticky;
    bottom: 90px;
    background-color: white;
} */

/* .category-btn-group {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
} */

.category-btn-group {
    /* background-color: #f8f9fa; */
    background-color: rgba(0, 0, 0, 0.1);
    padding: 5px 10px;
    border-radius: 8px;
    /* Rounded corners for the container */
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
</style>

<body>
    <header>
        <div class="row bg-light border-bottom py-3 px-4">
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
            <div class="category-btn-group mb-2 d-flex flex-wrap">
                <?php foreach ($categories as $category): ?>
                <button type="button" class="btn category-btn mx-2 my-2" data-category="<?= $category['name'] ?>">
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
        <div class="cart-section border-start border-5 border-primary bg-navbar-theme my-3 me-3 rounded">
            <form id="addForm" enctype="multipart/form-data" class="form-container">
                <div class="row">
                    <div class="col-5">
                        <label for="employeeid" class="form-label">Employee</label>
                        <input type="text" class="form-control" name="employeeid" required>
                    </div>
                    <div class="col-5">
                        <label for="customerid" class="form-label">Customer</label>
                        <select id="customerid" name="customerid" class="product-select" required>
                            <option value="">Select Customer</option>
                            <?php
                                while ($row = mysqli_fetch_assoc($result_customer)) {
                                                echo "<option value='{$row['customerid']}'>{$row['customername']} - {$row['phonenumber']}</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-2 align-self-center">
                        <button type="button" class="btn bg-transparent" data-bs-toggle="modal"
                            data-bs-target="#addModal">
                            <i class="fa-solid fa-user-plus fs-3"></i>
                        </button>
                    </div>
                    <div class="col-5">
                        <label for="addDiscount" class="form-label">Discount</label>
                        <input type="number" class="form-control" name="discount" id="addDiscount" value="0">
                    </div>
                </div>
                <div class="row m-2">
                    <span class="col-2">Products</span>
                    <span class="col-2">Price</span>
                    <span class="col-2">Discount</span>
                    <span class="col-2">Quantity</span>
                    <span class="col-2">Total</span>
                    <hr>
                </div>
                <div id="cart" class="mb-3">
                </div>
                <div class="mt-auto check-out">
                    <div class="row border-1">
                        <hr>
                        <div class="col-6">
                            <label for="totalBeforeDiscount" class="form-label">SUBTOTAL:</label>
                        </div>
                        <div class="col-6">
                            <input type="text" id="totalBeforeDiscount" class="form-input text-end" readonly>
                        </div>
                        <div class="col-6">
                            <label for="customerDiscountAmount" class="form-label">Product's Discount Amount:</label>
                        </div>
                        <div class="col-6">
                            <input type="text" id="productDiscountAmount" class="form-input text-end" readonly>
                        </div>
                        <div class="col-6">
                            <label for="customerDiscountAmount" class="form-label">Customer's Discount Amount:</label>
                        </div>
                        <div class="col-6">
                            <input type="text" id="customerDiscountAmount" class="form-input text-end" readonly>
                        </div>
                        <div class="col-6">
                            <label for="totalDiscountAmount" class="form-label">Total Discount Amount:</label>
                        </div>
                        <div class="col-6">
                            <input type="text" id="totalDiscountAmount" class="form-input text-end" readonly>
                        </div>
                        <hr>
                        <div class="col-6">
                            <label for="totalAmount" class="form-label">PAYABLE AMOUNT</label>
                        </div>
                        <div class="col-6">
                            <input type="text" class="form-input text-end" id="totalAmount" name="totalamount"
                                step="0.01" readonly>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Complete Order</button>
                </div>
            </form>
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