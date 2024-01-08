<?php 
if (file_exists("conf/conf.php")) 
    @include 'conf/conf.php';

$conn = new mysqli($conf->db->host, $conf->db->user, $conf->db->pass, $conf->db->name);

$remote_ip = $_SERVER['REMOTE_ADDR'];

if(isset($_POST['customer_id'])) {
    $query = "SELECT ";
}
else {
    $query = "SELECT mgkf_quote.customer_id, ";
}
$query .= "sum(row_total) as total, mgkf_quote.quote_currency_code as currency ";
$query .= "FROM mgkf_quote_item, mgkf_quote, mgkf_catalog_product_entity_int ";
$query .= "WHERE mgkf_quote_item.quote_id = mgkf_quote.entity_id ";
$query .= "AND mgkf_catalog_product_entity_int.entity_id = mgkf_quote_item.product_id ";
$query .= "AND mgkf_quote_item.row_total != '' ";
$query .= "AND mgkf_quote.is_active = 1 ";
$query .= "AND mgkf_catalog_product_entity_int.attribute_id = 158 ";
if($_POST['customer_id'] != '') {
    $customerId = $_POST['customer_id'];
    $query .= "AND mgkf_quote.customer_id = '".$customerId."' ";
}
else {
    $query .= "AND mgkf_quote.customer_id IS NULL ";
    $query .= "AND mgkf_quote.remote_ip = '".$remote_ip."'";
}
$result = $conn->query($query);
$row = $result->fetch_assoc();
if(isset($_POST['customer_id'])) {
    $customerId = $_POST['customer_id'];
}
$total = number_format($row['total'], 2);
$currency = ucfirst(strtolower($row['currency']));
?>
<div id="additional-info" class="d-none">
    <?php if($customerId != '') { ?>
        <select class="form-control form-control-sm mb-2" name="shipping" id="shipping" onchange="shipping_settings();">
            <?php
            $query_address = "SELECT country_id, city ";
            $query_address .= "FROM mgkf_customer_address_entity ";
            $query_address .= "WHERE parent_id = '".$customerId."'";
            $result_address = $conn->query($query_address);
            while($row_address = $result_address->fetch_assoc()) {
                $country = $row_address['country_id'];
                $city = $row_address['city'];
                if($country == 'US') $country = 'United States';
                if($country == 'RO') $country = 'Romania';
                if($country == 'DE') $country = 'Germany';

                $query_transports = "SELECT store_name, mgkf_marketplace_transports.seller_id, mgkf_marketplace_transports.country, mgkf_marketplace_transports.price, mgkf_marketplace_transports.price_free_transport ";
                $query_transports .= "FROM mgkf_marketplace_seller, mgkf_marketplace_transports ";
                $query_transports .= "WHERE mgkf_marketplace_seller.customer_id = mgkf_marketplace_transports.seller_id ";
                $query_transports .= "AND mgkf_marketplace_seller.country = '".$country."'";
            ?>
                <option><?php echo $city . ', &nbsp;' . $country; ?></option>
            <?php
            }
            ?>
        </select>
    <?php 
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
        if($ip != '127.0.0.1') {
            $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));
            $country = $details->country;
            
            $query = "SELECT country ";
            $query .= "FROM mgkf_marketplace_transports ";
            $query .= "WHERE iso = '".$country."' ";
            $query .= "LIMIT 0,1";
            $result = $conn->query($query);
            if($result->num_rows > 0) { ?>
                <select class="form-control form-control-sm mb-2" name="shipping" id="shipping" onchange="shipping_settings();">
                <?php
                while($row = $result->fetch_assoc()) {
                    $country = $row['country'];
                ?>
                    <option><?php echo $country; ?></option>
                <?php
                }
                ?>
                </select>
                <?php
            }
            else {
                $names = json_decode(file_get_contents("http://country.io/names.json"), true);
                echo 'Produsele acestea nu se pot livra în ' . $names[$country];
            }
        }
    }
    ?>
    <div class="row mb-2">
        <div class="col-4 font-weight-bold" scope="row">
            Cost produse
        </div>
        <div class="amount col-5">
            <span class="price">
                <span id="product_total"></span>
            </span>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4 font-weight-bold" scope="row">
            Cost livrare
        </div>
        <div class="amount col-5">
            <span class="price">
                <?php if($customerId != '') { ?>
                    <span id="shipping_total"></span>
                <?php 
                }
                else { 
                    if($result->num_rows > 0) {
                    ?>
                        <span id="shipping_total"></span>
                    <?php
                    }
                    else {
                    ?>
                        <span id="shipping_not_calculated">
                            Nu este calculat
                        </span>
                    <?php
                    }
                }
                ?>
            <span>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-4 font-weight-bold" scope="row">
        Total
    </div>
    <div class="amount col-5">
        <span class="price">
            <span id="grand_total"></span>
        </span>
    </div>
    <div class="col-3 text-right">
        <button onclick="show_summary();" id="show-info" class="border-0 text-white bg-black btn-sm btn-block">
            Detalii
        </button>
    </div>
</div>
<div class="actions">
    <a id="place-order-btn" class="action primary checkout border-0 m-0 btn-block" href="/ro/checkout/">
        <span>
            Finalizează comanda
        </span>
    </a>
</div>
<script>
if(document.getElementById("block-content-block-footer")) {
    document.getElementById("block-content-block-footer").style.display = "block";
}
</script>
<script src="/minicart.js"></script>