<?php 
if (file_exists("conf/conf.php")) 
    @include 'conf/conf.php';

$conn = new mysqli($conf->db->host, $conf->db->user, $conf->db->pass, $conf->db->name);

if(isset($_POST['customer_id'])) {
    $itemId = $_POST['item_id'];
    $productId = $_POST['product_id'];
    $childId = $_POST['child_id'];
    if(isset($_POST['color'])) {
        $color = $_POST['color'];
    }
    $size = $_POST['size'];
    $qty = $_POST['qty'];
    $customerId = $_POST['customer_id'];

    if(isset($_POST['color'])) {
        $query_color_id = "SELECT option_id ";
        $query_color_id .= "FROM mgkf_eav_attribute_option_value ";
        $query_color_id .= "WHERE value = '".$color."'";
        $result_color_id = $conn->query($query_color_id);
        $row_color_id = $result_color_id->fetch_assoc();
        $color_id = $row_color_id['option_id'];
    }

    $query_size_id = "SELECT option_id ";
    $query_size_id .= "FROM mgkf_eav_attribute_option_value ";
    $query_size_id .= "WHERE value = $size";
    $result_size_id = $conn->query($query_size_id);
    $row_size_id = $result_size_id->fetch_assoc();
    $size_id = $row_size_id['option_id'];
    
    if(isset($_POST['color'])) {
        $attributes = '{"93":"'.$color_id.'", "136":"'.$size_id.'"}';
    }
    else {
        $attributes = '{"136":"'.$size_id.'"}';
    }

    $query_product_name = "SELECT sku ";
    $query_product_name .= "FROM mgkf_catalog_product_entity ";
    $query_product_name .= "WHERE entity_id = '".$productId."'";
    $result_product_name = $conn->query($query_product_name);
    $row_product_name = $result_product_name->fetch_assoc();
    $productName = $row_product_name['sku'];

    $query_product_sku = "SELECT sku ";
    $query_product_sku .= "FROM mgkf_catalog_product_entity ";
    $query_product_sku .= "WHERE entity_id = '".$childId."'";
    $result_product_sku = $conn->query($query_product_sku);
    $row_product_sku = $result_product_sku->fetch_assoc();
    $productSku = $row_product_sku['sku'];

    $query_price = "SELECT DISTINCT(max_price) as price ";
    $query_price .= "FROM mgkf_catalog_product_index_price ";
    $query_price .= "WHERE entity_id = '".$productId."'";
    $result_price = $conn->query($query_price);
    $row_price = $result_price->fetch_assoc();
    $productPriceDefault = number_format($row_price['price'], 2);
    $productPrice = number_format(($row_price['price'] * 4.726883), 2);
    $productPriceDefault = str_replace(",", "", $productPriceDefault);
    $productPrice = str_replace(",", "", $productPrice);

    $query_quote_id = "SELECT entity_id, items_count, items_qty, grand_total ";
    $query_quote_id .= "FROM mgkf_quote ";
    $query_quote_id .= "WHERE customer_id = '".$customerId."' ";
    $query_quote_id .= "AND is_active = 1";
    $result_quote_id = $conn->query($query_quote_id);
    if($result_quote_id->num_rows > 0) {
        $row_quote_id = $result_quote_id->fetch_assoc();
        $quoteId = $row_quote_id['entity_id'];
        $itemsCount = $row_quote_id['items_count'] + $qty;
        $itemsQty = $row_quote_id['items_qty'] + $qty;
        $grandTotal = number_format(($row_quote_id['grand_total'] + $productPrice), 2);
    }
    else {
        $query_quote = "INSERT INTO mgkf_quote ";
        $query_quote .= "(store_id, is_active, items_count, items_qty, grand_total, customer_id, customer_tax_class_id, customer_group_id) VALUES ";
        $query_quote .= "('4', '1', '".$qty."', '".$qty."', '".$productPrice."', '".$customerId."', '3', '0')";
        $result_quote = $conn->query($query_quote);
        
        $query_quote_id = "SELECT entity_id, items_count, items_qty, grand_total ";
        $query_quote_id .= "FROM mgkf_quote ";
        $query_quote_id .= "WHERE customer_id = '".$customerId."' ";
        $query_quote_id .= "AND is_active = 1";
        $result_quote_id = $conn->query($query_quote_id);
        $row_quote_id = $result_quote_id->fetch_assoc();
        $quoteId = $row_quote_id['entity_id'];
        $itemsCount = $row_quote_id['items_count'] + $qty;
        $itemsQty = $row_quote_id['items_qty'] + $qty;
        $grandTotal = number_format(($row_quote_id['grand_total'] + $productPrice), 2);
    }

    $query = "UPDATE mgkf_quote ";
    $query .= "SET items_count = '".$itemsCount."', ";
    $query .= "items_qty = '".$itemsQty."', ";
    $query .= "grand_total = '".$grandTotal."' ";
    $query .= "WHERE entity_id = '".$quoteId."'";
    $result = $conn->query($query);

    $query = "INSERT INTO mgkf_quote_item (quote_id, product_id, store_id, is_virtual, sku, name, applied_rule_ids, qty, price, base_price, row_total, base_row_total, product_type, price_incl_tax, base_price_incl_tax, row_total_incl_tax, base_row_total_incl_tax) ";
    $query .= "VALUES ('".$quoteId."', '".$productId."', '4', '0', '".$productSku."', '".$productName."', '2', '".$qty."', '".$productPriceDefault."' , '".$productPriceDefault."', '".$productPrice."', '".$productPriceDefault."', 'configurable', '".$productPrice."', '".$productPriceDefault."', '".$productPrice."', '".$productPriceDefault."');";
    $result = $conn->query($query);
    $last_id = $conn->insert_id;

    $query = "INSERT INTO mgkf_quote_item (quote_id, product_id, store_id, parent_item_id, is_virtual, sku, name, qty, price, base_price, row_total, base_row_total, product_type) ";
    $query .= "VALUES ('$quoteId', '$childId', '4', '$last_id', '0', '$productSku', '$productSku', '$qty', '0.00' , '0.00', '0.00', '0.00', 'simple');";
    $result = $conn->query($query);
    $last_inserted = $conn->insert_id;

    $query = "INSERT INTO mgkf_quote_item_option (item_id, product_id, code, value) ";
    $query .= "VALUES ('$last_id', '$productId', 'attributes', '$attributes')";
    $result = $conn->query($query);

    $query = "INSERT INTO mgkf_quote_item_option (item_id, product_id, code, value) ";
    $query .= "VALUES ('$last_inserted', '$childId', 'parent_product_id', '$productId')";
    $result = $conn->query($query);
    
    $query = "DELETE FROM mgkf_wishlist_item ";
    $query .= "WHERE wishlist_item_id = '".$itemId."' ";
    $result = $conn->query($query);

    $query = "DELETE FROM mgkf_wishlist_item_option ";
    $query .= "WHERE wishlist_item_id = '".$itemId."' ";
    $query .= "AND product_id = '".$productId."'";
    $result = $conn->query($query);
}
if(isset($_POST['guest'])) {
    $guest = $_POST['guest'];
    $itemId = $_POST['item_id'];
    $productId = $_POST['product_id'];
    $childId = $_POST['child_id'];
    if(isset($_POST['color'])) {
        $color = $_POST['color'];
    }
    $size = $_POST['size'];
    $qty = $_POST['qty'];

    if(isset($_POST['color'])) {
        $query_color_id = "SELECT option_id ";
        $query_color_id .= "FROM mgkf_eav_attribute_option_value ";
        $query_color_id .= "WHERE value = '".$color."'";
        $result_color_id = $conn->query($query_color_id);
        $row_color_id = $result_color_id->fetch_assoc();
        $color_id = $row_color_id['option_id'];
    }

    $query_size_id = "SELECT option_id ";
    $query_size_id .= "FROM mgkf_eav_attribute_option_value ";
    $query_size_id .= "WHERE value = $size";
    $result_size_id = $conn->query($query_size_id);
    $row_size_id = $result_size_id->fetch_assoc();
    $size_id = $row_size_id['option_id'];
    
    if(isset($_POST['color'])) {
        $attributes = '{"93":"'.$color_id.'", "136":"'.$size_id.'"}';
    }
    else {
        $attributes = '{"136":"'.$size_id.'"}';
    }

    $query_product_name = "SELECT sku ";
    $query_product_name .= "FROM mgkf_catalog_product_entity ";
    $query_product_name .= "WHERE entity_id = '".$productId."'";
    $result_product_name = $conn->query($query_product_name);
    $row_product_name = $result_product_name->fetch_assoc();
    $productName = $row_product_name['sku'];

    $query_product_sku = "SELECT sku ";
    $query_product_sku .= "FROM mgkf_catalog_product_entity ";
    $query_product_sku .= "WHERE entity_id = '".$childId."'";
    $result_product_sku = $conn->query($query_product_sku);
    $row_product_sku = $result_product_sku->fetch_assoc();
    $productSku = $row_product_sku['sku'];

    $query_price = "SELECT DISTINCT(max_price) as price ";
    $query_price .= "FROM mgkf_catalog_product_index_price ";
    $query_price .= "WHERE entity_id = '".$productId."'";
    $result_price = $conn->query($query_price);
    $row_price = $result_price->fetch_assoc();
    $productPriceDefault = number_format($row_price['price'], 2);
    $productPrice = number_format(($row_price['price'] * 4.726883), 2);
    $productPriceDefault = str_replace(",", "", $productPriceDefault);
    $productPrice = str_replace(",", "", $productPrice);

    $query_quote_id = "SELECT entity_id, items_count, items_qty, grand_total ";
    $query_quote_id .= "FROM mgkf_quote ";
    $query_quote_id .= "WHERE customer_id IS NULL ";
    $query_quote_id .= "AND is_active = 1 ";
    $query_quote_id .= "AND remote_ip = $guest";
    $result_quote_id = $conn->query($query_quote_id);
    $row_quote_id = $result_quote_id->fetch_assoc();
    $quoteId = $row_quote_id['entity_id'];
    $itemsCount = $row_quote_id['items_count'] + $qty;
    $itemsQty = $row_quote_id['items_qty'] + $qty;
    $grandTotal = number_format(($row_quote_id['grand_total'] + $productPrice), 2);

    $query = "UPDATE mgkf_quote ";
    $query .= "SET items_count = '".$itemsCount."', ";
    $query .= "items_qty = '".$itemsQty."', ";
    $query .= "grand_total = '".$grandTotal."' ";
    $query .= "WHERE entity_id = '".$quoteId."'";
    $result = $conn->query($query);

    $query = "INSERT INTO mgkf_quote_item (quote_id, product_id, store_id, is_virtual, sku, name, applied_rule_ids, qty, price, base_price, row_total, base_row_total, product_type, price_incl_tax, base_price_incl_tax, row_total_incl_tax, base_row_total_incl_tax) ";
    $query .= "VALUES ('".$quoteId."', '".$productId."', '4', '0', '".$productSku."', '".$productName."', '2', '".$qty."', '".$productPriceDefault."' , '".$productPriceDefault."', '".$productPrice."', '".$productPriceDefault."', 'configurable', '".$productPrice."', '".$productPriceDefault."', '".$productPrice."', '".$productPriceDefault."');";
    $result = $conn->query($query);
    $last_id = $conn->insert_id;

    $query = "INSERT INTO mgkf_quote_item (quote_id, product_id, store_id, parent_item_id, is_virtual, sku, name, qty, price, base_price, row_total, base_row_total, product_type) ";
    $query .= "VALUES ('$quoteId', '$childId', '4', '$last_id', '0', '$productSku', '$productSku', '$qty', '0.00' , '0.00', '0.00', '0.00', 'simple');";
    $result = $conn->query($query);
    $last_inserted = $conn->insert_id;

    $query = "INSERT INTO mgkf_quote_item_option (item_id, product_id, code, value) ";
    $query .= "VALUES ('$last_id', '$productId', 'attributes', '$attributes')";
    $result = $conn->query($query);

    $query = "INSERT INTO mgkf_quote_item_option (item_id, product_id, code, value) ";
    $query .= "VALUES ('$last_inserted', '$childId', 'parent_product_id', '$productId')";
    $result = $conn->query($query);

    $query = "DELETE FROM mgkf_guest_wishlist_item ";
    $query .= "WHERE id = '".$itemId."' ";
    $result = $conn->query($query);
}
?>