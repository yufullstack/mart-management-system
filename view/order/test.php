<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Search</title>
    <style>
    .autocomplete {
        position: relative;
        display: inline-block;
    }

    .autocomplete-items {
        position: absolute;
        border: 1px solid #d4d4d4;
        border-bottom: none;
        border-top: none;
        z-index: 99;
        top: 100%;
        left: 0;
        right: 0;
        max-height: 200px;
        overflow-y: auto;
    }

    .autocomplete-items li {
        padding: 10px;
        cursor: pointer;
        background-color: #fff;
        border-bottom: 1px solid #d4d4d4;
    }

    .autocomplete-items li:hover {
        background-color: #e9e9e9;
    }
    </style>
</head>

<body>
    <div class="autocomplete">
        <input id="productSearch" type="text" placeholder="Search for products...">
        <ul id="productList" class="autocomplete-items"></ul>
    </div>
    <script>
    document.getElementById('productSearch').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const productList = document.getElementById('productList');
        productList.innerHTML = '';

        if (query) {
            fetch(`getProducts.php?q=${query}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(products => {
                    products.forEach(product => {
                        const li = document.createElement('li');
                        li.textContent = product
                        .productname; // Make sure this matches the column name in your database
                        li.addEventListener('click', () => {
                            alert(`Selected product: ${product.productname}`);
                            productList.innerHTML = '';
                            document.getElementById('productSearch').value = product
                                .productname;
                        });
                        productList.appendChild(li);
                    });
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        }
    });
    </script>
</body>

</html>