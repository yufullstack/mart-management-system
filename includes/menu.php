<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="view/admin/index.php" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bold ms-2">Pain Mart</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <?php
        $menuItems = [
            ['href' => '/view/index.php', 'icon' => 'bx bx-home-circle', 'text' => 'Dashboard'],
            ['href' => '/view/order/index.php', 'icon' => 'fas fa-shopping-cart', 'text' => 'Orders'],
            ['href' => '/view/employee/index.php', 'icon' => 'fas fa-users', 'text' => 'Employees'],
            ['href' => '/view/customer/index.php', 'icon' => 'fas fa-user', 'text' => 'Customer'],
            ['href' => '/view/product/index.php', 'icon' => 'fas fa-box', 'text' => 'Product'],
            ['href' => '/view/category/index.php', 'icon' => 'fas fa-tags', 'text' => 'Category'],
            ['href' => '/view/supplier/index.php', 'icon' => 'fas fa-truck', 'text' => 'Supplier'],
            ['href' => '/view/inventorylog/index.php', 'icon' => 'fas fa-boxes', 'text' => 'Inventory'],
            ['href' => 'role.php', 'icon' => 'fas fa-user-lock', 'text' => 'Role'],
        ];

        foreach ($menuItems as $item) {
            $isActive = strpos($_SERVER['REQUEST_URI'], $item['href']) !== false ? 'active' : '';
            echo "<li class='menu-item $isActive'>
                    <a href='{$item['href']}' class='menu-link'>
                        <i class='menu-icon {$item['icon']}'></i>
                        <div data-i18n='Basic'>{$item['text']}</div>
                    </a>
                  </li>";
        }
        ?>
    </ul>
</aside>