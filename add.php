<?php
if (file_exists("conf/conf.php")) 
    @include 'conf/conf.php';

$conn = new mysqli($conf->db->host, $conf->db->user, $conf->db->pass, $conf->db->name);

$date = date("Y-m-d");

if(isset($_POST['name'])) {
    $name = $_POST['name'];

    $lang = $_POST['lang'];

    if($_POST['sku'] == '') {
        $string = strtolower($name);
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        $string = preg_replace("/[\s-]+/", " ", $string);
        $string = preg_replace("/[\s_]/", "-", $string);
        $sku = $string;
    }
    else {
        $sku = $_POST['sku'];
    }

    $seller_id = $_POST['seller_id'];
    $query = "SELECT store_name FROM mgkf_marketplace_seller ";
    $query .= "WHERE customer_id = '$seller_id'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $store_name = $row['store_name'];

    $query = "SELECT DISTINCT(option_id) FROM mgkf_eav_attribute_option_value ";
    $query .= "WHERE value = '".$store_name."'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $vandut_de = $row['option_id'];
    $vandut_de_id = 236;

    $price = $_POST['price'];
    $category_id_name = $_POST['product_category'];
    $category = intval($category_id_name);
    $category_name_basic = str_replace($category, '', $category_id_name);
    $category_name = strtolower($category_name_basic);
    $subcategory = $_POST['product_subcategory'];
    $attribute_set = $_POST['set'];
    if(isset($_POST['color'])) {
        $color = $_POST['color'];
    }
    $size = $_POST['size'];
    $qty = $_POST['qty'];

    $category_name = str_replace(' ', '-', $category_name);  
    $subcategory = $category_name . '/' . $subcategory;

    $query = "SELECT entity_id FROM mgkf_catalog_category_entity_varchar ";
    $query .= "WHERE value LIKE '%$subcategory%'";
    $result = $conn->query($query);
    $subcategory = $result->fetch_row();
    $subcategory = $subcategory[0];
  
    if ($_POST['brand'] != '') {
        $brand_name = $_POST['brand'];
    }
    if ($_POST['new_brand_name'] != '') {
        $brand_name = $_POST['new_brand_name'];
        $query_brand = "INSERT INTO mgkf_marketplace_brand_requests (seller_id, brand_name, status) ";
        $query_brand.= "VALUES ('$seller_id', '$brand_name', '0')";
        $result_brand = $conn->query($query_brand);
    }     
    
    $brand_id = $_POST['brand'];

    $query_brand_name = "SELECT DISTINCT(value) ";
    $query_brand_name .= "FROM mgkf_eav_attribute_option_value, mgkf_eav_attribute_option ";
    $query_brand_name .= "WHERE mgkf_eav_attribute_option_value.option_id = mgkf_eav_attribute_option.option_id ";
    $query_brand_name .= "AND attribute_id = 197 ";
    $query_brand_name .= "AND mgkf_eav_attribute_option_value.option_id = '".$brand_id."'";
    $result_brand_name = $conn->query($query_brand_name);
    $row_brand_name = $result_brand_name->fetch_assoc();
    $brand = $row_brand_name['value'];

    $query_category_brand = "SELECT entity_id FROM mgkf_catalog_category_entity_varchar ";
    $query_category_brand .= "WHERE value = '$brand'";
    $result_category_brand = $conn->query($query_category_brand);
    $row_category_brand = $result_category_brand->fetch_assoc();
    $category_brand_id = $row_category_brand['entity_id'];

    $brand_id = 197;
    $material_id = 131;
    $model_id = 200;
    $stil_id = 201;
    $imprimeu_id = 202;
    $sezon_id = 205;
    $talie_id = 203;
    $maneca_id = 204;
    $unicat_id = 229;
    $guler_id = 230;
    $gluga_id = 231;
    $interior_id = 212;
    $tip_cupa_id = 221;
    $sarma_cupa_id = 222;
    $burete_cupa_id = 223;
    $bretele_id = 224;
    $speciale_id = 225;
    $tip_inchidere_id = 209;
    $tip_varf_id = 210;
    $tip_toc_id = 211;
    $inaltime_toc_id = 213;
    $personaje_id = 232;
    $ocazie_id = 233;
    $sarbatoare_id = 234;
    $marime_id = 235;

    $brand = $_POST['brand'];
    $haine_material = $_POST['haine-material'];
    $haine_model = $_POST['haine-model'];
    $haine_stil = $_POST['haine-stil'];
    $haine_imprimeu = $_POST['haine-imprimeu'];
    $haine_sezon = $_POST['haine-sezon'];
    $haine_talie = $_POST['haine-talie'];
    $haine_maneca = $_POST['haine-maneca'];
    $haine_unicat = $_POST['haine-unicat'];
    $haine_guler = $_POST['haine-guler'];
    $haine_gluga = $_POST['haine-gluga'];
    $haine_interior = $_POST['haine-interior'];
    $chiloti_material = $_POST['chiloti-dama-material'];
    $chiloti_talie = $_POST['chiloti-dama-talie'];
    $chiloti_model = $_POST['chiloti-dama-model'];
    $chiloti_stil = $_POST['chiloti-dama-stil'];
    $sutiene_material = $_POST['sutiene-material'];
    $sutiene_tip_cupa = $_POST['sutiene-tip_cupa'];
    $sutiene_sarma_cupa = $_POST['sutiene-sarma_cupa'];
    $sutiene_burete_cupa = $_POST['sutiene-burete_cupa'];
    $sutiene_bretele = $_POST['sutiene-bretele'];
    $sutiene_speciale = $_POST['sutiene-speciale'];
    $sutiene_stil = $_POST['sutiene-stil'];
    $incaltaminte_material = $_POST['incaltaminte-material'];
    $incaltaminte_stil = $_POST['incaltaminte-stil'];
    $incaltaminte_tip_inchidere = $_POST['incaltaminte-tip_inchidere'];
    $incaltaminte_tip_varf = $_POST['incaltaminte-tip_varf'];
    $incaltaminte_tip_toc = $_POST['incaltaminte-tip_toc'];
    $incaltaminte_interior = $_POST['incaltaminte-interior'];
    $incaltaminte_inaltime_toc = $_POST['incaltaminte-inaltime_toc'];
    $incaltaminte_imprimeu = $_POST['incaltaminte-imprimeu'];
    $incaltaminte_sezon = $_POST['incaltaminte-sezon'];
    $incaltaminte_unicat = $_POST['incaltaminte-unicat'];
    $lenjerie_barbati_material = $_POST['lenjerie-barbati-material'];
    $lenjerie_barbati_stil = $_POST['lenjerie-barbati-stil'];
    $haine_copii_material = $_POST['haine-copii-material'];
    $haine_copii_personaje = $_POST['haine-copii-personaje'];
    $haine_copii_imprimeu = $_POST['haine-copii-imprimeu'];
    $haine_copii_ocazie = $_POST['haine-copii-ocazie'];
    $haine_copii_sarbatoare = $_POST['haine-copii-sarbatoare'];
    $haine_copii_marime = $_POST['haine-copii-marime'];

    $description = '';

    $product = $_POST['product'];

    $full_text_scope = $_POST['full_text_scope'];
    $full_text_scope_count = count($full_text_scope);
    if($full_text_scope_count == 1) {
        $enabled = 'Enabled | Enabled';
        $tax = 'None | None';
    }
    if($full_text_scope_count == 2) {
        $enabled = 'Enabled | Enabled | Enabled';
        $tax = 'None | None | None';
    }
    if($full_text_scope_count == 3) {
        $enabled = 'Enabled | Enabled | Enabled | Enabled';
        $tax = 'None | None | None | None';
    }
    if($full_text_scope_count == 4) {
        $enabled = 'Enabled | Enabled | Enabled | Enabled | Enabled';
        $tax = 'None | None | None | None | None';
    }
    if($full_text_scope_count == 5) {
        $enabled = 'Enabled | Enabled | Enabled | Enabled | Enabled | Enabled';
        $tax = 'None | None | None | None | None | None';
    }
    if($full_text_scope_count == 6) {
        $enabled = 'Enabled | Enabled | Enabled | Enabled | Enabled | Enabled | Enabled';
        $tax = 'None | None | None | None | None | None | None';
    }
    if($full_text_scope_count == 7) {
        $enabled = 'Enabled | Enabled | Enabled | Enabled | Enabled | Enabled | Enabled | Enabled';
        $tax = 'None | None | None | None | None | None| None| None ';
    }
    if($full_text_scope_count == 8) {
        $enabled = 'Enabled | Enabled | Enabled | Enabled | Enabled | Enabled | Enabled | Enabled | Enabled';
        $tax = 'None | None | None | None | None | None | None | None | None';
    }
    if($full_text_scope_count == 9) {
        $enabled = 'Enabled | Enabled | Enabled | Enabled | Enabled | Enabled | Enabled | Enabled | Enabled | Enabled';
        $tax = 'None | None | None | None | None | None | None | None | None | None';
    }
    if($full_text_scope_count == 10) {
        $enabled = 'Enabled | Enabled | Enabled | Enabled | Enabled | Enabled | Enabled | Enabled | Enabled | Enabled | Enabled';
        $tax = 'None | None | None | None | None | None | None | None | None | None | None';
    }    

    $full_text_scope = implode(" | ", $full_text_scope);

    for($i=0; $i<count($qty); $i++) {
        $product[$i] = $product[$i] . '_' . $qty[$i];
        $product[$i] = str_replace('_',' ',$product[$i]);
        if(isset($_POST['color'])) {
            $color = preg_replace('/[0-9]+/', '', $product[$i]);
        }
        $size = substr($product[$i], 0, 3); 
        $quantity = str_replace($size, '', $product[$i]);
        if(isset($_POST['color'])) {
            $quantity = str_replace($color, '', $quantity);
        }
    }

    $basic_path = '/home/atytude/public_html/pub/media/catalog/product/'.$d_1.'/'.$d_2.'/';
    if(!file_exists($basic_path)) {
        mkdir($basic_path, 0775, true);
    }
    $file = $basic_path . $image;

    if(!copy("/home/atytude/public_html/pub/media/catalog/product/cache/021a5445bd4418c7815af89045564cdd/$d_1/$d_2/$image", $file)) {}
}
    
$countfiles = count($_FILES['product_images']['name']);
for($i=0;$i<$countfiles;$i++){
    $filename = $_FILES['product_images']['name'][$i];
    $dir_1 = $filename[0];
    $dir_2 = $filename[1];

    $dir = '/home/atytude/public_html/pub/media/catalog/product/cache';
    $subdirs = array_diff(scandir($dir), array('..', '.'));

    foreach($subdirs as $subdir) {
        $path = '/home/atytude/public_html/pub/media/catalog/product/cache/'.$subdir.'/'.$dir_1.'/'.$dir_2.'/';
        if(!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $path_file = '/home/atytude/public_html/pub/media/catalog/product/cache/'.$subdir.'/'.$dir_1.'/'.$dir_2.'/' . $filename;
        move_uploaded_file($_FILES['product_images']['tmp_name'][$i], $path_file);
        if(!copy("/home/atytude/public_html/pub/media/catalog/product/cache/021a5445bd4418c7815af89045564cdd/$dir_1/$dir_2/$filename", $path_file)) {}
    }

    $basic_path = '/home/atytude/public_html/pub/media/catalog/product/'.$dir_1.'/'.$dir_2.'/';
    if(!file_exists($basic_path)) {
        mkdir($basic_path, 0775, true);
    }
    $file = $basic_path . $filename;

    if(!copy("/home/atytude/public_html/pub/media/catalog/product/cache/021a5445bd4418c7815af89045564cdd/$dir_1/$dir_2/$filename", $file)) {}
}

$query_seller_currency = "SELECT currency FROM mgkf_marketplace_seller_currency ";
$query_seller_currency .= "WHERE seller_id = '".$seller_id."'";
$result_seller_currency = $conn->query($query_seller_currency);
$row_seller_currency = $result_seller_currency->fetch_row();
$seller_currency = $row_seller_currency[0];

$price_id = 237;

// Price definitions for filter
$price_1 = 472;
$price_2 = 473;
$price_3 = 474;
$price_4 = 475;
$price_5 = 476;
$price_6 = 477;
$price_7 = 478;
$price_8 = 479;
$price_9 = 480;

if($seller_currency != '') {   
    $currency = $seller_currency;
    if($currency == 'RON') {
        if($price < 50) {
            $price_nr = $price_1;
        }
        if($price > 50 && $price < 100) {
            $price_nr = $price_2;
        }
        if($price > 100 && $price < 200) {
            $price_nr = $price_3;
        }
        if($price > 200 && $price < 300) {
            $price_nr = $price_4;
        }
        if($price > 300 && $price < 400) {
            $price_nr = $price_5;
        }
        if($price > 400 && $price < 500) {
            $price_nr = $price_6;
        }
        if($price > 500 && $price < 750) {
            $price_nr = $price_7;
        }
        if($price > 750 && $price < 1000) {
            $price_nr = $price_8;
        }
        if($price > 1000) {
            $price_nr = $price_9;
        }

        $price = $price / 4.726883;
    }
    else if($currency == 'USD') {
        $price = $price / 1.121762;
    }
    else if($currency == 'EUR') {
        $price = $price;
    }
}

for($i=0;$i<count($qty);$i++) {
    $product[$i] = $product[$i] . '_' . $qty[$i];
    $product[$i] = str_replace('_',' ',$product[$i]);
    if(isset($_POST['color'])) {
        $color = preg_replace('/[0-9]+/', '', $product[$i]);
    }
    $size = substr($product[$i], 0, 3); 
    $size = str_replace(' ', '', $size);
    $size_database = $size;
    if($size == 28) $size_database = 172;
    if($size == 29) $size_database = 173;
    if($size == 30) $size_database = 174;
    if($size == 31) $size_database = 175;
    if($size == 32) $size_database = 176;
    if($size == 33) $size_database = 177;
    if($size == 34) $size_database = 178;
    if($size == 36) $size_database = 179;
    if($size == 38) $size_database = 180;
    
    $quantity = str_replace($size, '', $product[$i]);
    if(preg_match_all('/\d+/', $quantity, $numbers))
        $quantity = end($numbers[0]);
    
    $sku = str_replace(' ', '', $sku);
    if(isset($_POST['color'])) {
        $simple_product = $sku.'-'.$size.'-'.$color;
    }
    else {
        $simple_product = $sku.'-'.$size;
    }    
    $simple_product = str_replace('- ', '-', $simple_product);

    if(isset($_POST['color'])) {
        $color = str_replace(' ', '', $color);
        $color_default = $color;
        $color_lowercase = strtolower($color);

        if($color == 'Black') {
            $option_color = 49;
        }
        if($color == 'Blue') {
            $option_color = 50;
        }
        if($color == 'Brown') {
            $option_color = 51;
        }
        if($color == 'Gray') {
            $option_color = 52;
        }
        if($color == 'Green') {
            $option_color = 53;
        }
        if($color == 'Lavender') {
            $option_color = 54;
        }
        if($color == 'Multi') {
            $option_color = 55;
        }
        if($color == 'Orange') {
            $option_color = 56;
        }
        if($color == 'Purple') {
            $option_color = 57;
        }
        if($color == 'Red') {
            $option_color = 58;
        }
        if($color == 'White') {
            $option_color = 59;
        }
        if($color == 'Yellow') {
            $option_color = 60;
        }

        $multiple = $sku.'-'.$color.'-'.$size;
    }
    else {
        $multiple = $sku.'-'.$size;
    }


    $query = "INSERT INTO `mgkf_catalog_product_entity` (`attribute_set_id`, `type_id`, `sku`, `has_options`, `required_options`) VALUES ";
    $query .= "(4, 'simple', '".$multiple."', 0, 0)";
    $result = $conn->query($query);
    $last_insert_id = $conn->insert_id;

    $query = "SELECT MAX(entity_id) as entity_id ";
    $query .= "FROM mgkf_catalog_product_entity ";
    $query .= "WHERE type_id = 'configurable'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $test_id = $row['entity_id'] + 1;

    $total = count($qty) + $test_id;

    $image = $_FILES['color_image_'.$i]['name'];
        
    $dir = '/home/atytude/public_html/pub/media/catalog/product/product_images/';
    $new_dir = $dir.$total;

    if($image != '') {
        mkdir($new_dir, 0777, true);

        $file = $new_dir.'/'.$image;
        $moved = move_uploaded_file($_FILES['color_image_'.$i]['tmp_name'], $file);

        $image = $option_color.'.jpg';
        $new_file = $new_dir.'/'.$image;
        rename($file, $new_file);
    }

    $query = "INSERT INTO `mgkf_catalog_product_entity_varchar` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
    $query .= "(180, 0, $last_insert_id, '1'), ";
    $query .= "(179, 0, $last_insert_id, '1'), ";
    $query .= "(121, 0, $last_insert_id, '$simple_product'), ";
    $query .= "(118, 0, $last_insert_id, '0'), ";
    $query .= "(106, 0, $last_insert_id, 'container2'), ";
    $query .= "(86, 0, $last_insert_id, '$name'), ";
    $query .= "(84, 0, $last_insert_id, '$name'), ";
    $query .= "(73, 0, $last_insert_id, '$simple_product'), ";
    $query .= "(128, 0, $last_insert_id, 0);";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_inventory_low_stock_notification_configuration` (`source_code`, `sku`, `notify_stock_qty`) VALUES ";
    $query .= "('default', '".$name."', NULL);";
    $result = $conn->query($query);

    if(isset($_POST['color'])) {
        $string1 = $name.'-'.$size.'-'.$color;
        $string2 = $sku.'-'.$color_default.'-'.$size;

        $query = "INSERT INTO `mgkf_inventory_low_stock_notification_configuration` (`source_code`, `sku`, `notify_stock_qty`) VALUES ";
        $query .= "('default', '".$string1."', NULL);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_inventory_source_item` (`source_item_id`, `source_code`, `sku`, `quantity`, `status`) VALUES ";
        $query .= "($last_insert_id, 'default', '".$string2."', $quantity, 1);";
        $result = $conn->query($query);
    }
    else {
        $string1 = $name . '-' . $size;
        $string2 = $sku . '-' . $size;

        $query = "INSERT INTO `mgkf_inventory_low_stock_notification_configuration` (`source_code`, `sku`, `notify_stock_qty`) VALUES ";
        $query .= "('default', '".$string1."', NULL);";
        $result = $conn->query($query);
        
        $query = "INSERT INTO `mgkf_inventory_source_item` (`source_item_id`, `source_code`, `sku`, `quantity`, `status`) VALUES ";
        $query .= "($last_insert_id, 'default', '".$string2."', $quantity, 1);";
        $result = $conn->query($query);
    }

    $query = "INSERT INTO `mgkf_cataloginventory_stock_status` (`product_id`, `website_id`, `stock_id`, `qty`, `stock_status`) VALUES ";
    $query .= "($last_insert_id, 0, 1, '".$quantity."', 1);";
    $result = $conn->query($query);

    $query = "ALTER VIEW `mgkf_inventory_stock_1` AS select distinct `legacy_stock_status`.`product_id` AS `product_id`,`legacy_stock_status`.`website_id` AS `website_id`,`legacy_stock_status`.`stock_id` AS `stock_id`,`legacy_stock_status`.`qty` AS `quantity`,`legacy_stock_status`.`stock_status` AS `is_salable`,`product`.`sku` AS `sku` from (`mgkf_cataloginventory_stock_status` `legacy_stock_status` join `mgkf_catalog_product_entity` `product` on((`legacy_stock_status`.`product_id` = `product`.`entity_id`)))";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_cataloginventory_stock_item` (`product_id`, `stock_id`, `qty`, `min_qty`, `use_config_min_qty`, `is_qty_decimal`, `backorders`, `use_config_backorders`, `min_sale_qty`, `use_config_min_sale_qty`, `max_sale_qty`, `use_config_max_sale_qty`, `is_in_stock`, `low_stock_date`, `notify_stock_qty`, `use_config_notify_stock_qty`, `manage_stock`, `use_config_manage_stock`, `stock_status_changed_auto`, `use_config_qty_increments`, `qty_increments`, `use_config_enable_qty_inc`, `enable_qty_increments`, `is_decimal_divided`, `website_id`) VALUES ";
    $query .= "($last_insert_id, 1, '".$quantity."', 0.0000, 1, 0, 0, 1, 1.0000, 1, 0.0000, 1, 1, NULL, NULL, 1, 1, 0, 0, 1, 0.0000, 1, 0, 0, 0);";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_catalog_category_product` (`category_id`, `product_id`, `position`) VALUES ";
    $query .= "($category, '$last_insert_id', 0), ";
    $query .= "($subcategory, '".$last_insert_id."', 0);";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_catalog_product_entity_decimal` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
    $query .= "(77, 0, $last_insert_id, '".$price."');";  
    $result = $conn->query($query);

    if(isset($_POST['color'])) {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "(195, 0, $last_insert_id, 0), ";
        $query .= "(188, 0, $last_insert_id, 1), ";
        $query .= "(184, 0, $last_insert_id, 1), ";
        $query .= "(181, 0, $last_insert_id, 1), ";
        $query .= "(159, 0, $last_insert_id, 1), ";
        $query .= "(158, 0, $last_insert_id, $seller_id), ";
        $query .= "(136, 0, $last_insert_id, $size_database), ";
        $query .= "(127, 0, $last_insert_id, 2), ";
        $query .= "(99, 0, $last_insert_id, 1), ";
        $query .= "(97, 0, $last_insert_id, 1), ";
        $query .= "(93, 0, $last_insert_id, $option_color);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "(93, 1, $option_color, $last_insert_id), ";
        $query .= "(93, 4, $option_color, $last_insert_id), ";
        $query .= "(93, 7, $option_color, $last_insert_id), ";
        $query .= "(99, 1, 1, $last_insert_id), ";
        $query .= "(99, 4, 1, $last_insert_id), ";
        $query .= "(99, 7, 1, $last_insert_id), ";
        $query .= "(136, 1, $size_database, $last_insert_id), ";
        $query .= "(136, 4, $size_database, $last_insert_id), ";
        $query .= "(136, 7, $size_database, $last_insert_id)";
        $result = $conn->query($query);
    }
    else {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "(195, 0, $last_insert_id, 0), ";
        $query .= "(188, 0, $last_insert_id, 1), ";
        $query .= "(184, 0, $last_insert_id, 1), ";
        $query .= "(181, 0, $last_insert_id, 1), ";
        $query .= "(159, 0, $last_insert_id, 1), ";
        $query .= "(158, 0, $last_insert_id, $seller_id), ";
        $query .= "(136, 0, $last_insert_id, $size_database), ";
        $query .= "(127, 0, $last_insert_id, 2), ";
        $query .= "(99, 0, $last_insert_id, 1), ";
        $query .= "(97, 0, $last_insert_id, 1)";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_insert_id, 99, 1, 1, $last_insert_id), ";
        $query .= "($last_insert_id, 99, 4, 1, $last_insert_id), ";
        $query .= "($last_insert_id, 99, 7, 1, $last_insert_id), ";
        $query .= "($last_insert_id, 136, 1, $size_database, $last_insert_id), ";
        $query .= "($last_insert_id, 136, 4, $size_database, $last_insert_id), ";
        $query .= "($last_insert_id, 136, 7, $size_database, $last_insert_id)";
        $result = $conn->query($query);
    }

    $query = "INSERT INTO `mgkf_catalog_product_index_price` (`entity_id`, `customer_group_id`, `website_id`, `tax_class_id`, `price`, `final_price`, `min_price`, `max_price`, `tier_price`) VALUES ";
    $query .= "($last_insert_id, 4, 1, 0, $price, $price, $price, $price, NULL), ";
    $query .= "($last_insert_id, 3, 1, 0, $price, $price, $price, $price, NULL), ";
    $query .= "($last_insert_id, 2, 1, 0, $price, $price, $price, $price, NULL), ";
    $query .= "($last_insert_id, 1, 1, 0, $price, $price, $price, $price, NULL), ";
    $query .= "($last_insert_id, 0, 1, 0, $price, $price, $price, $price, NULL)";
    $result = $conn->query($query);
    
    $query = "INSERT INTO `mgkf_catalog_product_website` (`product_id`, `website_id`) VALUES ";
    $query .= "($last_insert_id, 1)";
    $result = $conn->query($query);
}

$query = "INSERT INTO `mgkf_catalog_product_entity` (`entity_id`, `attribute_set_id`, `type_id`, `sku`, `has_options`, `required_options`) VALUES ";
$query .= "($last_insert_id+1, 4, 'configurable', '".$name."', 0, 1);";
$result = $conn->query($query);
$last_id = $conn->insert_id;

if(isset($_POST['product_discount_type'])) {
    $productPrice = number_format(($price * 4.726883), 2, '.', '');
    $productPrice = $productPrice . ' ' . $seller_currency;
    $discount = $_POST['product_discount_type'];
    $dateFrom = $_POST['product_date_from'];
    $dateTo = $_POST['product_date_to'];
    if($date >= $dateFrom && $date <= $dateTo) {
        $isActive = 1;
    }
    else {
        $isActive = 0;
    }

    $query_discount = "INSERT INTO mgkf_marketplace_discount_products ";
    $query_discount .= "(seller_id, product_id, product_name, product_price, discount, is_active, date_from, date_to) ";
    $query_discount .= "VALUES ";
    $query_discount .= "('".$seller_id."', '".$last_id."', '".$name."', '".$productPrice."', '".$discount."', '".$isActive."', '".$dateFrom."', '".$dateTo."')";
    $result_discount = $conn->query($query_discount);
}

$query = "INSERT INTO mgkf_product_attributes (`product_id`, `brand`, `material`, `model`, `stil`, `imprimeu`, `sezon`, `talie`, `maneca`) ";
$query .= "VALUES ($last_id, '$brand', '$material', '$model', '$stil', '$imprimeu', '$sezon', '$talie', '$maneca');";
$result = $conn->query($query);

$query = "INSERT INTO `mgkf_catalogsearch_fulltext_scope1` (`entity_id`, `attribute_id`, `data_index`) VALUES ";
$query .= "($last_id, 73, '$name | $full_text_scope'), ";
$query .= "($last_id, 74, '$name'), ";
$query .= "($last_id, 75, '$description'), ";
$query .= "($last_id, 97, '$enabled'), ";
$query .= "($last_id, 127, '$tax');";
$result = $conn->query($query);

$query = "INSERT INTO `mgkf_catalogsearch_fulltext_scope4` (`entity_id`, `attribute_id`, `data_index`) VALUES ";
$query .= "($last_id, 73, '$name | $full_text_scope'), ";
$query .= "($last_id, 74, '$name'), ";
$query .= "($last_id, 75, '$description'), ";
$query .= "($last_id, 97, '$enabled'), ";
$query .= "($last_id, 127, '$tax');";
$result = $conn->query($query);

$query = "INSERT INTO `mgkf_catalog_category_product` (`category_id`, `product_id`, `position`) VALUES ";
$query .= "($category, '$last_id', 0), ";
$query .= "($subcategory, '$last_id', 0);";
$result = $conn->query($query);

for($i=0;$i<count($qty);$i++) {
    $product[$i] = $product[$i] . '_' . $qty[$i];
    $product[$i] = str_replace('_',' ',$product[$i]);
    if(isset($_POST['color'])) {
        $color = preg_replace('/[0-9]+/', '', $product[$i]);
    }    
    $size = substr($product[$i], 0, 3); 
    $size_database = $size;
    if($size == 172) $size = 28;
    if($size == 173) $size = 29;
    if($size == 174) $size = 30;
    if($size == 175) $size = 31;
    if($size == 176) $size = 32;
    if($size == 177) $size = 33;
    if($size == 178) $size = 34;
    if($size == 179) $size = 36;
    if($size == 180) $size = 38;
    $quantity = str_replace($size, '', $product[$i]);
    if(isset($_POST['color'])) {
        $quantity = str_replace($color, '', $quantity);
        $simple_product = $sku.'-'.$size.'-'.$color;
    }
    else {
        $simple_product = $sku.'-'.$size;
    }

    $query = "INSERT INTO `mgkf_catalog_product_entity_text` ( `attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
    $query .= "(75, 0, $last_id, '".$description."');";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_catalog_product_entity_varchar` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
    $query .= "(128, 0, $last_id, '2'), ";
    $query .= "(121, 0, $last_id, '$sku'), ";
    $query .= "(118, 0, $last_id, '0'), ";
    $query .= "(106, 0, $last_id, 'container2'), ";
    $query .= "(86, 0, $last_id, '$description'), ";
    $query .= "(180, 0, $last_id, '1'), ";
    $query .= "(179, 0, $last_id, '1'), ";
    $query .= "(84, 0, $last_id, '$name'), ";
    $query .= "(73, 0, $last_id, '$name');";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_cataloginventory_stock_status` (`product_id`, `website_id`, `stock_id`, `qty`, `stock_status`) VALUES ";
    $query .= "($last_id, 0, 1, 0.0000, 1);";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_url_rewrite` (`entity_type`, `entity_id`, `request_path`, `target_path`, `redirect_type`, `store_id`, `description`, `is_autogenerated`, `metadata`) VALUES ";
    $query .= "('product', $last_id, '$sku', 'catalog/product/view/id/$last_id', 0, 4, NULL, 1, NULL), ";
    $query .= "('product', $last_id, '$sku', 'catalog/product/view/id/$last_id', 0, 1, NULL, 1, NULL), ";
    $query .= "('product', $last_id, '$sku', 'catalog/product/view/id/$last_id', 0, 7, NULL, 1, NULL), ";
    $query .= "('product', $last_id, '/$sku', 'catalog/product/view/id/$last_id', 0, 4, NULL, 1, '{\"category_id\":\"2\"}'), ";
    $query .= "('product', $last_id, '/$sku', 'catalog/product/view/id/$last_id', 0, 1, NULL, 1, '{\"category_id\":\"2\"}'), ";
    $query .= "('product', $last_id, '/$sku', 'catalog/product/view/id/$last_id', 0, 7, NULL, 1, '{\"category_id\":\"2\"}'), ";
    $query .= "('product', $last_id, '$category_name/$sku', 'catalog/product/view/id/$last_id/category/$category', 0, 4, NULL, 1, '{\"category_id\":\"$category\"}'), ";
    $query .= "('product', $last_id, '$category_name/$sku', 'catalog/product/view/id/$last_id/category/$category', 0, 1, NULL, 1, '{\"category_id\":\"$category\"}'), ";
    $query .= "('product', $last_id, '$category_name/$sku', 'catalog/product/view/id/$last_id/category/$category', 0, 7, NULL, 1, '{\"category_id\":\"$category\"}')";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_cataloginventory_stock_item` (`product_id`, `stock_id`, `qty`, `min_qty`, `use_config_min_qty`, `is_qty_decimal`, `backorders`, `use_config_backorders`, `min_sale_qty`, `use_config_min_sale_qty`, `max_sale_qty`, `use_config_max_sale_qty`, `is_in_stock`, `low_stock_date`, `notify_stock_qty`, `use_config_notify_stock_qty`, `manage_stock`, `use_config_manage_stock`, `stock_status_changed_auto`, `use_config_qty_increments`, `qty_increments`, `use_config_enable_qty_inc`, `enable_qty_increments`, `is_decimal_divided`, `website_id`) VALUES ";
    $query .= "($last_id, 1, 0.0000, 0.0000, 1, 0, 0, 1, 1.0000, 1, 0.0000, 1, 1, NULL, NULL, 1, 0, 1, 0, 1, 0.0000, 1, 0, 0, 0);";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
    $query .= "(195, 0, $last_id, 0), ";
    $query .= "(188, 0, $last_id, 1), ";
    $query .= "(184, 0, $last_id, 1), ";
    $query .= "(181, 0, $last_id, 1), ";
    $query .= "(159, 0, $last_id, 1), ";
    $query .= "(158, 0, $last_id, $seller_id), ";
    $query .= "(127, 0, $last_id, 2), ";
    $query .= "(99, 0, $last_id, 4), ";
    $query .= "(97, 0, $last_id, 1);";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
    $query .= "('$model_id', 0, $last_id, $haine_model);";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
    $query .= "($last_id, $model_id, 1, $haine_model, $last_id), ";
    $query .= "($last_id, $model_id, 4, $haine_model, $last_id), ";
    $query .= "($last_id, $model_id, 7, $haine_model, $last_id);";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
    $query .= "('$vandut_de_id', 0, $last_id, $vandut_de);";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
    $query .= "($last_id, $vandut_de_id, 1, $vandut_de, $last_id), ";
    $query .= "($last_id, $vandut_de_id, 4, $vandut_de, $last_id), ";
    $query .= "($last_id, $vandut_de_id, 7, $vandut_de, $last_id);";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
    $query .= "('$price_id', 0, $last_id, $price_nr);";
    $result = $conn->query($query);  

    $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
    $query .= "($last_id, $price_id, 1, $price_nr, $last_id), ";
    $query .= "($last_id, $price_id, 4, $price_nr, $last_id), ";
    $query .= "($last_id, $price_id, 7, $price_nr, $last_id);";
    $result = $conn->query($query);

    if($brand != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$brand_id', 0, $last_id, $brand);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $brand_id, 1, $brand, $last_id), ";
        $query .= "($last_id, $brand_id, 4, $brand, $last_id), ";
        $query .= "($last_id, $brand_id, 7, $brand, $last_id);";
        $result = $conn->query($query);
    }
    if($haine_material != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$material_id', 0, $last_id, $haine_material);";
        $result = $conn->query($query);
        
        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $material_id, 1, $haine_material, $last_id), ";
        $query .= "($last_id, $material_id, 4, $haine_material, $last_id), ";
        $query .= "($last_id, $material_id, 7, $haine_material, $last_id);";
        $result = $conn->query($query);
    }
    if($haine_model != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$model_id', 0, $last_id, $haine_model);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $model_id, 1, $haine_model, $last_id), ";
        $query .= "($last_id, $model_id, 4, $haine_model, $last_id), ";
        $query .= "($last_id, $model_id, 7, $haine_model, $last_id);";
        $result = $conn->query($query);
    }
    if($haine_stil != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$stil_id', 0, $last_id, $haine_stil);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $stil_id, 1, $haine_stil, $last_id), ";
        $query .= "($last_id, $stil_id, 4, $haine_stil, $last_id), ";
        $query .= "($last_id, $stil_id, 7, $haine_stil, $last_id);";
        $result = $conn->query($query);
    }
    if($haine_imprimeu != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$imprimeu_id', 0, $last_id, $haine_imprimeu);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $imprimeu_id, 1, $haine_imprimeu, $last_id), ";
        $query .= "($last_id, $imprimeu_id, 4, $haine_imprimeu, $last_id), ";
        $query .= "($last_id, $imprimeu_id, 7, $haine_imprimeu, $last_id);";
        $result = $conn->query($query);
    }
    if($haine_sezon != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$sezon_id', 0, $last_id, $haine_sezon);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $sezon_id, 1, $haine_sezon, $last_id), ";
        $query .= "($last_id, $sezon_id, 4, $haine_sezon, $last_id), ";
        $query .= "($last_id, $sezon_id, 7, $haine_sezon, $last_id);";
        $result = $conn->query($query);
    }
    if($haine_talie != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$talie_id', 0, $last_id, $haine_talie);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $talie_id, 1, $haine_talie, $last_id), ";
        $query .= "($last_id, $talie_id, 4, $haine_talie, $last_id), ";
        $query .= "($last_id, $talie_id, 7, $haine_talie, $last_id);";
        $result = $conn->query($query);
    }
    if($haine_maneca != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$maneca_id', 0, $last_id, $haine_maneca);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $maneca_id, 1, $haine_maneca, $last_id), ";
        $query .= "($last_id, $maneca_id, 4, $haine_maneca, $last_id), ";
        $query .= "($last_id, $maneca_id, 7, $haine_maneca, $last_id);";
        $result = $conn->query($query);
    }
    if($haine_unicat != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$unicat_id', 0, $last_id, $haine_unicat);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $unicat_id, 1, $haine_unicat, $last_id), ";
        $query .= "($last_id, $unicat_id, 4, $haine_unicat, $last_id), ";
        $query .= "($last_id, $unicat_id, 7, $haine_unicat, $last_id);";
        $result = $conn->query($query);
    }
    if($haine_guler != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$guler_id', 0, $last_id, $haine_guler);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $guler_id, 1, $haine_guler, $last_id), ";
        $query .= "($last_id, $guler_id, 4, $haine_guler, $last_id), ";
        $query .= "($last_id, $guler_id, 7, $haine_guler, $last_id);";
        $result = $conn->query($query);
    }
    if($haine_gluga != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$gluga_id', 0, $last_id, $haine_gluga);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $gluga_id, 1, $haine_gluga, $last_id), ";
        $query .= "($last_id, $gluga_id, 4, $haine_gluga, $last_id), ";
        $query .= "($last_id, $gluga_id, 7, $haine_gluga, $last_id);";
        $result = $conn->query($query);
    }
    if($haine_interior != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$interior_id', 0, $last_id, $haine_interior);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $interior_id, 1, $haine_interior, $last_id), ";
        $query .= "($last_id, $interior_id, 4, $haine_interior, $last_id), ";
        $query .= "($last_id, $interior_id, 7, $haine_interior, $last_id);";
        $result = $conn->query($query);
    }
    if($haine_copii_material != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$material_id', 0, $last_id, $haine_copii_material);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $material_id, 1, $haine_copii_material, $last_id), ";
        $query .= "($last_id, $material_id, 4, $haine_copii_material, $last_id), ";
        $query .= "($last_id, $material_id, 7, $haine_copii_material, $last_id);";
        $result = $conn->query($query);
    }
    if($haine_copii_personaje != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query = "('$personaje_id', 0, $last_id, $haine_copii_personaje);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $personaje_id, 1, $haine_copii_personaje, $last_id), ";
        $query .= "($last_id, $personaje_id, 4, $haine_copii_personaje, $last_id), ";
        $query .= "($last_id, $personaje_id, 7, $haine_copii_personaje, $last_id);";
        $result = $conn->query($query);
    }
    if($haine_copii_imprimeu != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$imprimeu_id', 0, $last_id, $haine_copii_imprimeu);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $imprimeu_id, 1, $haine_copii_imprimeu, $last_id), ";
        $query .= "($last_id, $imprimeu_id, 4, $haine_copii_imprimeu, $last_id), ";
        $query .= "($last_id, $imprimeu_id, 7, $haine_copii_imprimeu, $last_id);";
        $result = $conn->query($query);
    }
    if($haine_copii_ocazie != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$ocazie_id', 0, $last_id, $haine_copii_ocazie);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $ocazie_id, 1, $haine_copii_ocazie, $last_id), ";
        $query .= "($last_id, $ocazie_id, 4, $haine_copii_ocazie, $last_id), ";
        $query .= "($last_id, $ocazie_id, 7, $haine_copii_ocazie, $last_id);";
        $result = $conn->query($query);
    }
    if($haine_copii_sarbatoare != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$sarbatoare_id', 0, $last_id, $haine_copii_sarbatoare);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $sarbatoare_id, 1, $haine_copii_sarbatoare, $last_id), ";
        $query .= "($last_id, $sarbatoare_id, 4, $haine_copii_sarbatoare, $last_id), ";
        $query .= "($last_id, $sarbatoare_id, 7, $haine_copii_sarbatoare, $last_id);";
        $result = $conn->query($query);
    }
    if($haine_copii_marime != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$marime_id', 0, $last_id, $haine_copii_marime);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $marime_id, 1, $haine_copii_marime, $last_id), ";
        $query .= "($last_id, $marime_id, 4, $haine_copii_marime, $last_id), ";
        $query .= "($last_id, $marime_id, 7, $haine_copii_marime, $last_id);";
        $result = $conn->query($query);
    }
    if($chiloti_material != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$material_id', 0, $last_id, $chiloti_material);";
        $result = $conn->query($query);
        
        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $material_id, 1, $chiloti_material, $last_id), ";
        $query .= "($last_id, $material_id, 4, $chiloti_material, $last_id), ";
        $query .= "($last_id, $material_id, 7, $chiloti_material, $last_id);";
        $result = $conn->query($query);
    }
    if($chiloti_talie != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$talie_id', 0, $last_id, $chiloti_talie);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $talie_id, 1, $chiloti_talie, $last_id), ";
        $query .= "($last_id, $talie_id, 4, $chiloti_talie, $last_id), ";
        $query .= "($last_id, $talie_id, 7, $chiloti_talie, $last_id);";
        $result = $conn->query($query);
    }
    if($chiloti_model != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$model_id', 0, $last_id, $chiloti_model);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $model_id, 1, $chiloti_model, $last_id), ";
        $query .= "($last_id, $model_id, 4, $chiloti_model, $last_id), ";
        $query .= "($last_id, $model_id, 7, $chiloti_model, $last_id);";
        $result = $conn->query($query);
    }
    if($chiloti_stil != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$stil_id', 0, $last_id, $chiloti_stil);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $stil_id, 1, $chiloti_stil, $last_id), ";
        $query .= "($last_id, $stil_id, 4, $chiloti_stil, $last_id), ";
        $query .= "($last_id, $stil_id, 7, $chiloti_stil, $last_id);";
        $result = $conn->query($query);
    }
    if($sutiene_material != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$material_id', 0, $last_id, $sutiene_material);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $material_id, 1, $sutiene_material, $last_id), ";
        $query .= "($last_id, $material_id, 4, $sutiene_material, $last_id), ";
        $query .= "($last_id, $material_id, 7, $sutiene_material, $last_id);";
        $result = $conn->query($query);
    }
    if($sutiene_tip_cupa != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$tip_cupa_id', 0, $last_id, $sutiene_tip_cupa);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $tip_cupa_id, 1, $sutiene_tip_cupa, $last_id), ";
        $query .= "($last_id, $tip_cupa_id, 4, $sutiene_tip_cupa, $last_id), ";
        $query .= "($last_id, $tip_cupa_id, 7, $sutiene_tip_cupa, $last_id);";
        $result = $conn->query($query);
    }
    if($sutiene_sarma_cupa != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$sarma_cupa_id', 0, $last_id, $sutiene_sarma_cupa);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $sarma_cupa_id, 1, $sutiene_sarma_cupa, $last_id), ";
        $query .= "($last_id, $sarma_cupa_id, 4, $sutiene_sarma_cupa, $last_id), ";
        $query .= "($last_id, $sarma_cupa_id, 7, $sutiene_sarma_cupa, $last_id);";
        $result = $conn->query($query);
    }
    if($sutiene_burete_cupa != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$burete_cupa_id', 0, $last_id, $sutiene_burete_cupa);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $burete_cupa_id, 1, $sutiene_burete_cupa, $last_id), ";
        $query .= "($last_id, $burete_cupa_id, 4, $sutiene_burete_cupa, $last_id), ";
        $query .= "($last_id, $burete_cupa_id, 7, $sutiene_burete_cupa, $last_id);";
        $result = $conn->query($query);
    }
    if($sutiene_bretele != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$bretele_id', 0, $last_id, $sutiene_bretele);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $bretele_id, 1, $sutiene_bretele, $last_id), ";
        $query .= "($last_id, $bretele_id, 4, $sutiene_bretele, $last_id), ";
        $query .= "($last_id, $bretele_id, 7, $sutiene_bretele, $last_id);";
        $result = $conn->query($query);
    }
    if($sutiene_speciale != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$speciale_id', 0, $last_id, $sutiene_speciale);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $speciale_id, 1, $sutiene_speciale, $last_id), ";
        $query .= "($last_id, $speciale_id, 4, $sutiene_speciale, $last_id), ";
        $query .= "($last_id, $speciale_id, 7, $sutiene_speciale, $last_id);";
        $result = $conn->query($query);
    }
    if($sutiene_stil != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$stil_id', 0, $last_id, $sutiene_stil);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $stil_id, 1, $sutiene_stil, $last_id), ";
        $query .= "($last_id, $stil_id, 4, $sutiene_stil, $last_id), ";
        $query .= "($last_id, $stil_id, 7, $sutiene_stil, $last_id);";
        $result = $conn->query($query);
    }
    if($lenjerie_barbati_material != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$material_id', 0, $last_id, $lenjerie_barbati_material);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $material_id, 1, $lenjerie_barbati_material, $last_id), ";
        $query .= "($last_id, $material_id, 4, $lenjerie_barbati_material, $last_id), ";
        $query .= "($last_id, $material_id, 7, $lenjerie_barbati_material, $last_id);";
        $result = $conn->query($query);
    }
    if($lenjerie_barbati_stil != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$stil_id', 0, $last_id, $lenjerie_barbati_stil);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $stil_id, 1, $lenjerie_barbati_stil, $last_id), ";
        $query .= "($last_id, $stil_id, 4, $lenjerie_barbati_stil, $last_id), ";
        $query .= "($last_id, $stil_id, 7, $lenjerie_barbati_stil, $last_id);";
        $result = $conn->query($query);
    }
    if($incaltaminte_material != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$material_id', 0, $last_id, $incaltaminte_material);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $material_id, 1, $incaltaminte_material, $last_id), ";
        $query .= "($last_id, $material_id, 4, $incaltaminte_material, $last_id), ";
        $query .= "($last_id, $material_id, 7, $incaltaminte_material, $last_id);";
        $result = $conn->query($query);
    }
    if($incaltaminte_stil != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$stil_id', 0, $last_id, $incaltaminte_stil);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $stil_id, 1, $incaltaminte_stil, $last_id), ";
        $query .= "($last_id, $stil_id, 4, $incaltaminte_stil, $last_id), ";
        $query .= "($last_id, $stil_id, 7, $incaltaminte_stil, $last_id);";
        $result = $conn->query($query);
    }
    if($incaltaminte_tip_inchidere != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$tip_inchidere_id', 0, $last_id, $incaltaminte_tip_inchidere);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $tip_inchidere_id, 1, $incaltaminte_tip_inchidere, $last_id), ";
        $query .= "($last_id, $tip_inchidere_id, 4, $incaltaminte_tip_inchidere, $last_id), ";
        $query .= "($last_id, $tip_inchidere_id, 7, $incaltaminte_tip_inchidere, $last_id);";
        $result = $conn->query($query);
    }
    if($incaltaminte_tip_varf != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$tip_varf_id', 0, $last_id, $incaltaminte_tip_varf);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $tip_varf_id, 1, $incaltaminte_tip_varf, $last_id), ";
        $query .= "($last_id, $tip_varf_id, 4, $incaltaminte_tip_varf, $last_id), ";
        $query .= "($last_id, $tip_varf_id, 7, $incaltaminte_tip_varf, $last_id);";
        $result = $conn->query($query);
    }
    if($incaltaminte_tip_toc != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$tip_toc_id', 0, $last_id, $incaltaminte_tip_toc);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $tip_toc_id, 1, $incaltaminte_tip_toc, $last_id), ";
        $query .= "($last_id, $tip_toc_id, 4, $incaltaminte_tip_toc, $last_id), ";
        $query .= "($last_id, $tip_toc_id, 7, $incaltaminte_tip_toc, $last_id);";
        $result = $conn->query($query);
    }
    if($incaltaminte_interior != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$interior_id', 0, $last_id, $incaltaminte_interior);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $interior_id, 1, $incaltaminte_interior, $last_id), ";
        $query .= "($last_id, $interior_id, 4, $incaltaminte_interior, $last_id), ";
        $query .= "($last_id, $interior_id, 7, $incaltaminte_interior, $last_id);";
        $result = $conn->query($query);
    }
    if($incaltaminte_inaltime_toc != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$inaltime_toc_id', 0, $last_id, $incaltaminte_inaltime_toc);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $inaltime_toc_id, 1, $incaltaminte_inaltime_toc, $last_id), ";
        $query .= "($last_id, $inaltime_toc_id, 4, $incaltaminte_inaltime_toc, $last_id), ";
        $query .= "($last_id, $inaltime_toc_id, 7, $incaltaminte_inaltime_toc, $last_id);";
        $result = $conn->query($query);
    }
    if($incaltaminte_imprimeu != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$imprimeu_id', 0, $last_id, $incaltaminte_imprimeu);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $imprimeu_id, 1, $incaltaminte_imprimeu, $last_id), ";
        $query .= "($last_id, $imprimeu_id, 4, $incaltaminte_imprimeu, $last_id), ";
        $query .= "($last_id, $imprimeu_id, 7, $incaltaminte_imprimeu, $last_id);";
        $result = $conn->query($query);
    }
    if($incaltaminte_sezon != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$sezon_id', 0, $last_id, $incaltaminte_sezon);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $sezon_id, 1, $incaltaminte_sezon, $last_id), ";
        $query .= "($last_id, $sezon_id, 4, $incaltaminte_sezon, $last_id), ";
        $query .= "($last_id, $sezon_id, 7, $incaltaminte_sezon, $last_id);";
        $result = $conn->query($query);
    }
    if($incaltaminte_unicat != '') {
        $query = "INSERT INTO `mgkf_catalog_product_entity_int` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
        $query .= "('$unicat_id', 0, $last_id, $incaltaminte_unicat);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
        $query .= "($last_id, $unicat_id, 1, $incaltaminte_unicat, $last_id), ";
        $query .= "($last_id, $unicat_id, 4, $incaltaminte_unicat, $last_id), ";
        $query .= "($last_id, $unicat_id, 7, $incaltaminte_unicat, $last_id);";
        $result = $conn->query($query);
    }

    $query = "INSERT INTO `mgkf_catalog_category_product_index_store1` (`category_id`, `product_id`, `position`, `is_parent`, `store_id`, `visibility`) VALUES ";
    $query .= "(2, '$last_id', 10000, 0, 1, 4), ";
    $query .= "($category, $last_id, 0, 1, 1, 4), ";
    $query .= "($subcategory, $last_id, 0, 1, 1, 4);";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_catalog_category_product_index_store4` (`category_id`, `product_id`, `position`, `is_parent`, `store_id`, `visibility`) VALUES ";
    $query .= "(2, '$last_id', 10000, 0, 4, 4), ";
    $query .= "($category, $last_id, 0, 1, 4, 4), ";
    $query .= "($subcategory, $last_id, 0, 1, 4, 4);";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_catalog_product_index_price` (`entity_id`, `customer_group_id`, `website_id`, `tax_class_id`, `price`, `final_price`, `min_price`, `max_price`, `tier_price`) VALUES ";
    $query .= "($last_id, 0, 1, 0, '0.0000', '0.0000', '$price', '$price', NULL), ";
    $query .= "($last_id, 1, 1, 0, '0.0000', '0.0000', '$price', '$price', NULL), ";
    $query .= "($last_id, 2, 1, 0, '0.0000', '0.0000', '$price', '$price', NULL), ";
    $query .= "($last_id, 3, 1, 0, '0.0000', '0.0000', '$price', '$price', NULL), ";
    $query .= "($last_id, 4, 1, 0, '0.0000', '0.0000', '$price', '$price', NULL);";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_email_catalog` (`product_id`, `imported`, `modified`) VALUES ";
    $query .= "($last_id, NULL, NULL);";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_catalog_product_index_eav` (`entity_id`, `attribute_id`, `store_id`, `value`, `source_id`) VALUES ";
    $query .= "($last_id, 99, 1, 4, $last_id), ";
    $query .= "($last_id, 99, 4, 4, $last_id), ";
    $query .= "($last_id, 99, 7, 4, $last_id)";
    $result = $conn->query($query);

    $query = "UPDATE mgkf_catalog_product_index_eav ";
    $query .= "SET entity_id = $last_id WHERE entity_id = 0";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_catalog_product_website` (`product_id`, `website_id`) VALUES ";
    $query .= "($last_id, 1)";
    $result = $conn->query($query);

    if(isset($_POST['color'])) {
        $query = "INSERT INTO `mgkf_catalog_product_super_attribute` (`product_id`, `attribute_id`, `position`) VALUES ";
        $query .= "($last_id, 93, 0), ";
        $query .= "($last_id, 136, 1);";
        $result = $conn->query($query);
    }
    else {
        $query = "INSERT INTO `mgkf_catalog_product_super_attribute` (`product_id`, `attribute_id`, `position`) VALUES ";
        $query .= "($last_id, 136, 1);";
        $result = $conn->query($query);
    }    

    $query = "INSERT INTO `mgkf_catalog_product_entity_varchar` (`attribute_id`, `store_id`, `entity_id`, `value`) VALUES ";
    $query .= "(89, 0, $last_id, '/$dir_1/$dir_2/$filename'), ";
    $query .= "(88, 0, $last_id, '/$dir_1/$dir_2/$filename'), ";
    $query .= "(87, 0, $last_id, '/$dir_1/$dir_2/$filename'), ";
    $query .= "(154, 0, $last_id, '/$dir_1/$dir_2/$filename');";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_catalog_product_entity_media_gallery` (`attribute_id`, `value`, `media_type`, `disabled`) VALUES ";
    $query .= "(90, '/$dir_1/$dir_2/$filename', 'image', 0);";
    $result = $conn->query($query);
    $last_inserted_id = $conn->insert_id;

    $query = "INSERT INTO `mgkf_catalog_product_entity_media_gallery_value` (`value_id`, `store_id`, `entity_id`, `label`, `position`, `disabled`) VALUES ";
    $query .= "($last_inserted_id, 0, $last_id, NULL, 1, 0);";
    $result = $conn->query($query);

    $query = "INSERT INTO `mgkf_catalog_product_entity_media_gallery_value_to_entity` (`value_id`, `entity_id`) VALUES ";
    $query .= "($last_inserted_id, $last_id);";
    $result = $conn->query($query);
}

for($k=count($product)+1; $k--;) {
    $id = $last_id - $k;

    if($id != $last_id) {  
        $query = "INSERT INTO `mgkf_catalog_product_relation` (`parent_id`, `child_id`) VALUES ";
        $query .= "($last_id, $id);";
        $result = $conn->query($query);

        $query = "INSERT INTO `mgkf_catalog_product_super_link` (`product_id`, `parent_id`) VALUES ";
        $query .= "($id, $last_id);";
        $result = $conn->query($query);
    }  
}
?>
<script>
var lang = '<?php echo $lang; ?>';
window.location.href = lang + "/marketplace/product/manage/";
</script>