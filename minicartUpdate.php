<?php
if (file_exists("conf/conf.php"))
    @include 'conf/conf.php';

$conn = new mysqli($conf->db->host, $conf->db->user, $conf->db->pass, $conf->db->name);

$remote_ip = $_SERVER['REMOTE_ADDR'];
$date = date("Y-m-d");

if(isset($_POST['customer_id'])) {
    $query_base_info = "SELECT ";
}
else {
    $query_base_info = "SELECT mgkf_quote.customer_id, ";
}
$query_base_info .= "mgkf_quote.entity_id, mgkf_catalog_product_entity_int.value as seller_id, mgkf_quote_item.item_id, mgkf_quote_item.store_id, mgkf_quote_item.product_id, mgkf_quote_item.name as product_name, mgkf_quote_item.qty, mgkf_quote_item.row_total as total, mgkf_quote.quote_currency_code as currency, mgkf_quote.grand_total ";
$query_base_info .= "FROM mgkf_quote_item, mgkf_quote, mgkf_catalog_product_entity_int ";
$query_base_info .= "WHERE mgkf_quote_item.quote_id = mgkf_quote.entity_id ";
$query_base_info .= "AND mgkf_catalog_product_entity_int.entity_id = mgkf_quote_item.product_id ";
$query_base_info .= "AND mgkf_quote_item.row_total != '' ";
$query_base_info .= "AND mgkf_quote.is_active = 1 ";
$query_base_info .= "AND mgkf_catalog_product_entity_int.attribute_id = 158 ";
if($_POST['customer_id'] != '') {
    $customerId = $_POST['customer_id'];
    $query_base_info .= "AND mgkf_quote.customer_id = '".$customerId."' ";
}
else {
    $query_base_info .= "AND mgkf_quote.customer_id IS NULL ";
    $query_base_info .= "AND mgkf_quote.remote_ip = '".$remote_ip."' ";
}
$query_base_info .= "ORDER BY value ASC";
$result_base_info = $conn->query($query_base_info);
if($customerId != '') { ?>
    <input type="hidden" id="client" value="<?php echo $customerId; ?>" />
<?php
}
else { ?>
    <input type="hidden" id="client" value="<?php echo $remote_ip; ?>" />
<?php
}
if($result_base_info->num_rows > 0) {
    $i = 0;
    while($row_base_info = $result_base_info->fetch_assoc()) {
        if(isset($_POST['customer_id'])) {
            $customerId = $_POST['customer_id'];
        }
        else {
            $customerId = $row_base_info['customer_id'];
        }
        $entityId = $row_base_info['entity_id'];
        $itemId = $row_base_info['item_id'];
        $storeId = $row_base_info['store_id'];
        $sellerId = $row_base_info['seller_id'];
        $productId = $row_base_info['product_id'];
        $productName = $row_base_info['product_name'];
        $productQty = $row_base_info['qty'];
        $productTotal = $row_base_info['total'];
        $grandTotal = $row_base_info['grand_total'];
        $currency = ucfirst(strtolower($row_base_info['currency']));
        // get seller store name
        $query_seller_store_name = "SELECT store_name ";
        $query_seller_store_name .= "FROM mgkf_marketplace_seller ";
        $query_seller_store_name .= "WHERE customer_id = '".$sellerId."'";
        $result_seller_store_name = $conn->query($query_seller_store_name);
        $row_seller_store_name = $result_seller_store_name->fetch_assoc();
        $sellerStoreName = $row_seller_store_name['store_name'];
        // get product img path
        $query_product_img = "SELECT mgkf_catalog_product_entity_media_gallery.value ";
        $query_product_img .= "FROM mgkf_catalog_product_entity_media_gallery, mgkf_catalog_product_entity_media_gallery_value ";
        $query_product_img .= "WHERE mgkf_catalog_product_entity_media_gallery.value_id = mgkf_catalog_product_entity_media_gallery_value.value_id ";
        $query_product_img .= "AND mgkf_catalog_product_entity_media_gallery_value.entity_id = '".$productId."'";
        $result_product_img = $conn->query($query_product_img);
        $row_product_img = $result_product_img->fetch_assoc();
        $productImg = '/pub/media/catalog/product'.$row_product_img['value'];
        // get product url
        $query_product_url = "SELECT request_path FROM mgkf_url_rewrite ";
        $query_product_url .= "WHERE target_path = 'catalog/product/view/id/$productId' ";
        $query_product_url .= "ORDER BY url_rewrite_id DESC LIMIT 0,1";
        $result_product_url = $conn->query($query_product_url);
        $row_product_url = $result_product_url->fetch_row();
        $productUrl = $row_product_url[0];
        // get product options (attributes)
        $query_options = "SELECT value as options ";
        $query_options .= "FROM mgkf_quote_item_option ";
        $query_options .= "WHERE item_id = '".$itemId."' ";
        $query_options .= "AND code = 'attributes'";
        $result_options = $conn->query($query_options);
        $row_options = $result_options->fetch_assoc();
        $options = $row_options['options'];

        preg_match_all('!\d+!', $options, $matches);
        if(count($matches[0]) > 2) {
            $color_option_id = $matches[0][0];
            $color_option_value_id = $matches[0][1];
            $size_option_id = $matches[0][2];
            $size_option_value_id = $matches[0][3];
        }
        else {
            $size_option_id = $matches[0][0];
            $size_option_value_id = $matches[0][1];
        }
        
        if(isset($color_option_id)) {
            $query_color_value = "SELECT mgkf_eav_attribute_option_value.value, mgkf_eav_attribute_option_value.option_id ";
            $query_color_value .= "FROM mgkf_eav_attribute_option_value, mgkf_eav_attribute_option ";
            $query_color_value .= "WHERE mgkf_eav_attribute_option_value.option_id = mgkf_eav_attribute_option.option_id ";
            $query_color_value .= "AND mgkf_eav_attribute_option.attribute_id = '".$color_option_id."' ";
            $query_color_value .= "AND mgkf_eav_attribute_option_value.option_id = '".$color_option_value_id."' ";
            $query_color_value .= "AND mgkf_eav_attribute_option_value.store_id = '".$storeId."'";
            $result_color_value = $conn->query($query_color_value);
            $row_color_value = $result_color_value->fetch_assoc();
            $color = $row_color_value['value'];
        }

        $query_size_value = "SELECT mgkf_eav_attribute_option_value.value, mgkf_eav_attribute_option_value.option_id ";
        $query_size_value .= "FROM mgkf_eav_attribute_option_value, mgkf_eav_attribute_option ";
        $query_size_value .= "WHERE mgkf_eav_attribute_option_value.option_id = mgkf_eav_attribute_option.option_id ";
        $query_size_value .= "AND mgkf_eav_attribute_option.attribute_id = '".$size_option_id."' ";
        $query_size_value .= "AND mgkf_eav_attribute_option_value.option_id = '".$size_option_value_id."'";
        $result_size_value = $conn->query($query_size_value);
        $row_size_value = $result_size_value->fetch_assoc();
        $size = $row_size_value['value'];

        // get all available options (attributes)
        if(isset($color_option_id)) {
            $query_color = "SELECT value ";
            $query_color .= "FROM mgkf_catalog_product_relation, mgkf_catalog_product_entity_int ";
            $query_color .= "WHERE mgkf_catalog_product_relation.child_id = mgkf_catalog_product_entity_int.entity_id ";
            $query_color .= "AND parent_id = '".$productId."' ";
            $query_color .= "AND attribute_id = '".$color_option_id."' ";
            $query_color .= "AND value != '".$color_option_value_id."'";
            $query_color .= "GROUP BY value";
            $result_color = $conn->query($query_color);
        }
        
        $query_size = "SELECT value ";
        $query_size .= "FROM mgkf_catalog_product_relation, mgkf_catalog_product_entity_int ";
        $query_size .= "WHERE mgkf_catalog_product_relation.child_id = mgkf_catalog_product_entity_int.entity_id ";
        $query_size .= "AND parent_id = '".$productId."' ";
        $query_size .= "AND attribute_id = '".$size_option_id."' ";
        $query_size .= "AND value != '".$size_option_value_id."'";
        $query_size .= "GROUP BY value";
        $result_size = $conn->query($query_size);

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

        $query_price = "SELECT DISTINCT(max_price) FROM mgkf_catalog_product_index_price ";
        $query_price .= "WHERE entity_id = '".$productId."'";
        $result_price = $conn->query($query_price);
        $row_price = $result_price->fetch_assoc();
        $productPrice = (float)number_format($row_price['max_price'] * 4.726883, 2, ',', '');

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
        }

        if($countCategoryDiscount > 0) {
            $newPrice = number_format(($productPrice - ($productPrice * ($discountCategory / 100))), 2, '.', ',');
        }
        if($countProductDiscount > 0) {
            $newPrice = number_format(($productPrice - ($productPrice * ($discountProduct / 100))), 2, '.', ',');
        }
        if($countCategoryDiscount > 0 && $countProductDiscount > 0) {
            if($discountCategory >= $discountProduct) {
                $newPrice = number_format(($productPrice - ($productPrice * ($discountCategory / 100))), 2, '.', ',');
            }
            else {
                $newPrice = number_format(($productPrice - ($productPrice * ($discountProduct / 100))), 2, '.', ',');
            }
        }

        // qty options
        if($productQty == 1) {
            $productQtyOption_1 = 2;
            $productQtyOption_2 = 3;
            $productQtyOption_3 = 4;
            $productQtyOption_4 = 5;
            $productQtyOption_5 = 6;
            $productQtyOption_6 = 7;
            $productQtyOption_7 = 8;
            $productQtyOption_8 = 9;
            $productQtyOption_9 = 10;
        }
        elseif($productQty == 2) {
            $productQtyOption_1 = 1;
            $productQtyOption_2 = 3;
            $productQtyOption_3 = 4;
            $productQtyOption_4 = 5;
            $productQtyOption_5 = 6;
            $productQtyOption_6 = 7;
            $productQtyOption_7 = 8;
            $productQtyOption_8 = 9;
            $productQtyOption_9 = 10;
        }
        elseif($productQty == 3) {
            $productQtyOption_1 = 1;
            $productQtyOption_2 = 2;
            $productQtyOption_3 = 4;
            $productQtyOption_4 = 5;
            $productQtyOption_5 = 6;
            $productQtyOption_6 = 7;
            $productQtyOption_7 = 8;
            $productQtyOption_8 = 9;
            $productQtyOption_9 = 10;
        }
        elseif($productQty == 4) {
            $productQtyOption_1 = 1;
            $productQtyOption_2 = 2;
            $productQtyOption_3 = 3;
            $productQtyOption_4 = 5;
            $productQtyOption_5 = 6;
            $productQtyOption_6 = 7;
            $productQtyOption_7 = 8;
            $productQtyOption_8 = 9;
            $productQtyOption_9 = 10;
        }
        elseif($productQty == 5) {
            $productQtyOption_1 = 1;
            $productQtyOption_2 = 2;
            $productQtyOption_3 = 3;
            $productQtyOption_4 = 4;
            $productQtyOption_5 = 6;
            $productQtyOption_6 = 7;
            $productQtyOption_7 = 8;
            $productQtyOption_8 = 9;
            $productQtyOption_9 = 10;
        }
        elseif($productQty == 6) {
            $productQtyOption_1 = 1;
            $productQtyOption_2 = 2;
            $productQtyOption_3 = 3;
            $productQtyOption_4 = 4;
            $productQtyOption_5 = 5;
            $productQtyOption_6 = 7;
            $productQtyOption_7 = 8;
            $productQtyOption_8 = 9;
            $productQtyOption_9 = 10;
        }
        elseif($productQty == 7) {
            $productQtyOption_1 = 1;
            $productQtyOption_2 = 2;
            $productQtyOption_3 = 3;
            $productQtyOption_4 = 4;
            $productQtyOption_5 = 5;
            $productQtyOption_6 = 6;
            $productQtyOption_7 = 8;
            $productQtyOption_8 = 9;
            $productQtyOption_9 = 10;
        }
        elseif($productQty == 8) {
            $productQtyOption_1 = 1;
            $productQtyOption_2 = 2;
            $productQtyOption_3 = 3;
            $productQtyOption_4 = 4;
            $productQtyOption_5 = 5;
            $productQtyOption_6 = 6;
            $productQtyOption_7 = 7;
            $productQtyOption_8 = 9;
            $productQtyOption_9 = 10;
        }
        elseif($productQty == 9) {
            $productQtyOption_1 = 1;
            $productQtyOption_2 = 2;
            $productQtyOption_3 = 3;
            $productQtyOption_4 = 4;
            $productQtyOption_5 = 5;
            $productQtyOption_6 = 6;
            $productQtyOption_7 = 7;
            $productQtyOption_8 = 8;
            $productQtyOption_9 = 10;
        }
        else {
            $productQtyOption_1 = 1;
            $productQtyOption_2 = 2;
            $productQtyOption_3 = 3;
            $productQtyOption_4 = 4;
            $productQtyOption_5 = 5;
            $productQtyOption_6 = 6;
            $productQtyOption_7 = 7;
            $productQtyOption_8 = 8;
            $productQtyOption_9 = 9;
        }

        // get price per 1 piece
        $query_price_piece = "SELECT DISTINCT(max_price) as price ";
        $query_price_piece .= "FROM mgkf_catalog_product_index_price ";
        $query_price_piece .= "WHERE entity_id = '".$productId."'";
        $result_price_piece = $conn->query($query_price_piece);
        $row_price_piece = $result_price_piece->fetch_assoc();
        $price_per_piece = number_format(($row_price_piece['price'] * 4.726883), 2);

        $query_transports = "SELECT store_name, mgkf_marketplace_transports.seller_id, mgkf_marketplace_transports.country, mgkf_marketplace_transports.price, mgkf_marketplace_transports.price_free_transport ";
        $query_transports .= "FROM mgkf_marketplace_seller, mgkf_marketplace_transports ";
        $query_transports .= "WHERE mgkf_marketplace_seller.customer_id = mgkf_marketplace_transports.seller_id ";
        $query_transports .= "AND mgkf_marketplace_transports.seller_id = '".$sellerId."'";
        $result_transports = $conn->query($query_transports);
        ?>
        
        <input type="hidden" name="item_id" id="item_id_<?php echo $i; ?>" value="<?php echo $itemId; ?>" />
        <li class="product py-2 position-relative" data-role="product-item" data-collapsible="true">
            <span class="d-nonee"> 
                <i class="fal fa-times p-1 remove-product-icon" onclick="remove_item(<?php echo $i . ',' . $entityId . ',' . $itemId . ',' . $productTotal . ',' . $grandTotal; ?>);" id="remove-item"></i> 
            </span>
            <div style="display:none;" id="transport_info_<?php echo $sellerId . '_' . $i; ?>">
                <?php 
                $j = 0;
                while($row_transports = $result_transports->fetch_assoc()) {
                    $seller_id = $row_transports['seller_id'];
                    $store_name = $row_transports['store_name'];
                    $country = $row_transports['country'];
                    $price_transport = number_format(($row_transports['price'] * 4.726883), 2);
                    $price_free_transport = number_format(($row_transports['price_free_transport'] * 4.726883), 2);
                    ?>
                    <input type="hidden" id="store_name_<?php echo $seller_id . '_' . $j; ?>" value="<?php echo $store_name; ?>" />
                    <input type="hidden" id="country_<?php echo $seller_id . '_' . $j; ?>" value="<?php echo $country; ?>" />
                    <input type="hidden" id="price_transport_<?php echo $seller_id . '_' . $j; ?>" value="<?php echo $price_transport; ?>" />
                    <input type="hidden" id="price_free_transport_<?php echo $seller_id . '_' . $j; ?>" value="<?php echo $price_free_transport; ?>" />
                    <input type="hidden" id="grand_total_price" value="<?php echo $grandTotal; ?>" />
                    <?php
                    $j++;
                }
                ?>
            </div>
            <p class="store_name pb-2">
                Produse vandute si livrate de: 
                <?php echo '"' . $sellerStoreName . '"'; ?>
            </p>
            <div class="product row">
                <a class="product-item-photo col-4" href="<?php echo $productUrl; ?>" title="<?php echo $productName; ?>">
                    <span class="product-image-container">
                        <span class="product-image-wrapper">
                            <img src="<?php echo $productImg; ?>" alt="<?php echo $productName; ?>">
                        </span>
                    </span>
                </a>
                <div class="product-item-details col-8">
                    <strong>
                        <a class="text-capitalize" href="/ro/<?php echo $productUrl; ?>">
                            <?php echo $productName; ?>
                        </a>
                    </strong>
                    <div class="product options" role="tablist" data-collapsible="true">
                        <div data-role="content" class="d-flex align-items-center justify-content-between" role="tabpanel" aria-hidden="true">
                            <?php 
                            if($result_color->num_rows > 0) {
                            ?>
                                <div>
                                    <label class="label">
                                        Culoare
                                    </label>
                                    <dd class="values">
                                        <span>
                                            <select class="form-control form-control-sm" name="select_color" id="select_color">
                                                <option><?php echo $color; ?></option>
                                                <?php 
                                                while($row_color = $result_color->fetch_assoc()) { 
                                                    $option_id = $row_color['value'];
                                                    $query_value = "SELECT value ";
                                                    $query_value .= "FROM mgkf_eav_attribute_option_value ";
                                                    $query_value .= "WHERE option_id = '".$option_id."' ";
                                                    $query_value .= "AND store_id = '".$storeId."' ";
                                                    $query_value .= "AND value != '".$color."'";
                                                    $result_value = $conn->query($query_value); 
                                                    $row_value = $result_value->fetch_assoc();
                                                    $value = $row_value['value'];  
                                                ?>
                                                    <option value="<?php echo $option_id; ?>"><?php echo $value; ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </span>
                                    </dd>
                                </div>
                            <?php 
                            }
                            ?>
                            <div>
                                <label class="label">
                                    Marime
                                </label>
                                <dd class="values">
                                    <span>
                                        <select class="form-control form-control-sm" name="select_size" id="select_size">
                                            <option><?php echo $size; ?></option>
                                            <?php
                                            while($row_size = $result_size->fetch_assoc()) { 
                                                $option_id = $row_size['value'];
                                                $query_value = "SELECT value ";
                                                $query_value .= "FROM mgkf_eav_attribute_option_value ";
                                                $query_value .= "WHERE option_id = '".$option_id."' ";
                                                $query_value .= "AND value != '".$size."'";
                                                $result_value = $conn->query($query_value); 
                                                $row_value = $result_value->fetch_assoc();
                                                $value = $row_value['value'];  
                                            ?>
                                                <option value="<?php echo $option_id; ?>"><?php echo $value; ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </span>
                                </dd>
                            </div>
                            <div class="details-qty m-0">
                                <label class="label w-60">
                                    Cantitate
                                </label>
                                <select class="form-control form-control-sm" name="select_qty" id="select_qty_<?php echo $i; ?>" onchange="changed_qty(<?php echo $i; ?>);">
                                    <option>
                                        <?php echo intval($productQty); ?>
                                    </option>
                                    <option value="<?php echo $productQtyOption_1; ?>"><?php echo $productQtyOption_1; ?></option>
                                    <option value="<?php echo $productQtyOption_2; ?>"><?php echo $productQtyOption_2; ?></option>
                                    <option value="<?php echo $productQtyOption_3; ?>"><?php echo $productQtyOption_3; ?></option>
                                    <option value="<?php echo $productQtyOption_4; ?>"><?php echo $productQtyOption_4; ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="product-item-pricing">                                                 
                        <div class="price-container">
                            <span class="price-wrapper">
                                <span class="price-excluding-tax">
                                    <span class="minicart-price">
                                        <?php 
                                        if($countProductDiscount > 0 || $countCategoryDiscount > 0) { ?>
                                            <span style="text-decoration:line-through !important;" class="price">
                                                <span class="product_price_old"><?php echo number_format($productTotal, 2, '.', '') ?></span>
                                        <?php 
                                        }
                                        else { ?>
                                            <span class="price">
                                                <span class="product_price" id="product_price_<?php echo $seller_id . '_' . $i; ?>"><?php echo number_format($productTotal, 2, '.', '') ?></span>
                                                <?php echo ' '. $currency; ?>
                                                <input type="hidden" name="price_per_piece" id="price_per_piece_<?php echo $i; ?>" value="<?php echo $price_per_piece; ?>" />
                                        <?php
                                        }
                                        ?>
                                            <input type="hidden" id="seller_2_shipping_price_<?php echo $i; ?>" value=""/>
                                            <input type="hidden" id="seller_7_shipping_price_<?php echo $i; ?>" value=""/>
                                        </span>
                                        <?php
                                        if($countProductDiscount > 0 || $countCategoryDiscount > 0) { ?>
                                            <span style="color:red!important;font-weight:700;" class="product_price" id="product_price_<?php echo $seller_id . '_' . $i; ?>">
                                                <?php echo $newPrice; ?>
                                            </span>
                                            <span style="color:red!important;font-size:1.1rem;font-weight:700;"><?php echo $currency; ?></span>
                                            <input type="hidden" name="price_per_piece" id="price_per_piece_<?php echo $i; ?>" value="<?php echo $newPrice; ?>" />                           
                                        <?php
                                        }
                                        ?>
                                    </span>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    <?php 
    $i++;
    }
}
?>