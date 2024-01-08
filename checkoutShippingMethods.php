<?php
if (file_exists("conf/conf.php"))
    @include 'conf/conf.php';

$conn = new mysqli($conf->db->host, $conf->db->user, $conf->db->pass, $conf->db->name);

$address = $_POST['address'];
$sellerIds = $_POST['seller_ids'];
$customerId = $_POST['customer_id'];
$remote_ip = $_SERVER['REMOTE_ADDR'];
?>
<div id="checkout-shipping-method-load" style="text-align:center;">
    <?php
    $i = 0;
    $query_transport = "SELECT mgkf_marketplace_seller.store_name, mgkf_marketplace_seller.customer_id as seller_id, mgkf_marketplace_transports.country, FORMAT(mgkf_marketplace_transports.price * 4.726883, 2) as price, FORMAT(mgkf_marketplace_transports.price_free_transport * 4.726883, 2) as price_free_transport, mgkf_marketplace_transports.courier, mgkf_marketplace_transports.average_delivery_time ";
    $query_transport .= "FROM mgkf_marketplace_seller, mgkf_marketplace_transports ";
    $query_transport .= "WHERE mgkf_marketplace_seller.customer_id = mgkf_marketplace_transports.seller_id ";
    $query_transport .= "AND mgkf_marketplace_transports.seller_id IN (".$sellerIds.") ";
    $query_transport .= "ORDER BY mgkf_marketplace_transports.seller_id ASC";
    $result_transport = $conn->query($query_transport);
    $num_rows = $result_transport->num_rows;
    while($row_transport = $result_transport->fetch_assoc()) {
        $store_name = $row_transport['store_name'];
        $sellerId = $row_transport['seller_id'];
        $country = $row_transport['country'];
        $price = $row_transport['price'];
        $price_free_transport = $row_transport['price_free_transport'];
        $price_free_transport = str_replace(",", "", $price_free_transport);
        $courier = $row_transport['courier'];
        $delivery_time = $row_transport['average_delivery_time'];

        $query_total = "SELECT SUM(mgkf_quote_item.row_total) as seller_total ";
        $query_total .= "FROM mgkf_quote_item, mgkf_quote, mgkf_catalog_product_entity_int ";
        $query_total .= "WHERE mgkf_quote_item.quote_id = mgkf_quote.entity_id ";
        $query_total .= "AND mgkf_catalog_product_entity_int.entity_id = mgkf_quote_item.product_id ";
        $query_total .= "AND mgkf_quote_item.row_total != '' ";
        $query_total .= "AND mgkf_quote.is_active = 1 ";
        $query_total .= "AND mgkf_catalog_product_entity_int.attribute_id = 158 ";
        if($customerId != '') {
            $query_total .= "AND mgkf_quote.customer_id = '".$customerId."' ";
        }
        $query_total .= "AND mgkf_catalog_product_entity_int.value = '".$sellerId."'";
        $result_total = $conn->query($query_total);
        $row_total = $result_total->fetch_assoc();
        $total = number_format($row_total['seller_total'], 2, ".", "");

        if($customerId != '') {
            $query = "SELECT total FROM mgkf_cart_total WHERE customer_id = '".$customerId."'";
        }
        else {
            $query = "SELECT total FROM mgkf_cart_total WHERE remote_ip = '".$remote_ip."'";
        }
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $total = number_format($row['total'], 2, ".", "");

        if(strpos($address, $country) !== false) {

            $query_check = "SELECT COUNT(id) as count ";
            $query_check .= "FROM mgkf_marketplace_transports ";
            $query_check .= "WHERE country = '".$country."' ";
            $query_check .= "AND seller_id = '".$sellerId."'";
            $result_check = $conn->query($query_check);
            $row_check = $result_check->fetch_assoc();
            $count = $row_check['count'];

        ?>
            <div class="align-items-center m-0 mt-3 rounded row" id="transport_<?php echo $i; ?>">
                <div class="col-12">
                    <p class="seller-store-name"><?php echo $store_name; ?></p>
                    <span style="display:none;" id="transport_country_<?php echo $i; ?>">
                        <?php echo $country; ?>
                    </span>
                </div>
                <div id="shipping-method-<?php echo $i; ?>" <?php if($count > 1) { ?> onclick="set_active_shipping_method(<?php echo $i; ?>);" <?php } ?> class="col-12 border rounded store-shipping-info <?php if($count == 1) { echo 'active'; }?>">
                    <div class="col-12 col-md-11">
                        <?php
                        $shippingPrice = 0;
                        if($total >= $price_free_transport) {
                            $shippingPrice = "0.00";
                        }
                        else {
                            $shippingPrice = $price;
                        }
                        ?>
                        <?php echo $shippingPrice . ' Ron prin ' . $courier . ' în ' . $delivery_time . ' zile lucrătoare'; ?>
                        <input type="hidden" id="price-for-shipping-<?php echo $i; ?>" value="<?php echo $shippingPrice; ?>" />
                    </div>
                    <div class="col-12 col-md-1">
                        <input class="form-check-input shipping-method" type="checkbox" id="selected-shipping-method-<?php echo $i; ?>" value="option<?php echo $i; ?>" <?php if($count == 1) echo 'checked'; ?>>
                    </div>
                </div>
            </div>
        <?php
        }
        else {
            if($i == 0) {
            ?>
                <span id="no-shipping-methods">
                    Aceste produse nu se pot livra în <?php echo 'țara aceasta'; ?>
                </span>
                <script>
                document.getElementById("total-shipping").style.display = "none";
                document.getElementById("price-not-calculated").style.display = "block";
                var price_not_calculated = document.getElementById("price-not-calculated").innerText;
                document.getElementById("grand-total").innerText = "";
                document.getElementById("grand-total").innerText = price_not_calculated;
                document.getElementById("place-order-btn").style.pointerEvents = "none";
                </script>
            <?php
            }
        }
        $i++;
    }
    ?>
    <input type="hidden" id="calculated-shipping-price" value="" />
</div>
<script>
if(document.getElementById("shipping-method-4")) {
    document.getElementById("shipping-method-4").classList.add("active");
    document.getElementById("selected-shipping-method-4").setAttribute("checked", "");
}

if(document.getElementsByClassName("seller-store-name")[0]) {
    if(document.getElementsByClassName("seller-store-name")[0].innerText == 'developer store') {
        document.getElementById("shipping-method-0").classList.add("active");
        document.getElementById("selected-shipping-method-0").setAttribute("checked", "");
    }
}

function set_active_shipping_method(k) {
    if(k == 0) {
        document.getElementById("shipping-method-1").classList.remove("active");
        document.getElementById("selected-shipping-method-1").removeAttribute("checked");
        document.getElementById("shipping-method-0").classList.add("active");
        document.getElementById("selected-shipping-method-0").setAttribute("checked", "");
    }
    if(k == 1) {
        document.getElementById("shipping-method-0").classList.remove("active");
        document.getElementById("selected-shipping-method-0").removeAttribute("checked");
        document.getElementById("shipping-method-1").classList.add("active");
        document.getElementById("selected-shipping-method-1").setAttribute("checked", "");
    }
    if(k == 3) {
        document.getElementById("shipping-method-4").classList.remove("active");
        document.getElementById("selected-shipping-method-4").removeAttribute("checked");
        document.getElementById("shipping-method-3").classList.add("active");
        document.getElementById("selected-shipping-method-3").setAttribute("checked", "");
    }
    if(k == 4) {
        document.getElementById("shipping-method-3").classList.remove("active");
        document.getElementById("selected-shipping-method-3").removeAttribute("checked");
        document.getElementById("shipping-method-4").classList.add("active");
        document.getElementById("selected-shipping-method-4").setAttribute("checked", "");
    }

    var array = [];
    for(var i=0; i<10; i++) {
        if(document.getElementById("shipping-method-"+i)) {
            if(document.getElementById("shipping-method-"+i).classList.contains("active")) {
                var shipping_price = document.getElementById("price-for-shipping-"+i).value;
                array.push(shipping_price);
            }
        }
    }
    var shipping_price = eval(array.join('+')).toFixed(2);
    var currency = document.getElementById("product-total-currency").value;
    document.getElementById("calculated-shipping-price").value = shipping_price;
    document.getElementById("price-not-calculated").style.display = "none";
    document.getElementById("total-shipping").innerText = shipping_price + " " + currency;
    document.getElementById("total-shipping").style.display = "block";
    var total = (Number(shipping_price) + Number(product_total)).toFixed(2);
    document.getElementById("grand-total").innerText = total + " " + currency;
    document.getElementById("place-order-btn").style.pointerEvents = "auto";
}

var main = document.getElementById("checkout-shipping-method-load");
var seller_store_name = main.getElementsByClassName("seller-store-name");
for(var j=0; j<seller_store_name.length; j++) {
    seller_store_name[j].setAttribute("id", "seller-store-name-"+j);
}
if(seller_store_name.length > 2) {
    document.getElementById("seller-store-name-2").style.display = "none";
}
else {
    if(seller_store_name.length > 1) {
        if(document.getElementById("seller-store-name-0").innerText == document.getElementById("seller-store-name-1").innerText) {
            document.getElementById("seller-store-name-1").style.display = "none";
        }
    }
}

var array = [];
for(var i=0; i<10; i++) {
    if(document.getElementById("shipping-method-"+i)) {
        if(document.getElementById("shipping-method-"+i).classList.contains("active")) {
            var shipping_price = document.getElementById("price-for-shipping-"+i).value;
            array.push(shipping_price);
        }
    }
}
var shipping_price = eval(array.join('+')).toFixed(2);
var currency = document.getElementById("product-total-currency").value;
var product_total = document.getElementById("product-total-amount").value;
document.getElementById("calculated-shipping-price").value = shipping_price;
document.getElementById("price-not-calculated").style.display = "none";
document.getElementById("total-shipping").innerText = shipping_price + " " + currency;
document.getElementById("total-shipping").style.display = "block";
var total = (Number(shipping_price) + Number(product_total)).toFixed(2);
document.getElementById("grand-total").innerText = total + " " + currency;
document.getElementById("place-order-btn").style.pointerEvents = "auto";
</script>
