!$(document).ready(function() {
var table = $('#example').DataTable({
"ajax": "fetch_data.php",
"columns": [
{ "data": "orderid" },
{ "data": "orderdate" },
{ "data": "employeename" },
{ "data": "customername" },
{ "data": "discount" },
{ "data": "totalamount" },
{ "data": "statusname" },
{
"data": null,
"className": "center",
"defaultContent": '<div class="dropdown">' +
    '<button class="btn btn-primary dropdown-toggle actionIcon" type="button" id="actionDropdown"
        data-bs-toggle="dropdown" aria-expanded="false">' +
        'Action' +
        '</button>' +
    '<ul class="dropdown-menu" aria-labelledby="actionDropdown">' +
        '<li><button type="button" class="dropdown-item editBtn text-warning"><i class="fas fa-edit"></i> Edit</button>
        </li>' +
        '<li><button type="button" class="dropdown-item viewBtn text-secondary"><i class="fas fa-eye"></i> View</button>
        </li>' +
        '<li><button type="button" class="dropdown-item deleteBtn text-danger"><i class="fas fa-trash-alt"></i>
                Delete</button></li>' +
        '</ul>' +
    '</div>'
}
],
"paging": true,
"lengthChange": true,
"searching": true,
"ordering": true,
"info": true,
"autoWidth": false,
"pageLength": 10,
"lengthMenu": [
[10, 25, 50, -1],
[10, 25, 50, "All"]
],
"language": {
"search": "Filter rows:",
"lengthMenu": "Number of rows: _MENU_",
},
"columnDefs": [{
"targets": -1,
"orderable": false
}]
});

async function loadOptions() {
try {
const response = await $.ajax({
url: 'fetch_options.php',
type: 'GET',
dataType: 'json'
});
return response;
} catch (error) {
console.error("Error loading options:", error);
}
}

async function populateOptions() {
const options = await loadOptions();
if (options) {
$.each(options.employees, function(index, value) {
$('#addEmployee, #editEmployee').append('<option value="' + value.employeeid + '">' + value.employeename + '</option>');
});
$.each(options.customers, function(index, value) {
$('#addCustomer, #editCustomer').append('<option value="' + value.customerid + '">' + value.customername + '</option>');
});
$.each(options.statuses, function(index, value) {
$('#addStatus, #editStatus').append('<option value="' + value.statusid + '">' + value.statusname + '</option>');
});

var products = options.products.map(function(product) {
return {
label: product.productname,
value: product.productid
};
});

$("#addProduct, #editProduct").autocomplete({
source: products,
select: function(event, ui) {
$(this).val(ui.item.label);
$(this).data('product-id', ui.item.value);
return false;
},
focus: function(event, ui) {
$(this).val(ui.item.label);
return false;
}
});
}
}

populateOptions();

$('#example tbody').on('click', 'button.editBtn', async function() {
var data = table.row($(this).parents('tr')).data();
var orderId = data.orderid;

try {
const orderData = await $.ajax({
url: 'get_order_details.php',
type: 'GET',
data: { orderid: orderId },
dataType: 'json'
});

$('#editOrderId').val(orderData.orderid);
$('#editOrderDate').val(orderData.orderdate);
$('#editDiscount').val(orderData.discount);
$('#editTotalAmount').val(orderData.totalamount);

const options = await loadOptions();

$('#editEmployee').empty();
$('#editCustomer').empty();
$('#editStatus').empty();
$('#editProduct').empty();

$.each(options.employees, function(index, value) {
$('#editEmployee').append('<option value="' + value.employeeid + '">' + value.employeename + '</option>');
});

$.each(options.customers, function(index, value) {
$('#editCustomer').append('<option value="' + value.customerid + '">' + value.customername + '</option>');
});

$.each(options.statuses, function(index, value) {
$('#editStatus').append('<option value="' + value.statusid + '">' + value.statusname + '</option>');
});

var products = options.products.map(function(product) {
return {
label: product.productname,
value: product.productid
};
});

$("#editProduct").autocomplete({
source: products,
select: function(event, ui) {
$(this).val(ui.item.label);
$(this).data('product-id', ui.item.value);
return false;
},
focus: function(event, ui) {
$(this).val(ui.item.label);
return false;
}
});

$('#editEmployee').val(orderData.employeeid);
$('#editCustomer').val(orderData.customerid);
$('#editStatus').val(orderData.statusid);
$('#editProduct').val(orderData.productid);

$('#editModal').modal('show');
} catch (error) {
console.error("Error fetching order details:", error);
}
});

// Add new record
$('#addForm').on('submit', function(e) {
e.preventDefault();
var formData = new FormData(this);
$.ajax({
url: 'add_record.php',
type: 'POST',
data: formData,
processData: false,
contentType: false,
success: function(data) {
$('#addModal').modal('hide');
table.ajax.reload();
},
error: function(xhr, status, error) {
console.error("Error adding record:", status, error);
}
});
});




$('#editForm').on('submit', async function(e) {
e.preventDefault();
var formData = new FormData(this);
var productID = $('#editProduct').data('product-id');
formData.append('productid', productID);
try {
await $.ajax({
url: 'edit_order.php',
type: 'POST',
data: formData,
processData: false,
contentType: false
});
$('#editModal').modal('hide');
table.ajax.reload();
} catch (error) {
console.error("Error editing record:", error);
}
});

$('#example tbody').on('click', 'button.deleteBtn', function() {
var data = table.row($(this).parents('tr')).data();
if (confirm("Are you sure you want to delete this record?")) {
$.ajax({
url: 'delete_order.php',
type: 'POST',
data: { orderid: data.orderid },
success: function() {
table.ajax.reload();
},
error: function(xhr, status, error) {
console.error("Error deleting record:", error);
}
});
}
});

$('#example tbody').on('click', 'button.viewBtn', function() {
var data = table.row($(this).parents('tr')).data();

$('#viewOrderId').val(data.orderid);
$('#viewOrderDate').val(data.orderdate);
$('#viewEmployee').val(data.employeename);
$('#viewCustomer').val(data.customername);
$('#viewDiscount').val(data.discount);
$('#viewTotalAmount').val(data.totalamount);
$('#viewStatus').val(data.statusname);

var orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
orderModal.show();
});

function fetchProductInfo(productId) {
if (!productId) return;

fetch(`fetch_product_info.php?productId=${productId}`)
.then(response => response.json())
.then(data => {
if (data.error) {
console.error(data.error);
} else {
const existingProduct = document.querySelector(`input[value="${productId}"]`);
if (existingProduct) {
const quantityInput = existingProduct.closest('.product-item').querySelector('input[name="quantity[]"]');
quantityInput.value = parseInt(quantityInput.value) + 1;
updateSubtotal(quantityInput);
} else {
addProductRow(productId, data.productname, data.price, data.discount);
}
}
})
.catch(error => console.error('Error fetching product info:', error))
.finally(() => {
const productSearchInput = document.getElementById('productSearch');
productSearchInput.value = '';
productSearchInput.focus();
});
}

function addProductRow(productId,productName, unitPrice, discount) {
const productsContainer = document.getElementById('productsContainer');
const productRow = document.createElement('div');
productRow.classList.add('product-item', 'row', 'mb-3');

const quantity = 1;
const subtotal = (quantity * unitPrice * (1 - discount / 100)).toFixed(2);

productRow.innerHTML = `
<div class="col-md-3">
    <div class="mb-3">
        <label class="form-label">Product ID</label>
        <input type="text" class="form-control" value="${productName}" readonly>
        <input type="hidden" name="productid[]" value="${productId}">
    </div>
</div>
<div class="col-md-2">
    <div class="mb-3">
        <label for="quantity" class="form-label">Quantity</label>
        <input type="number" class="form-control" name="quantity[]" value="${quantity}" min="1"
            oninput="updateSubtotal(this)">
    </div>
</div>
<div class="col-md-2">
    <div class="mb-3">
        <label class="form-label">Unit Price</label>
        <input type="number" class="form-control" name="unitprice[]" value="${unitPrice}" readonly>
    </div>
</div>
<div class="col-md-2">
    <div class="mb-3">
        <label class="form-label">Discount (%)</label>
        <input type="number" class="form-control" name="productdiscount[]" value="${discount}" readonly>
    </div>
</div>
<div class="col-md-2">
    <div class="mb-3">
        <label class="form-label">Subtotal</label>
        <input type="number" class="form-control" name="subtotal[]" value="${subtotal}" readonly>
    </div>
</div>
<div class="col-md-1">
    <div class="mb-3 d-flex align-items-end">
        <button type="button" class="btn btn-danger" onclick="removeProductRow(this)">Remove</button>
    </div>
</div>
`;

productsContainer.appendChild(productRow);
updateTotalAmount();
}

function updateSubtotal(quantityInput) {
const productItem = quantityInput.closest('.product-item');
const unitPrice = parseFloat(productItem.querySelector('input[name="unitprice[]"]').value);
const discount = parseFloat(productItem.querySelector('input[name="productdiscount[]"]').value);
const quantity = parseInt(quantityInput.value);

const subtotal = (quantity * unitPrice * (1 - discount / 100)).toFixed(2);
productItem.querySelector('input[name="subtotal[]"]').value = subtotal;
updateTotalAmount();
}

function updateTotalAmount() {
const subtotals = document.querySelectorAll('input[name="subtotal[]"]');
let totalAmount = 0;
subtotals.forEach(subtotal => {
totalAmount += parseFloat(subtotal.value);
});
document.getElementById('addTotalAmount').value = totalAmount.toFixed(2);
}

function removeProductRow(button) {
const productRow = button.closest('.product-item');
productRow.remove();
updateTotalAmount();
}

function fetchProductByIdOrName(query) {
if (!query) return;

if (isNaN(query)) {
fetch(`getProducts.php?q=${query}`)
.then(response => response.json())
.then(products => {
const productList = document.getElementById('productList');
productList.innerHTML = '';

const uniqueProductIds = new Set();
products.forEach(product => {
if (!uniqueProductIds.has(product.productid)) {
const li = document.createElement('li');
li.textContent = product.productname;
li.dataset.productId = product.productid;
li.tabIndex = 0;
li.addEventListener('click', () => {
document.getElementById('productSearch').value = product.productname;
productList.innerHTML = '';
fetchProductInfo(product.productid);
});
li.addEventListener('keypress', (e) => {
if (e.key === 'Enter') {
document.getElementById('productSearch').value = product.productname;
productList.innerHTML = '';
fetchProductInfo(product.productid);
}
});
productList.appendChild(li);
uniqueProductIds.add(product.productid);
}
});

if (query.length > 10 && products.length > 0) {
const firstProduct = productList.querySelector('li');
if (firstProduct) {
document.getElementById('productSearch').value = firstProduct.textContent;
productList.innerHTML = '';
fetchProductInfo(firstProduct.dataset.productId);
}
}
})
.catch(error => {
console.error('There was a problem with the fetch operation:', error);
});
} else {
fetchProductInfo(query);
}
}

document.getElementById('productSearch').addEventListener('input', function() {
const query = this.value.toLowerCase();
fetchProductByIdOrName(query);
});

document.getElementById('productSearch').addEventListener('keypress', function(e) {
if (e.key === 'Enter') {
e.preventDefault();
const productList = document.getElementById('productList');
const firstProduct = productList.querySelector('li');
if (firstProduct) {
document.getElementById('productSearch').value = firstProduct.textContent;
productList.innerHTML = '';
fetchProductInfo(firstProduct.dataset.productId);
}
}
});

document.getElementById('addForm').addEventListener('keypress', function(e) {
if (e.key === 'Enter') {
e.preventDefault();
const productList = document.getElementById('productList');
const firstProduct = productList.querySelector('li');
if (firstProduct) {
document.getElementById('productSearch').value = firstProduct.textContent;
productList.innerHTML = '';
fetchProductInfo(firstProduct.dataset.productId);
}
}
});
});