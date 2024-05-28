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

    <title>Product</title>

    <meta name="description" content="" />

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
                            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                                data-bs-target="#addModal">
                                <i class="fas fa-plus p-0 pe-2"></i> Insert
                            </button>
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
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Supplier</th>
                                    <th>Price In</th>
                                    <th>Price Out</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <!-- Add Modal -->
                    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addModalLabel">Add New Product</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="addForm" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="addProductName" class="form-label">Product Name</label>
                                            <input type="text" class="form-control" id="addProductName"
                                                name="productname" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="addCategory" class="form-label">Category</label>
                                            <select class="form-select" id="addCategory" name="categoryid" required>

                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="addSupplier" class="form-label">Supplier</label>
                                            <select class="form-control" id="addSupplier" name="supplierid"
                                                required></select>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="addQuantity" class="form-label">Quantity</label>
                                                <input type="number" class="form-control" id="addQuantity"
                                                    name="quantity" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="addPriceIn" class="form-label">Price In</label>
                                                <input type="number" step="0.01" class="form-control" id="addPriceIn"
                                                    name="pricein" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="addPriceOut" class="form-label">Price Out</label>
                                                <input type="number" step="0.01" class="form-control" id="addPriceOut"
                                                    name="priceout" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="addInStock" class="form-label">In Stock</label>
                                                <input type="number" class="form-control" id="addInStock" name="instock"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="addProductImage" class="form-label">Product Image</label>
                                            <input type="file" class="form-control" id="addProductImage"
                                                name="productimage" required>
                                        </div>
                                        <!-- You can add more input fields for other columns as needed -->
                                        <button type="submit" class="btn btn-primary">Save</button>
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
                                    <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editForm" enctype="multipart/form-data">
                                        <input type="hidden" id="editProductId" name="productid">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="editName" class="form-label">Product Name</label>
                                                    <input type="text" class="form-control" id="editName"
                                                        name="productname" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="editCategory" class="form-label">Category</label>
                                                    <select class="form-control" id="editCategory" name="categoryid"
                                                        required>
                                                        <!-- Options for categories will be populated dynamically -->
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="editSupplier" class="form-label">Supplier</label>
                                                    <select class="form-control" id="editSupplier" name="supplierid"
                                                        required>
                                                        <!-- Options for suppliers will be populated dynamically -->
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="editQuantity" class="form-label">Quantity</label>
                                                    <input type="number" class="form-control" id="editQuantity"
                                                        name="quantity" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="editPriceIn" class="form-label">Price In</label>
                                                    <input type="number" class="form-control" id="editPriceIn"
                                                        name="pricein" step="0.01" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="editPriceOut" class="form-label">Price Out</label>
                                                    <input type="number" class="form-control" id="editPriceOut"
                                                        name="priceout" step="0.01" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editInStock" class="form-label">In Stock</label>
                                            <input type="number" class="form-control" id="editInStock" name="instock"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editProductImage" class="form-label">Product Image</label>
                                            <input type="file" class="form-control" id="editProductImage"
                                                name="productimage">
                                        </div>
                                        <div class="mb-3">
                                            <label for="editStatus" class="form-label">Status</label>
                                            <select class="form-control" id="editStatus" name="statusid" required>
                                                <!-- Options for status will be populated dynamically -->
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Product Details Modal -->
                    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="productModalLabel">Product Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form>
                                        <div class="text-center mb-3">
                                            <img id="productImage" src="" alt="Product Image" class="img-fluid"
                                                style="width: 150px; height: 150px;">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="productId" class="form-label">Product ID</label>
                                                <input type="text" class="form-control" id="productId" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="productName" class="form-label">Product Name</label>
                                                <input type="text" class="form-control" id="productName" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="productCategory" class="form-label">Category</label>
                                                <input type="text" class="form-control" id="productCategory" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="productSupplier" class="form-label">Supplier</label>
                                                <input type="text" class="form-control" id="productSupplier" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="productQuantity" class="form-label">Quantity</label>
                                                <input type="text" class="form-control" id="productQuantity" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="productPriceIn" class="form-label">Price In</label>
                                                <input type="text" class="form-control" id="productPriceIn" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="productPriceOut" class="form-label">Price Out</label>
                                                <input type="text" class="form-control" id="productPriceOut" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="productInStock" class="form-label">In Stock</label>
                                                <input type="text" class="form-control" id="productInStock" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="productDate" class="form-label">Product Date</label>
                                                <input type="text" class="form-control" id="productDate" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="productStatus" class="form-label">Status</label>
                                                <input type="text" class="form-control" id="productStatus" readonly>
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

        <?php
            include("../../public/js/bootstrap_datatable.php");
        ?>
        <script src="../../public/js/product.js"></script>

        <script src="../../public/vendor/js/menu.js"></script>

        <!-- Main JS -->
        <script src="../../public/js/main.js"></script>
        <!-- GitHub Buttons JS -->
        <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
<!-- <script></script> -->

</html>