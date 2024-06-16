<?php
include ('../../includes/script.php');
include ('../../config/database.php');
$sql_product = "SELECT * FROM tblproduct";
$result_product = mysqli_query($conn, $sql_product);
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <title>Promotion</title>

    <meta name="description" content="" />

</head>

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
                                <i class="fas fa-plus p-0 pe-2"></i> Add Promotion
                            </button>
                        </div>

                        <table id="example" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Discount ID</th>
                                    <th>Product</th>
                                    <th>Discount Value</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
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
                                    <h5 class="modal-title" id="addModalLabel">Add Promotion</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="addForm" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="addProductId" class="form-label">Product Product</label>
                                                <select id="addProductId" name="productid" class="product-select">
                                                    <option value="">Select Product</option>
                                                    <?php
                                                        while ($row = mysqli_fetch_assoc($result_product)) {
                                                            echo "<option value='{$row['productid']}'>{$row['productname']}</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="addDiscountValue" class="form-label">Discount Value</label>
                                                <input type="number" step="0.01" class="form-control"
                                                    id="addDiscountValue" name="discountvalue" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="addStartDate" class="form-label">Start Date</label>
                                                <input type="date" class="form-control" id="addStartDate"
                                                    name="startdate" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="addEndDate" class="form-label">End Date</label>
                                                <input type="date" class="form-control" id="addEndDate" name="enddate"
                                                    required>
                                            </div>
                                        </div>
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
                                    <h5 class="modal-title" id="editModalLabel">Edit Record</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editForm" enctype="multipart/form-data">
                                        <div class="row">
                                            <input type="hidden" id="editId" name="discountid">
                                            <div class="col-md-6 mb-3">
                                                <label for="editProductId" class="form-label">Product ID</label>
                                                <input type="text" class="form-control" id="editProductId"
                                                    name="productid" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="editDiscountValue" class="form-label">Discount Value</label>
                                                <input type="number" step="0.01" class="form-control"
                                                    id="editDiscountValue" name="discountvalue" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="editStartDate" class="form-label">Start Date</label>
                                                <input type="date" class="form-control" id="editStartDate"
                                                    name="startdate" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="editEndDate" class="form-label">End Date</label>
                                                <input type="date" class="form-control" id="editEndDate" name="enddate"
                                                    required>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Promotion Details Modal -->
                    <div class="modal fade" id="promotionModal" tabindex="-1" aria-labelledby="promotionModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="promotionModalLabel">Promotion Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="promotionId" class="form-label">Discount ID</label>
                                                <input type="text" class="form-control" id="promotionId" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="promotionProductName" class="form-label">Product
                                                    Name</label>
                                                <input type="text" class="form-control" id="promotionProductName"
                                                    readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="promotionDiscountValue" class="form-label">Discount
                                                    Value</label>
                                                <input type="text" class="form-control" id="promotionDiscountValue"
                                                    readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="promotionStartDate" class="form-label">Start Date</label>
                                                <input type="text" class="form-control" id="promotionStartDate"
                                                    readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="promotionEndDate" class="form-label">End Date</label>
                                                <input type="text" class="form-control" id="promotionEndDate" readonly>
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
        include ("../../public/js/bootstrap_datatable.php");
        ?>
        <script src="../../public/js/promotion.js"></script>
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