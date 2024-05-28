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

    <title>Supplier</title>

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
                                    <th>Supplier Name</th>
                                    <th>Contact Name</th>
                                    <th>Contact Title</th>
                                    <th>Phone Number</th>
                                    <th>Email</th>
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
                                    <h5 class="modal-title" id="addModalLabel">Add New Supplier</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="addForm" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="addName" class="form-label">Supplier Name</label>
                                                    <input type="text" class="form-control" id="addName"
                                                        name="suppliername" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="addContactName" class="form-label">Contact Name</label>
                                                    <input type="text" class="form-control" id="addContactName"
                                                        name="contactname" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="addPosition" class="form-label">Contact Title</label>
                                                    <select class="form-select" id="addPosition" name="positionid"
                                                        required></select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="addAddress" class="form-label">Address</label>
                                                    <input type="text" class="form-control" id="addAddress"
                                                        name="address" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="addPhoneNumber" class="form-label">Phone Number</label>
                                                    <input type="text" class="form-control" id="addPhoneNumber"
                                                        name="phonenumber" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="addEmail" class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="addEmail" name="email"
                                                        required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="addWebsite" class="form-label">Website</label>
                                                    <input type="text" class="form-control" id="addWebsite"
                                                        name="website" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="addTelegram" class="form-label">Telegram</label>
                                                    <input type="text" class="form-control" id="addTelegram"
                                                        name="telegram" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="addStatus" class="form-label">Status</label>
                                                    <select class="form-control" id="addStatus" name="statusid"
                                                        required></select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="addPhoto" class="form-label">Photo</label>
                                            <input type="file" class="form-control" id="addPhoto" name="photo" required>
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
                                    <h5 class="modal-title" id="editModalLabel">Edit Supplier</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editForm" enctype="multipart/form-data">
                                        <input type="hidden" id="editId" name="supplierid">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="editName" class="form-label">Supplier Name</label>
                                                    <input type="text" class="form-control" id="editName"
                                                        name="suppliername" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="editContactName" class="form-label">Contact Name</label>
                                                    <input type="text" class="form-control" id="editContactName"
                                                        name="contactname" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="editPosition" class="form-label">Contact Title</label>
                                                    <select class="form-select" id="editPosition" name="positionid"
                                                        required></select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="editAddress" class="form-label">Address</label>
                                                    <input type="text" class="form-control" id="editAddress"
                                                        name="address" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="editPhoneNumber" class="form-label">Phone Number</label>
                                                    <input type="text" class="form-control" id="editPhoneNumber"
                                                        name="phonenumber" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="editEmail" class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="editEmail" name="email"
                                                        required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="editWebsite" class="form-label">Website</label>
                                                    <input type="text" class="form-control" id="editWebsite"
                                                        name="website" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="editTelegram" class="form-label">Telegram</label>
                                                    <input type="text" class="form-control" id="editTelegram"
                                                        name="telegram" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="editStatus" class="form-label">Status</label>
                                                    <select class="form-control" id="editStatus" name="statusid"
                                                        required></select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editPhoto" class="form-label">Photo</label>
                                            <input type="file" class="form-control" id="editPhoto" name="photo">
                                            <div id="currentPhoto"></div>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- displays -->
                    <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="supplierModalLabel">Supplier Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form>

                                        <div class="text-center mb-3">
                                            <img id="photo" src="" alt="Supplier Photo"
                                                class="img-fluid rounded-circle img-fit"
                                                style="width: 150px; height: 150px;">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="supplierId" class="form-label">ID</label>
                                                <input type="text" class="form-control" id="supplierId" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="supplierName" class="form-label">Supplier Name</label>
                                                <input type="text" class="form-control" id="supplierName" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="contactName" class="form-label">Contact Name</label>
                                                <input type="text" class="form-control" id="contactName" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="positionname" class="form-label">Contact
                                                    Title</label>
                                                <input type="text" class="form-control" id="positionname" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="address" class="form-label">Address</label>
                                                <input type="text" class="form-control" id="address" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="phoneNumber" class="form-label">Phone Number</label>
                                                <input type="text" class="form-control" id="phoneNumber" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="text" class="form-control" id="email" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="website" class="form-label">Website</label>
                                                <input type="text" class="form-control" id="website" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="telegram" class="form-label">Telegram</label>
                                                <input type="text" class="form-control" id="telegram" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
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
            <div class="layout-overlay layout-menu-toggle"></div>
        </div>

        <?php
            include("../../public/js/bootstrap_datatable.php");
        ?>
        <script src="../../public/js/supplier.js"></script>

        <script src="../../public/vendor/js/menu.js"></script>


        <!-- Main JS -->
        <script src="../../public/js/main.js"></script>
        <!-- GitHub Buttons JS -->
        <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
<!-- <script></script> -->

</html>