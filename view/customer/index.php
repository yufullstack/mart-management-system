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

    <title>Customer</title>

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
                                    <th>Gender</th>
                                    <th>Address</th>
                                    <th>Phone Number</th>
                                    <th>Created</th>
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
                                    <h5 class="modal-title" id="addModalLabel">Add New Record</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="addForm" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="addName" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="addName" name="customername"
                                                    required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="addGender" class="form-label">Gender</label>
                                                <select class="form-control" id="addGender" name="sexid"
                                                    required></select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="addAddress" class="form-label">Address</label>
                                                <input type="text" class="form-control" id="addAddress" name="address"
                                                    required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="addPhoneNumber" class="form-label">Phone Number</label>
                                                <input type="text" class="form-control" id="addPhoneNumber"
                                                    name="phonenumber" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="addStatus" class="form-label">Status</label>
                                                <select class="form-control" id="addStatus" name="statusid"
                                                    required></select>
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
                                            <input type="hidden" id="editId" name="customerid">
                                            <div class="col-md-6 mb-3">
                                                <label for="editName" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="editName"
                                                    name="customername" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="editGender" class="form-label">Gender</label>
                                                <select class="form-control" id="editGender" name="sexid"
                                                    required></select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="editAddress" class="form-label">Address</label>
                                                <input type="text" class="form-control" id="editAddress" name="address"
                                                    required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="editPhoneNumber" class="form-label">Phone Number</label>
                                                <input type="text" class="form-control" id="editPhoneNumber"
                                                    name="phonenumber" required>
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

                    <!-- Customer Details Modal -->
                    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="customerModalLabel">Customer Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="customerId" class="form-label">ID</label>
                                                <input type="text" class="form-control" id="customerId" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="customerName" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="customerName" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="customerGender" class="form-label">Gender</label>
                                                <input type="text" class="form-control" id="customerGender" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="customerAddress" class="form-label">Address</label>
                                                <input type="text" class="form-control" id="customerAddress" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="customerPhoneNumber" class="form-label">Phone Number</label>
                                                <input type="text" class="form-control" id="customerPhoneNumber"
                                                    readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="customerCreated" class="form-label">Created</label>
                                                <input type="text" class="form-control" id="customerCreated" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="customerStatus" class="form-label">Status</label>
                                                <input type="text" class="form-control" id="customerStatus" readonly>
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
        <script src="../../public/js/customer.js"></script>
        <script src="../../public/vendor/js/menu.js"></script>
        <!-- Main JS -->
        <script src="../../public/js/main.js"></script>
        <!-- GitHub Buttons JS -->
        <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>