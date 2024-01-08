<?php
if (file_exists("conf/conf.php")) 
    @include 'conf/conf.php';

$conn = new mysqli($conf->db->host, $conf->db->user, $conf->db->pass, $conf->db->name);

if(isset($_POST['customer_id'])) { 
    $customerId = $_POST['customer_id'];
    $paymentMethod = $_POST['payment_method'];
    $shippingAddressId = $_POST['shipping_address_id'];
    $productPrice = $_POST['product_price'];
    $shippingPrice = $_POST['shipping_price'];
    $totalPrice = $_POST['total_price'];
    $sellers = $_POST['sellers'];

    $date = date("Y-m-d");

    $query_customer_data = "SELECT * FROM mgkf_customer_grid_flat ";
    $query_customer_data .= "WHERE entity_id = '".$customerId."'";
    $result_customer_data = $conn->query($query_customer_data);
    $row_customer_data = $result_customer_data->fetch_assoc();
    $customerEmail = $row_customer_data['email'];
    $customerName = $row_customer_data['name'];
    $customerName = explode(" ", $customerName);
    $customerFirstname = $customerName[0];
    $customerLastname = $customerName[1];
    $customerName = $customerFirstname . ' ' . $customerLastname;

    $query_customer_address = "SELECT * FROM mgkf_customer_address_entity ";
    $query_customer_address .= "WHERE parent_id = '".$customerId."' ";
    if($shippingAddressId == 0) {
        $query_customer_address .= "ORDER BY entity_id ASC";
    }
    if($shippingAddressId == 1) {
        $query_customer_address .= "ORDER BY entity_id DESC";
    }
    $result_customer_address = $conn->query($query_customer_address);
    $row_customer_address = $result_customer_address->fetch_assoc();
    $addressFirstname = $row_customer_address['firstname'];
    $addressLastname = $row_customer_address['lastname'];
    $addressStreet = $row_customer_address['street'];
    $addressCity = $row_customer_address['city'];
    $addressPostcode = $row_customer_address['postcode'];
    $addressCountry = $row_customer_address['country_id'];
    $addressPhone = $row_customer_address['telephone'];
    $addressFullname = $addressFirstname . ' ' . $addressLastname;
    $addressDb = $addressStreet . ',' . $addressCity . ',' . $addressPostcode;

    $query_check_last_order_id = "SELECT increment_id ";
    $query_check_last_order_id .= "FROM mgkf_sales_order ";
    $query_check_last_order_id .= "ORDER BY entity_id DESC LIMIT 1";
    $result_check_last_order_id = $conn->query($query_check_last_order_id);
    $row_check_last_order_id = $result_check_last_order_id->fetch_assoc();
    $lastOrderId = $row_check_last_order_id['increment_id'];
    
    $query_cart_info = "SELECT mgkf_quote_item.quote_id, mgkf_catalog_product_entity_int.value as seller_id, mgkf_quote_item.item_id, mgkf_quote_item.store_id, mgkf_quote_item.product_id, mgkf_quote_item.name as product_name, mgkf_quote_item.qty, mgkf_quote_item.row_total as total, mgkf_quote.quote_currency_code as currency, mgkf_quote.grand_total, mgkf_quote_item.row_total, mgkf_quote_item.sku, mgkf_quote_item.parent_item_id ";
    $query_cart_info .= "FROM mgkf_quote_item, mgkf_quote, mgkf_catalog_product_entity_int ";
    $query_cart_info .= "WHERE mgkf_quote_item.quote_id = mgkf_quote.entity_id ";
    $query_cart_info .= "AND mgkf_catalog_product_entity_int.entity_id = mgkf_quote_item.product_id ";
    $query_cart_info .= "AND mgkf_quote_item.row_total != '' ";
    $query_cart_info .= "AND mgkf_quote.is_active = 1 ";
    $query_cart_info .= "AND mgkf_catalog_product_entity_int.attribute_id = 158 ";
    $query_cart_info .= "AND mgkf_quote.customer_id = '".$customerId."' ";
    $query_cart_info .= "ORDER BY value ASC";
    $result_cart_info = $conn->query($query_cart_info);
    $i = 0;
    while($row_cart_info = $result_cart_info->fetch_assoc()) {
        $quoteId = $row_cart_info['quote_id'];
        $sellerId = $row_cart_info['seller_id'];
        $itemId = $row_cart_info['item_id'];
        $storeId = $row_cart_info['store_id'];
        $productId = $row_cart_info['product_id'];
        $productName = $row_cart_info['product_name'];
        $productQty = $row_cart_info['qty'];
        $productTotal = $row_cart_info['total'];
        $productCurrency = $row_cart_info['currency'];
        $productTotal = $row_cart_info['row_total'];
        $grandTotal = $row_cart_info['grand_total'];
        $productSku = $row_cart_info['sku'];
        $parentItemId = $row_cart_info['parent_item_id'];

        $query_category = "SELECT mgkf_catalog_category_entity_varchar.entity_id, mgkf_catalog_category_entity_varchar.value ";
        $query_category .= "FROM mgkf_catalog_category_entity_varchar, mgkf_catalog_category_product ";
        $query_category .= "WHERE mgkf_catalog_category_entity_varchar.entity_id = mgkf_catalog_category_product.category_id ";
        $query_category .= "AND mgkf_catalog_category_entity_varchar.store_id = 0 ";
        $query_category .= "AND mgkf_catalog_category_entity_varchar.attribute_id = 45 ";
        $query_category .= "AND mgkf_catalog_category_product.product_id = '".$productId."' ";
        $query_category .= "LIMIT 0,1";
        $result_category = $conn->query($query_category);
        $row_category = $result_category->fetch_assoc();
        $categoryId = $row_category['entity_id'];
        $categoryName = $row_category['value'];

        $query_subcategory = "SELECT mgkf_catalog_category_entity_varchar.entity_id, mgkf_catalog_category_entity_varchar.value ";
        $query_subcategory .= "FROM mgkf_catalog_category_entity_varchar, mgkf_catalog_category_product ";
        $query_subcategory .= "WHERE mgkf_catalog_category_entity_varchar.entity_id = mgkf_catalog_category_product.category_id ";
        $query_subcategory .= "AND mgkf_catalog_category_entity_varchar.store_id = 0 ";
        $query_subcategory .= "AND mgkf_catalog_category_entity_varchar.attribute_id = 45 ";
        $query_subcategory .= "AND mgkf_catalog_category_product.product_id = '".$productId."' ";
        $query_subcategory .= "LIMIT 1,1";
        $result_subcategory = $conn->query($query_subcategory);
        $row_subcategory = $result_subcategory->fetch_assoc();
        $subcategoryId = $row_subcategory['entity_id'];
        $subcategoryName = $row_subcategory['value'];

        $query_category_discount = "SELECT category_id, subcategory_name, discount_type ";
        $query_category_discount .= "FROM mgkf_marketplace_discount_category ";
        $query_category_discount .= "WHERE category_id = '".$categoryId."' ";
        $query_category_discount .= "AND subcategory_name = '".$subcategoryName."' ";
        $query_category_discount .= "AND seller_id = '".$sellerId."' ";
        $query_category_discount .= "AND is_active = 1 ";
        $query_category_discount .= "AND date_from <= '".$date."' ";
        $query_category_discount .= "AND date_to >= '".$date."'";
        $result_category_discount = $conn->query($query_category_discount);
        $countCategoryDiscount = $result_category_discount->num_rows;
        if($countCategoryDiscount > 0) {
            $row_category_discount = $result_category_discount->fetch_assoc();
            $discountCategory = str_replace("%", "", $row_category_discount['discount_type']);
        }

        $query_product_discount = "SELECT * FROM mgkf_marketplace_discount_products ";
        $query_product_discount .= "WHERE is_active = 1 ";
        $query_product_discount .= "AND seller_id = '".$sellerId."' ";
        $query_product_discount .= "AND product_id = '".$productId."' ";
        $query_product_discount .= "AND date_from <= '".$date."' ";
        $query_product_discount .= "AND date_to >= '".$date."'";
        $result_product_discount = $conn->query($query_product_discount);
        $countProductDiscount = $result_product_discount->num_rows;
        if($countProductDiscount > 0) {
            $row_product_discount = $result_product_discount->fetch_assoc();
            $discount = str_replace("%", "", $row_product_discount['discount']);
            $discountProduct = $discount;
            $discountFrom = $row_product_discount['date_from'];
            $discountTo = $row_product_discount['date_to'];
        }

        if($countCategoryDiscount > 0) {
            $discount = $discountCategory;
            $newPrice = number_format(($productPrice - ($productPrice * ($discountCategory / 100))), 2, '.', '');
        }
        if($countProductDiscount > 0) {
            $discount = $discountProduct;
            $newPrice = number_format(($productPrice - ($productPrice * ($discountProduct / 100))), 2, '.', '');
        }
        if($countCategoryDiscount > 0 && $countProductDiscount > 0) {
            if($discountCategory >= $discountProduct) {
                $discount = $discountCategory;
                $newPrice = number_format(($productPrice - ($productPrice * ($discountCategory / 100))), 2, '.', '');
            }
            else {
                $discount = $discountProduct;
                $newPrice = number_format(($productPrice - ($productPrice * ($discountProduct / 100))), 2, '.', '');
            }
        }

        if($countProductDiscount > 0 || $countCategoryDiscount > 0) {
            $productTotal = $newPrice;
        }
        else {
            $productTotal = $productTotal;
        }
        
        $state = 'new';
        $status = 'pending';
        $storeName = 'Main Website Atytude.ro English';
        $incrementId = $lastOrderId + $i +1;

        $query_parent_id = "SELECT parent_id FROM mgkf_catalog_product_relation ";
        $query_parent_id .= "WHERE child_id = '".$itemId."'";
        $result_parent_id = $conn->query($query_parent_id);
        $row_parent_id = $result_parent_id->fetch_assoc();
        $parentProductId = $row_parent_id['parent_id'];

        $query_product_name = "SELECT sku FROM mgkf_catalog_product_entity ";
        $query_product_name .= "WHERE type_id = 'configurable' ";
        $query_product_name .= "AND entity_id = '".$parentProductId."'";
        $result_product_name = $conn->query($query_product_name);
        $row_product_name = $result_product_name->fetch_assoc();
        $productBaseName = $row_product_name['sku'];

        // mgkf_sales_order
        $query = "INSERT INTO mgkf_sales_order (state, status, shipping_description, is_virtual, store_id, customer_id, total_qty_ordered, quote_id, increment_id, customer_email, customer_firstname, customer_lastname, order_currency_code, total_item_count, subtotal, grand_total, shipping_amount) ";
        $query .= "VALUES ('$state', '$status', 'test123', '0', '1', '$customerId', '$productQty', '$quoteId', '$incrementId', '$customerEmail', '$customerFirstname', '$customerLastname', 'RON', '$productQty', '$productTotal', '$productTotal', '$shippingPrice')";
        $result = $conn->query($query);
        $last_id = $conn->insert_id;

        // mgkf_sales_order_grid
        $query = "INSERT INTO mgkf_sales_order_grid (entity_id, status, store_id, store_name, customer_id, grand_total, increment_id, shipping_name, billing_name, billing_address, shipping_address, shipping_information, customer_email, customer_group, shipping_and_handling, customer_name, payment_method) ";
        $query .= "VALUES ('$last_id', '$status', '1', '$storeName', '$customerId', '$totalPrice', '$incrementId', '$addressFullname', '$addressFullname', '$addressDb', '$addressDb', 'test', '$customerEmail', '1', '$shippingPrice', '$customerName', '$paymentMethod')";
        $result = $conn->query($query);

        // mgkf_marketplace_sellerorder
        $query = "INSERT INTO mgkf_marketplace_sellerorder (order_id, active, seller_id, seller_product_total, status, increment_id, shipping_amount, order_currency_code, customer_id) ";
        $query .= "VALUES ('$last_id', '1', '$sellerId', '$totalPrice', '$status', '$incrementId', '$shippingPrice', 'RON', '$customerId')";
        $result = $conn->query($query);

        // mgkf_sales_order_item
        $query = "INSERT INTO mgkf_sales_order_item (order_id, quote_item_id, store_id, product_id, product_type, sku, name, qty_ordered, price, original_price, row_total, row_total_incl_tax) ";
        $query .= "VALUES ('$last_id', '$quoteId', '1', '$itemId', 'simple', '$productSku', '$productName', '$productQty', '$productTotal', '$productTotal', '$totalPrice', '$totalPrice')";
        $result = $conn->query($query);
        $lastOrderItemId = $conn->insert_id;
        
        // mgkf_marketplace_sellerorderitems
        $query = "INSERT INTO mgkf_marketplace_sellerorderitems (order_id, seller_id, order_item_id, product_id, product_sku, product_qty, product_name, status, product_price) ";
        $query .= "VALUES ('$last_id', '$sellerId', '$lastOrderItemId', '$itemId', '$productSku', '$productQty', '$productName', '$status', '$productTotal')";
        $result = $conn->query($query);
        
        $query = "UPDATE mgkf_quote ";
        $query .= "SET is_active = 0 ";
        $query .= "WHERE entity_id = '".$quoteId."'";
        $result = $conn->query($query);

        $i++;
    }
}
if(isset($_POST['guest'])) {    
    $remoteIp = $_POST['guest'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $country = $_POST['country'];
    $city = $_POST['city'];
    $zip = $_POST['zip'];
    $region = $_POST['region'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $paymentMethod = $_POST['payment_method'];
    $productPrice = $_POST['product_price'];
    $shippingPrice = $_POST['shipping_price'];
    $totalPrice = $_POST['total_price'];
    $sellers = $_POST['sellers'];

    $customerName = $firstname . ' ' . $lastname;

    $date = date("Y-m-d");

    $addressFullname = $firstname . ' ' . $lastname;
    $addressDb = $address . ',' . $city . ',' . $zip;

    $query_check_last_order_id = "SELECT increment_id ";
    $query_check_last_order_id .= "FROM mgkf_sales_order ";
    $query_check_last_order_id .= "ORDER BY entity_id DESC LIMIT 1";
    $result_check_last_order_id = $conn->query($query_check_last_order_id);
    $row_check_last_order_id = $result_check_last_order_id->fetch_assoc();
    $lastOrderId = $row_check_last_order_id['increment_id'];
    
    $query_cart_info = "SELECT mgkf_quote_item.quote_id, mgkf_catalog_product_entity_int.value as seller_id, mgkf_quote_item.item_id, mgkf_quote_item.store_id, mgkf_quote_item.product_id, mgkf_quote_item.name as product_name, mgkf_quote_item.qty, mgkf_quote_item.row_total as total, mgkf_quote.quote_currency_code as currency, mgkf_quote.grand_total, mgkf_quote_item.row_total, mgkf_quote_item.sku, mgkf_quote_item.parent_item_id ";
    $query_cart_info .= "FROM mgkf_quote_item, mgkf_quote, mgkf_catalog_product_entity_int ";
    $query_cart_info .= "WHERE mgkf_quote_item.quote_id = mgkf_quote.entity_id ";
    $query_cart_info .= "AND mgkf_catalog_product_entity_int.entity_id = mgkf_quote_item.product_id ";
    $query_cart_info .= "AND mgkf_quote_item.row_total != '' ";
    $query_cart_info .= "AND mgkf_quote.is_active = 1 ";
    $query_cart_info .= "AND mgkf_catalog_product_entity_int.attribute_id = 158 ";
    $query_cart_info .= "AND mgkf_quote.customer_id IS NULL ";
    $query_cart_info .= "AND mgkf_quote.remote_ip = $remoteIp ";
    $query_cart_info .= "AND mgkf_quote.created_at > '2020-10-05' ";
    $query_cart_info .= "ORDER BY value ASC";
    $result_cart_info = $conn->query($query_cart_info);
    $i = 0;
    while($row_cart_info = $result_cart_info->fetch_assoc()) {
        $quoteId = $row_cart_info['quote_id'];
        $sellerId = $row_cart_info['seller_id'];
        $itemId = $row_cart_info['item_id'];
        $storeId = $row_cart_info['store_id'];
        $productId = $row_cart_info['product_id'];
        $productName = $row_cart_info['product_name'];
        $productQty = $row_cart_info['qty'];
        $productTotal = $row_cart_info['total'];
        $productCurrency = $row_cart_info['currency'];
        $productTotal = $row_cart_info['row_total'];
        $grandTotal = $row_cart_info['grand_total'];
        $productSku = $row_cart_info['sku'];
        $parentItemId = $row_cart_info['parent_item_id'];

        $query_category = "SELECT mgkf_catalog_category_entity_varchar.entity_id, mgkf_catalog_category_entity_varchar.value ";
        $query_category .= "FROM mgkf_catalog_category_entity_varchar, mgkf_catalog_category_product ";
        $query_category .= "WHERE mgkf_catalog_category_entity_varchar.entity_id = mgkf_catalog_category_product.category_id ";
        $query_category .= "AND mgkf_catalog_category_entity_varchar.store_id = 0 ";
        $query_category .= "AND mgkf_catalog_category_entity_varchar.attribute_id = 45 ";
        $query_category .= "AND mgkf_catalog_category_product.product_id = '".$productId."' ";
        $query_category .= "LIMIT 0,1";
        $result_category = $conn->query($query_category);
        $row_category = $result_category->fetch_assoc();
        $categoryId = $row_category['entity_id'];
        $categoryName = $row_category['value'];

        $query_subcategory = "SELECT mgkf_catalog_category_entity_varchar.entity_id, mgkf_catalog_category_entity_varchar.value ";
        $query_subcategory .= "FROM mgkf_catalog_category_entity_varchar, mgkf_catalog_category_product ";
        $query_subcategory .= "WHERE mgkf_catalog_category_entity_varchar.entity_id = mgkf_catalog_category_product.category_id ";
        $query_subcategory .= "AND mgkf_catalog_category_entity_varchar.store_id = 0 ";
        $query_subcategory .= "AND mgkf_catalog_category_entity_varchar.attribute_id = 45 ";
        $query_subcategory .= "AND mgkf_catalog_category_product.product_id = '".$productId."' ";
        $query_subcategory .= "LIMIT 1,1";
        $result_subcategory = $conn->query($query_subcategory);
        $row_subcategory = $result_subcategory->fetch_assoc();
        $subcategoryId = $row_subcategory['entity_id'];
        $subcategoryName = $row_subcategory['value'];

        $query_category_discount = "SELECT category_id, subcategory_name, discount_type ";
        $query_category_discount .= "FROM mgkf_marketplace_discount_category ";
        $query_category_discount .= "WHERE category_id = '".$categoryId."' ";
        $query_category_discount .= "AND subcategory_name = '".$subcategoryName."' ";
        $query_category_discount .= "AND seller_id = '".$sellerId."' ";
        $query_category_discount .= "AND is_active = 1 ";
        $query_category_discount .= "AND date_from <= '".$date."' ";
        $query_category_discount .= "AND date_to >= '".$date."'";
        $result_category_discount = $conn->query($query_category_discount);
        $countCategoryDiscount = $result_category_discount->num_rows;
        if($countCategoryDiscount > 0) {
            $row_category_discount = $result_category_discount->fetch_assoc();
            $discountCategory = str_replace("%", "", $row_category_discount['discount_type']);
        }

        $query_product_discount = "SELECT * FROM mgkf_marketplace_discount_products ";
        $query_product_discount .= "WHERE is_active = 1 ";
        $query_product_discount .= "AND seller_id = '".$sellerId."' ";
        $query_product_discount .= "AND product_id = '".$productId."' ";
        $query_product_discount .= "AND date_from <= '".$date."' ";
        $query_product_discount .= "AND date_to >= '".$date."'";
        $result_product_discount = $conn->query($query_product_discount);
        $countProductDiscount = $result_product_discount->num_rows;
        if($countProductDiscount > 0) {
            $row_product_discount = $result_product_discount->fetch_assoc();
            $discount = str_replace("%", "", $row_product_discount['discount']);
            $discountProduct = $discount;
            $discountFrom = $row_product_discount['date_from'];
            $discountTo = $row_product_discount['date_to'];
        }

        if($countCategoryDiscount > 0) {
            $discount = $discountCategory;
            $newPrice = number_format(($productPrice - ($productPrice * ($discountCategory / 100))), 2, '.', '');
        }
        if($countProductDiscount > 0) {
            $discount = $discountProduct;
            $newPrice = number_format(($productPrice - ($productPrice * ($discountProduct / 100))), 2, '.', '');
        }
        if($countCategoryDiscount > 0 && $countProductDiscount > 0) {
            if($discountCategory >= $discountProduct) {
                $discount = $discountCategory;
                $newPrice = number_format(($productPrice - ($productPrice * ($discountCategory / 100))), 2, '.', '');
            }
            else {
                $discount = $discountProduct;
                $newPrice = number_format(($productPrice - ($productPrice * ($discountProduct / 100))), 2, '.', '');
            }
        }

        if($countProductDiscount > 0 || $countCategoryDiscount > 0) {
            $productTotal = $newPrice;
        }
        else {
            $productTotal = $productTotal;
        }
        
        $state = 'new';
        $status = 'pending';
        $storeName = 'Main Website Atytude.ro English';
        $incrementId = $lastOrderId + $i +1;

        $query_parent_id = "SELECT parent_id FROM mgkf_catalog_product_relation ";
        $query_parent_id .= "WHERE child_id = '".$itemId."'";
        $result_parent_id = $conn->query($query_parent_id);
        $row_parent_id = $result_parent_id->fetch_assoc();
        $parentProductId = $row_parent_id['parent_id'];

        $query_product_name = "SELECT sku FROM mgkf_catalog_product_entity ";
        $query_product_name .= "WHERE type_id = 'configurable' ";
        $query_product_name .= "AND entity_id = '".$parentProductId."'";
        $result_product_name = $conn->query($query_product_name);
        $row_product_name = $result_product_name->fetch_assoc();
        $productBaseName = $row_product_name['sku'];

        // mgkf_sales_order
        $query = "INSERT INTO mgkf_sales_order (state, status, shipping_description, is_virtual, store_id, total_qty_ordered, quote_id, increment_id, customer_email, customer_firstname, customer_lastname, order_currency_code, total_item_count, subtotal, grand_total, shipping_amount) ";
        $query .= "VALUES ('$state', '$status', 'test123', '0', '1', '$productQty', '$quoteId', '$incrementId', '$email', '$firstname', '$lastname', 'RON', '$productQty', '$totalPrice', '$totalPrice', '$shippingPrice')";
        $result = $conn->query($query);
        $last_id = $conn->insert_id;

        // mgkf_sales_order_grid
        $query = "INSERT INTO mgkf_sales_order_grid (entity_id, status, store_id, store_name, grand_total, increment_id, shipping_name, billing_name, billing_address, shipping_address, shipping_information, customer_email, customer_group, shipping_and_handling, customer_name, payment_method) ";
        $query .= "VALUES ('$last_id', '$status', '1', '$storeName', '$totalPrice', '$incrementId', '$addressFullname', '$addressFullname', '$addressDb', '$addressDb', 'test', '$email', '1', '$shippingPrice', '$customerName', '$paymentMethod')";
        $result = $conn->query($query);

        // mgkf_marketplace_sellerorder
        $query = "INSERT INTO mgkf_marketplace_sellerorder (order_id, active, seller_id, seller_product_total, status, increment_id, shipping_amount, order_currency_code) ";
        $query .= "VALUES ('$last_id', '1', '$sellerId', '$totalPrice', '$status', '$incrementId', '$shippingPrice', 'RON')";
        $result = $conn->query($query);

        // mgkf_sales_order_item
        $query = "INSERT INTO mgkf_sales_order_item (order_id, quote_item_id, store_id, product_id, product_type, sku, name, qty_ordered, price, original_price, row_total, row_total_incl_tax) ";
        $query .= "VALUES ('$last_id', '$quoteId', '1', '$itemId', 'simple', '$productSku', '$productName', '$productQty', '$productTotal', '$productTotal', '$totalPrice', '$totalPrice')";
        $result = $conn->query($query);
        $lastOrderItemId = $conn->insert_id;
        
        // mgkf_marketplace_sellerorderitems
        $query = "INSERT INTO mgkf_marketplace_sellerorderitems (order_id, seller_id, order_item_id, product_id, product_sku, product_qty, product_name, status, product_price) ";
        $query .= "VALUES ('$last_id', '$sellerId', '$lastOrderItemId', '$itemId', '$productSku', '$productQty', '$productName', '$status', '$productTotal')";
        $result = $conn->query($query);
        
        $query = "UPDATE mgkf_quote ";
        $query .= "SET is_active = 0 ";
        $query .= "WHERE entity_id = '".$quoteId."'";
        $result = $conn->query($query);

        $i++;
    }
}
?>