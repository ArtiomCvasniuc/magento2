<?php
if (file_exists("conf/conf.php")) 
    @include 'conf/conf.php';

$conn = new mysqli($conf->db->host, $conf->db->user, $conf->db->pass, $conf->db->name);

if(isset($_POST['reason'])) {
    $product_id = $_POST['product_id'];
    $order_id = $_POST['order_id'];
    $seller_id = $_POST['seller_id'];
    $return_action = $_POST['choose_action'];
    $reason = $_POST['reason'];

    $query_order_full_id = "SELECT increment_id ";
    $query_order_full_id .= "FROM mgkf_marketplace_sellerorder ";
    $query_order_full_id .= "WHERE order_id = '".$order_id."'";
    $result_order_full_id = $conn->query($query_order_full_id);
    $row_order_full_id = $result_order_full_id->fetch_row();
    $order_full_id = $row_order_full_id[0];
    
    $query_1 = "INSERT INTO mgkf_marketplace_return_order ";
    $query_1 .= "(seller_id, order_id, order_increment_id, reason, return_action) ";
    $query_1 .= "VALUES ('".$seller_id."', '".$order_id."', '".$order_full_id."', '".$reason."','".$return_action."')";
    $result_1 = $conn->query($query_1);
    
    $query_2 = "UPDATE mgkf_marketplace_sellerorder ";
    $query_2 .= "SET is_returned = 1, ";
    $query_2 .= "cancelling_reason = '".$reason."', ";
    $query_2 .= "status = 'returned' ";
    $query_2 .= "WHERE order_id = '$order_id'";
    $result_2 = $conn->query($query_2);

    $query_3 = "UPDATE mgkf_sales_order ";
    $query_3 .= "SET status = 'returned', cancelling_reason = '".$reason."' ";
    $query_3 .= "WHERE entity_id = '".$order_id."'";
    $result_3 = $conn->query($query_3);

    $query_4 = "UPDATE mgkf_sales_order_grid ";
    $query_4 .= "SET status = 'returned' ";
    $query_4 .= "WHERE entity_id = '".$order_id."'";
    $result_4 = $conn->query($query_4);
    ?>
    <script>
    window.location.href = "/ro/sales/order/history";
    </script>
    <?php
}
if(isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];
    
    $query_check_last_order_id = "SELECT increment_id ";
    $query_check_last_order_id .= "FROM mgkf_sales_order ";
    $query_check_last_order_id .= "ORDER BY entity_id DESC LIMIT 1";
    $result_check_last_order_id = $conn->query($query_check_last_order_id);
    $row_check_last_order_id = $result_check_last_order_id->fetch_assoc();
    $incrementId = $row_check_last_order_id['increment_id'] + 1;

    $state = 'new';
    $storeName = 'Main Website Atytude.ro English';
    $date = date("d.m.y");

    $query = "SELECT mgkf_sales_order.status, mgkf_sales_order.shipping_description, mgkf_sales_order.total_qty_ordered, mgkf_sales_order.subtotal, mgkf_sales_order.quote_id, mgkf_sales_order.shipping_incl_tax, mgkf_sales_order_grid.customer_id, mgkf_sales_order_grid.grand_total, mgkf_sales_order_grid.order_currency_code, mgkf_sales_order_grid.shipping_name, mgkf_sales_order_grid.billing_name, mgkf_sales_order_grid.billing_address, mgkf_sales_order_grid.shipping_address, mgkf_sales_order_grid.shipping_information, mgkf_sales_order_grid.customer_name, mgkf_sales_order_grid.customer_email, mgkf_sales_order_grid.payment_method ";
    $query .= "FROM mgkf_sales_order, mgkf_sales_order_grid ";
    $query .= "WHERE mgkf_sales_order.entity_id = mgkf_sales_order_grid.entity_id ";
    $query .= "AND mgkf_sales_order.increment_id = '".$orderId."'";
    
    $result = $conn->query($query);
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $customerId = $row['customer_id'];
        $status = $row['status'];
        $quoteId = $row['quote_id'];
        $productQty = $row['total_qty_ordered'];
        $productTotal = $row['subtotal'];
        $grandTotal = $row['grand_total'];
        $currency = $row['order_currency_code'];
        $shippingDescription = $row['shipping_description'];
        $shippingTotal = $row['shipping_incl_tax'];
        $shippingName = $row['shipping_name'];
        $billingName = $row['billing_name'];
        $shippingAddress = $row['shipping_address'];
        $billingAddress = $row['billing_address'];
        $shippingInformation = $row['shipping_information'];
        $customerName = $row['customer_name'];
        $customerName = explode(" ", $customerName);
        $customerFirstname = $customerName[0];
        $customerLastname = $customerName[1];
        $customerEmail = $row['customer_email'];
        $paymentMethod = $row['payment_method'];

        $query = "SELECT mgkf_quote_item.quote_id, mgkf_catalog_product_entity_int.value as seller_id, mgkf_quote_item.item_id, mgkf_quote_item.store_id, mgkf_quote_item.product_id, mgkf_quote_item.name as product_name, mgkf_quote_item.qty, mgkf_quote_item.row_total as total, mgkf_quote.quote_currency_code as currency, mgkf_quote.grand_total, mgkf_quote_item.row_total, mgkf_quote_item.sku, mgkf_quote_item.parent_item_id ";
        $query .= "FROM mgkf_quote_item, mgkf_quote, mgkf_catalog_product_entity_int ";
        $query .= "WHERE mgkf_quote_item.quote_id = mgkf_quote.entity_id ";
        $query .= "AND mgkf_catalog_product_entity_int.entity_id = mgkf_quote_item.product_id ";
        $query .= "AND mgkf_quote_item.row_total != '' ";
        $query .= "AND mgkf_catalog_product_entity_int.attribute_id = 158 ";
        $query .= "AND mgkf_quote_item.quote_id = '".$quoteId."'";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $sellerId = $row['seller_id'];
        $productSku = $row['sku'];
        $productName = $row['product_name'];
        $productQty = $row['qty'];
        $productTotal = $row['row_total'];
        $itemId = $row['item_id'];

        $query = "INSERT INTO mgkf_sales_order (state, status, shipping_description, is_virtual, store_id, customer_id, total_qty_ordered, quote_id, increment_id, customer_email, customer_firstname, customer_lastname, order_currency_code, total_item_count, subtotal, grand_total) ";
        $query .= "VALUES ('$state', '$status', '$shippingDescription', '0', '1', '$customerId', '$productQty', '$quoteId', '$incrementId', '$customerEmail', '$customerFirstname', '$customerLastname', '$currency', '$productQty', '$grandTotal', '$grandTotal')";
        $result = $conn->query($query);
        $last_id = $conn->insert_id;
    
        $query = "INSERT INTO mgkf_sales_order_grid (entity_id, status, store_id, store_name, customer_id, grand_total, increment_id, shipping_name, billing_name, billing_address, shipping_address, shipping_information, customer_email, customer_group, shipping_and_handling, customer_name, payment_method) ";
        $query .= "VALUES ('$last_id', '$status', '1', '$storeName', '$customerId', '$productTotal', '$incrementId', '$shippingName', '$billingName', '$billingAddress', '$shippingAddress', '$shippingInformation', '$customerEmail', '1', '$shippingTotal', '$customerName', '$paymentMethod')";
        $result = $conn->query($query);

        $query = "INSERT INTO mgkf_marketplace_sellerorder (order_id, active, seller_id, seller_product_total, status, increment_id, shipping_amount, order_currency_code, customer_id) ";
        $query .= "VALUES ('$last_id', '1', '$sellerId', '$productTotal', '$status', '$incrementId', '$shippingTotal', '$currency', '$customerId')";
        $result = $conn->query($query);
        
        $query = "INSERT INTO mgkf_sales_order_item (order_id, quote_item_id, store_id, product_id, product_type, sku, name, qty_ordered, price, original_price, row_total, row_total_incl_tax) ";
        $query .= "VALUES ('$last_id', '$quoteId', '1', '$itemId', 'simple', '$productSku', '$productName', '$productQty', '$productTotal', '$productTotal', '$productTotal', '$productTotal')";
        $result = $conn->query($query);
        $lastOrderItemId = $conn->insert_id;

        $query = "INSERT INTO mgkf_marketplace_sellerorderitems (order_id, seller_id, order_item_id, product_id, product_sku, product_qty, product_name, status, product_price) ";
        $query .= "VALUES ('$last_id', '$sellerId', '$lastOrderItemId', '$itemId', '$productSku', '$productQty', '$productName', '$status', '$productTotal')";
        $result = $conn->query($query);
    }
    ?>
    <script>
    window.location.href = "/ro/sales/order/history";
    </script>
    <?php
}
?>