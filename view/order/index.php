<?php
    include('../../includes/script.php');
    include('../../config/database.php');
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
.img-fit {
    width: 100%;
    height: 100%;
    object-fit: cover;
    /* or any other value you want */
}
</style>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <?php include ("../../includes/menu.php"); ?>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <?php include ("../../includes/navbar.php"); ?>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="mb-3">
                            <!-- <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                                data-bs-target="#addModal">
                                <i class="fas fa-plus p-0 pe-2"></i> New Order
                            </button> -->
                            <a href="pos_interface.php" class="btn btn-primary me-2"><i
                                    class="fas fa-plus p-0 pe-2"></i> New Order</a>
                            <button type="button" class="btn btn-primary mx-2" onclick="exportData()">
                                <i class="fas fa-file-export p-0 pe-2"></i> Export Data
                            </button>
                            <input type="file" id="importFile" class="d-none" accept=".csv"
                                onchange="importData(this.files[0])">
                            <button type="button" class="btn btn-primary mx-2"
                                onclick="document.getElementById('importFile').click()">
                                <i class="fas fa-file-import p-0 pe-2"></i> Import Data
                            </button>
                        </div>

                        <table id="example" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Order Date</th>
                                    <th>Employee</th>
                                    <th>Customer</th>
                                    <th>Discount</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>


                    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addModalLabel">Add New Order</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="addForm" enctype="multipart/form-data">
                                        <!-- Order Details -->
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="addEmployee" class="form-label">Employee ID</label>
                                                    <select class="form-control" id="addEmployee" name="employeeid"
                                                        required></select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="addCustomer" class="form-label">Customer ID</label>
                                                    <select class="form-control" id="addCustomer" name="customerid"
                                                        required></select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <!-- Add Discount Input -->
                                                <div class="mb-3">
                                                    <label for="addDiscount" class="form-label">Discount (%)</label>
                                                    <input type="number" class="form-control" id="addDiscount"
                                                        name="discount" value="0" min="0" max="100" step="0.01">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Products Selection -->
                                        <!-- Products Selection -->
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="addProduct" class="form-label">Select Product</label>
                                                    <!-- <select class="product-select form-control" id="addProduct"
                                                        onchange="fetchProductInfo(this.value)">
                                                        <option value="">Select Product</option>
                                                    </select> -->
                                                    <input id="productSearch" class="form-control" type="text"
                                                        placeholder="Search for products..." autocomplete="off">
                                                    <ul id="productList" onchange="fetchProductInfo(this.value)"
                                                        class="autocomplete-items"></ul>
                                                </div>
                                            </div>
                                        </div>



                                        <!-- Products Details -->
                                        <div id="productsContainer"></div>
                                        <div class="mb-3">
                                            <label for="addTotalAmount" class="form-label">Total Amount</label>
                                            <input type="number" class="form-control" id="addTotalAmount"
                                                name="totalamount" step="0.01" readonly>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Complete Order</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Order</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editForm" enctype="multipart/form-data">
                                        <input type="hidden" id="editId" name="orderid">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="editOrderDate" class="form-label">Order Date</label>
                                                    <input type="date" class="form-control" id="editOrderDate"
                                                        name="orderdate" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="editEmployeeId" class="form-label">Employee ID</label>
                                                    <input type="text" class="form-control" id="editEmployeeId"
                                                        name="employeeid" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="editCustomerId" class="form-label">Customer ID</label>
                                                    <input type="text" class="form-control" id="editCustomerId"
                                                        name="customerid" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="editDiscount" class="form-label">Customer's
                                                        Discount</label>
                                                    <input type="number" class="form-control" id="editDiscount"
                                                        name="discount" step="0.01" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="editTotalAmount" class="form-label">Total Amount</label>
                                                    <input type="number" class="form-control" id="editTotalAmount"
                                                        name="totalamount" step="0.01" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="editStatus" class="form-label">Status</label>
                                                    <select class="form-select" id="editStatus" name="statusid"
                                                        required></select>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- displays -->
                    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="orderModalLabel">Order Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form>

                                        <div class="text-center mb-3">
                                            <img id="orderPhoto" src="" alt="Order Photo"
                                                class="img-fluid rounded-circle img-fit"
                                                style="width: 150px; height: 150px;">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="orderId" class="form-label">Order ID</label>
                                                <input type="text" class="form-control" id="orderId" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="orderDate" class="form-label">Order Date</label>
                                                <input type="text" class="form-control" id="orderDate" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="orderEmployeeId" class="form-label">Employee ID</label>
                                                <input type="text" class="form-control" id="orderEmployeeId" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="orderCustomerId" class="form-label">Customer ID</label>
                                                <input type="text" class="form-control" id="orderCustomerId" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="orderDiscount" class="form-label">Discount</label>
                                                <input type="text" class="form-control" id="orderDiscount" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="orderTotalAmount" class="form-label">Total Amount</label>
                                                <input type="text" class="form-control" id="orderTotalAmount" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="orderStatus" class="form-label">Status</label>
                                                <input type="text" class="form-control" id="orderStatus" readonly>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <!-- / Layout page -->
            </div>

            <!-- Overlay -->
            <div class="layout-overlay layout-menu-toggle"></div>
        </div>
        <script>
        </script>
        <?php
            include("../../public/js/bootstrap_datatable.php");
            ?>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <script src="../../public/js/order.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

        <script src="../../public/vendor/js/menu.js"></script>


        <!-- Main JS -->
        <script src="../../public/js/main.js"></script>
        <!-- GitHub Buttons JS -->
        <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
<!-- <script></script> -->

</html>