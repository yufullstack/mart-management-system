<?php
include ('../../includes/script.php');
include ('../../config/database.php');
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Inventory Log</title>

    <meta name="description" content="" />
</head>
<style>
.img-fit {
    width: 100%;
    height: 100%;
    object-fit: cover;
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
                                    <th>Log ID</th>
                                    <th>Product ID</th>
                                    <th>Change Amount</th>
                                    <th>Change Date</th>
                                    <th>Reason</th>
                                    <th>Status ID</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
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
                                            <input type="hidden" id="editLogId" name="logid">
                                            <div class="col-md-6 mb-3">
                                                <label for="editProduct" class="form-label">Product Name</label>
                                                <select class="form-control" id="editProduct" name="productid"
                                                    required></select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="editChangeAmount" class="form-label">Change Amount</label>
                                                <input type="number" class="form-control" id="editChangeAmount"
                                                    name="changeamount" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="editReason" class="form-label">Reason</label>
                                                <input type="text" class="form-control" id="editReason" name="reason"
                                                    required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="editStatus" class="form-label">Status</label>
                                                <select class="form-control" id="editStatus" name="statusid"
                                                    required></select>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Log Details Modal -->
                    <div class="modal fade" id="inventoryLogModal" tabindex="-1"
                        aria-labelledby="inventoryLogModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="inventoryLogModalLabel">Inventory Log Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="logId" class="form-label">Log ID</label>
                                                <input type="text" class="form-control" id="logId" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="productName" class="form-label">Product Name</label>
                                                <input type="text" class="form-control" id="productName" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="changeAmount" class="form-label">Change Amount</label>
                                                <input type="text" class="form-control" id="changeAmount" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="changeDate" class="form-label">Change Date</label>
                                                <input type="text" class="form-control" id="changeDate" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="reason" class="form-label">Reason</label>
                                                <input type="text" class="form-control" id="reason" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="statusName" class="form-label">Status</label>
                                                <input type="text" class="form-control" id="statusName" readonly>
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
            <div class=" layout-overlay layout-menu-toggle"></div>
        </div>

        <?php
        include ("../../public/js/bootstrap_datatable.php");
        ?>
        <script src="../../public/js/inventory.js"></script>
        <script src="../../public/vendor/js/menu.js"></script>
        <!-- Main JS -->
        <script src="../../public/js/main.js"></script>
        <!-- GitHub Buttons JS -->
        <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>