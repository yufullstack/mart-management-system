<?php
include('../../includes/script.php');
include('../../config/database.php');

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
            // Add the Font Awesome icon class here
            'icon' => getCategoryIcon($row['categoryname'])
        ];
    }
}

// Add 'All Categories' option
$totalProductCount = array_sum(array_column($categories, 'count'));
array_unshift($categories, [
    'id' => 0,
    'name' => 'All Categories',
    'count' => $totalProductCount,
    'icon' => 'fas fa-utensils'
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

// Function to get the Font Awesome icon class based on category name
function getCategoryIcon($categoryName) {
    $icons = [
        'Burger' => 'fas fa-hamburger',
        'Chicken' => 'fas fa-drumstick-bite',
        'Beer' => 'fas fa-beer',
        'Vegetables' => 'fas fa-carrot',
        'All Categories' => 'fas fa-utensils'
    ];
    return isset($icons[$categoryName]) ? $icons[$categoryName] : 'fas fa-question';
}

// $sqlDiscount = "SELECT discountvalue FROM tbldiscount WHERE productid = '$productId'";
// $resultDiscount = $conn->query($sqlDiscount);

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
.main-content {
    display: flex;
    height: 100vh;
}


.product-section {
    flex: 1;
    /* padding: 20px; */
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

.cart-section {
    width: 470px;
    padding: 20px;
    /* background-color: #f8f9fa; */
    position: sticky;
    top: 0;
    height: 100vh;
}



.category-btn-group {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.category-btn-group button {
    border: 1px solid #ced4da;
    background-color: #ffffff;
    padding: 10px;
    width: 150px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.category-btn-group button.active {
    border-color: #007bff;
    background-color: #e7f3ff;
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
                    <div class="container-xxl flex-grow-1 container-p-y main-content">
                        <!-- Product Section -->
                        <div class="product-section">
                            <input type="text" id="productSearch" class="form-control mb-3 search"
                                placeholder="Scan barcode or type product name" autocomplete="off">
                            <div class="category-btn-group mb-4">
                                <?php foreach ($categories as $category): ?>
                                <button type="button" class="btn category-btn" data-category="<?= $category['name'] ?>">
                                    <span><?= $category['name'] ?></span>
                                    <span class="badge bg-secondary"><?= $category['count'] ?></span>
                                </button>
                                <?php endforeach; ?>
                            </div>
                            <div class="row" id="productList">
                                <!-- <?php foreach ($allProducts as $product): ?>
                                <div class="col-md-3 mb-3 product-card" data-barcode="<?= $product['barcode'] ?>">
                                    <div class="card">
                                        <div class="card-top">
                                            <img src="../../public/img/<?= $product['productimage'] ?>"
                                                class="img-cover" alt="Product Image">
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title"><?= $product['productname'] ?></h5>
                                            <p class="card-text">Price: <?= $product['priceout'] ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?> -->
                            </div>

                        </div>

                        <!-- Cart Section -->
                        <div class="cart-section">
                            <h4>Cart</h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <span class="mb-1 px-1">Products</span>
                                    <span class="mb-1 px-1">Price</span>
                                    <span class="mb-1 px-1">Discoutn</span>
                                    <span class="mb-1 px-1">quantity</span>
                                    <span class="mb-1 px-1">quantity</span>
                                    <span class="mb-1 px-1">Total</span>
                                    <hr>
                                </div>
                            </div>
                            <div id="cart" class="mb-3"></div>
                            <div class="mb-3">
                                <label for="addTotalAmount" class="form-label">Total Amount</label>
                                <input type="number" class="form-control" id="addTotalAmount" name="totalamount"
                                    step="0.01" readonly>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-success" id="checkoutButton">Checkout</button>
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
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <script src="../../public/js/test.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

        <script src="../../public/vendor/js/menu.js"></script>


        <!-- Main JS -->
        <script src="../../public/js/main.js"></script>
        <!-- GitHub Buttons JS -->
        <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
<!-- <script></script> -->

</html>