var clicked = false;
var count_products = document.getElementById("mini-cart").getElementsByTagName("li").length;
if(count_products == 0) {
    document.getElementById("block-content-block-footer").style.display = "none";
    if(document.getElementsByClassName("product_price")[0]) {
        document.getElementById("product_total").innerText = document.getElementsByClassName("product_price")[0].innerText;
        document.getElementById("grand_total").innerText = document.getElementsByClassName("product_price")[0].innerText;
    }
}
if(count_products == 1) {
    document.getElementById("no-items-block").style.display = "none";
    if(document.getElementById("product_price_2_0")) {
        document.getElementById("product_total").innerText = document.getElementById("product_price_2_0").innerText;
    }
    if(document.getElementById("product_price_7_0")) {
        document.getElementById("product_total").innerText = document.getElementById("product_price_7_0").innerText;
    }
    document.getElementById("grand_total").innerText = document.getElementsByClassName("product_price")[0].innerText;
    changed_qty(0);
}
else if(count_products == 2) {
    document.getElementById("no-items-block").style.display = "none";
    if(document.getElementById("product_price_2_0")) {
        var first = document.getElementById("product_price_2_0").innerHTML;
    }
    if(document.getElementById("product_price_7_0")) {
        var first = document.getElementById("product_price_7_0").innerHTML;
    }
    if(document.getElementById("product_price_2_1")) {
        var second = document.getElementById("product_price_2_1").innerHTML;
    }
    if(document.getElementById("product_price_7_1")) {
        var second = document.getElementById("product_price_7_1").innerHTML;
    }
    first = Number(first);
    second = Number(second);
    var total = first + second;
    document.getElementById("product_total").innerHTML = total.toFixed(2);
    document.getElementById("grand_total").innerHTML = total.toFixed(2);
}
else if(count_products == 3) {
    document.getElementById("no-items-block").style.display = "none";
    if(document.getElementById("product_price_2_0")) {
        var first = document.getElementById("product_price_2_0").innerHTML;
    }
    if(document.getElementById("product_price_7_0")) {
        var first = document.getElementById("product_price_7_0").innerHTML;
    }
    if(document.getElementById("product_price_2_1")) {
        var second = document.getElementById("product_price_2_1").innerHTML;
    }
    if(document.getElementById("product_price_7_1")) {
        var second = document.getElementById("product_price_7_1").innerHTML;
    }
    if(document.getElementById("product_price_2_2")) {
        var third = document.getElementById("product_price_2_2").innerHTML;
    }
    if(document.getElementById("product_price_7_2")) {
        var third = document.getElementById("product_price_7_2").innerHTML;
    }
    first = Number(first);
    second = Number(second);
    third = Number(third);
    var total = first + second + third;
    document.getElementById("product_total").innerHTML = total.toFixed(2);
    document.getElementById("grand_total").innerHTML = total.toFixed(2);
}
else if(count_products == 4) {
    document.getElementById("no-items-block").style.display = "none";
    if(document.getElementById("product_price_2_0")) {
        var first = document.getElementById("product_price_2_0").innerHTML;
    }
    if(document.getElementById("product_price_7_0")) {
        var first = document.getElementById("product_price_7_0").innerHTML;
    }
    if(document.getElementById("product_price_2_1")) {
        var second = document.getElementById("product_price_2_1").innerHTML;
    }
    if(document.getElementById("product_price_7_1")) {
        var second = document.getElementById("product_price_7_1").innerHTML;
    }
    if(document.getElementById("product_price_2_2")) {
        var third = document.getElementById("product_price_2_2").innerHTML;
    }
    if(document.getElementById("product_price_7_2")) {
        var third = document.getElementById("product_price_7_2").innerHTML;
    }
    if(document.getElementById("product_price_2_3")) {
        var fourth = document.getElementById("product_price_2_3").innerHTML;
    }
    if(document.getElementById("product_price_7_3")) {
        var fourth = document.getElementById("product_price_7_3").innerHTML;
    }
    first = Number(first);
    second = Number(second);
    third = Number(third);
    fourth = Number(fourth);
    var total = first + second + third + fourth;
    document.getElementById("product_total").innerHTML = total.toFixed(2);
    document.getElementById("grand_total").innerHTML = total.toFixed(2);
}
else if(count_products == 5) {
    document.getElementById("no-items-block").style.display = "none";
    document.getElementById("no-items-block").style.display = "none";
    if(document.getElementById("product_price_2_0")) {
        var first = document.getElementById("product_price_2_0").innerHTML;
    }
    if(document.getElementById("product_price_7_0")) {
        var first = document.getElementById("product_price_7_0").innerHTML;
    }
    if(document.getElementById("product_price_2_1")) {
        var second = document.getElementById("product_price_2_1").innerHTML;
    }
    if(document.getElementById("product_price_7_1")) {
        var second = document.getElementById("product_price_7_1").innerHTML;
    }
    if(document.getElementById("product_price_2_2")) {
        var third = document.getElementById("product_price_2_2").innerHTML;
    }
    if(document.getElementById("product_price_7_2")) {
        var third = document.getElementById("product_price_7_2").innerHTML;
    }
    if(document.getElementById("product_price_2_3")) {
        var fourth = document.getElementById("product_price_2_3").innerHTML;
    }
    if(document.getElementById("product_price_7_3")) {
        var fourth = document.getElementById("product_price_7_3").innerHTML;
    }
    if(document.getElementById("product_price_2_4")) {
        var fifth = document.getElementById("product_price_2_4").innerHTML;
    }
    if(document.getElementById("product_price_7_4")) {
        var fifth = document.getElementById("product_price_7_4").innerHTML;
    }
    first = Number(first);
    second = Number(second);
    third = Number(third);
    fourth = Number(fourth);
    fifth = Number(fifth);
    var total = first + second + third + fourth + fifth;
    document.getElementById("product_total").innerHTML = total.toFixed(2);
    document.getElementById("grand_total").innerHTML = total.toFixed(2);
}
else if(count_products == 6) {
    document.getElementById("no-items-block").style.display = "none";
    if(document.getElementById("product_price_2_0")) {
        var first = document.getElementById("product_price_2_0").innerHTML;
    }
    if(document.getElementById("product_price_7_0")) {
        var first = document.getElementById("product_price_7_0").innerHTML;
    }
    if(document.getElementById("product_price_2_1")) {
        var second = document.getElementById("product_price_2_1").innerHTML;
    }
    if(document.getElementById("product_price_7_1")) {
        var second = document.getElementById("product_price_7_1").innerHTML;
    }
    if(document.getElementById("product_price_2_2")) {
        var third = document.getElementById("product_price_2_2").innerHTML;
    }
    if(document.getElementById("product_price_7_2")) {
        var third = document.getElementById("product_price_7_2").innerHTML;
    }
    if(document.getElementById("product_price_2_3")) {
        var fourth = document.getElementById("product_price_2_3").innerHTML;
    }
    if(document.getElementById("product_price_7_3")) {
        var fourth = document.getElementById("product_price_7_3").innerHTML;
    }
    if(document.getElementById("product_price_2_4")) {
        var fifth = document.getElementById("product_price_2_4").innerHTML;
    }
    if(document.getElementById("product_price_7_4")) {
        var fifth = document.getElementById("product_price_7_4").innerHTML;
    }
    if(document.getElementById("product_price_2_5")) {
        var sixth = document.getElementById("product_price_2_5").innerHTML;
    }
    if(document.getElementById("product_price_7_5")) {
        var sixth = document.getElementById("product_price_7_5").innerHTML;
    }
    first = Number(first);
    second = Number(second);
    third = Number(third);
    fourth = Number(fourth);
    fifth = Number(fifth);
    sixth = Number(sixth);
    var total = first + second + third + fourth + fifth + sixth;
    document.getElementById("product_total").innerHTML = total.toFixed(2);
    document.getElementById("grand_total").innerHTML = total.toFixed(2);
}
else {
    document.getElementById("no-items-block").style.display = "none";
    var first = document.getElementById("product_price_0").innerHTML;
    var second = document.getElementById("product_price_1").innerHTML;
    var third = document.getElementById("product_price_2").innerHTML;
    var fourth = document.getElementById("product_price_3").innerHTML;
    var fifth = document.getElementById("product_price_4").innerHTML;
    var sixth = document.getElementById("product_price_5").innerHTML;
    var seventh = document.getElementById("product_price_6").innerHTML;
    first = Number(first);
    second = Number(second);
    third = Number(third);
    fourth = Number(fourth);
    fifth = Number(fifth);
    sixth = Number(sixth);
    seventh = Number(seventh);
    var total = first + second + third + fourth + fifth + sixth + seventh;
    document.getElementById("product_total").innerHTML = total.toFixed(2);
    document.getElementById("grand_total").innerHTML = total.toFixed(2);
}

shipping_settings();
check_store_name();
function check_store_name() {
    for(var i=0; i<count_products; i++) {
        if(document.getElementById("store_name_"+i)) {
            var k = i + 1;
            var j = k + 1;
            var h = j + 1;
            var first = document.getElementById("store_name_"+i).innerText;
            if(document.getElementById("store_name_"+k)) {
                var second = document.getElementById("store_name_"+k).innerText;
            }
            if(document.getElementById("store_name_"+j)) {
                var third = document.getElementById("store_name_"+j).innerText;
            }
            if(document.getElementById("store_name_"+h)) {
                var fourth = document.getElementById("store_name_"+h).innerText;
            }
            if(first == second) {
                if(document.getElementById("store_name_"+k)) {
                    document.getElementById("store_name_"+k).innerText = '';
                }
            }
            if(first == third) {
                if(document.getElementById("store_name_"+j)) {
                    document.getElementById("store_name_"+j).innerText = '';
                }
            }
            if(first == fourth) {
                if(document.getElementById("store_name_"+h)) {
                    document.getElementById("store_name_"+h).innerText = '';
                }
            }
        }
    }
}

function show_details(k) {
    if(!clicked) {        
        document.getElementById("options_list_"+k).style.display = "block";
    }
    else {        
        document.getElementById("options_list_"+k).style.display = "none";
    }
    clicked = !clicked;
}

function changed_qty(k) {
    if(document.getElementById("select_qty_"+k)) {
        var select = document.getElementById("select_qty_"+k);
        var qty = select.options[select.selectedIndex].value;
    }

    var price_per_piece = document.getElementById("price_per_piece_"+k).value;
    var updated_price = qty * price_per_piece;

    for(var i=0;i<15;i++) {
        if(document.getElementById("product_price_"+i+"_"+k)) {
            document.getElementById("product_price_"+i+"_"+k).innerHTML = updated_price.toFixed(2);
        }
    }
    
    if(count_products == 1) {
        document.getElementById("product_total").innerHTML = updated_price.toFixed(2);
        document.getElementById("grand_total").innerHTML = updated_price.toFixed(2);
    }
    else if(count_products == 2) {
        if(document.getElementById("product_price_2_0")) {
            var first = document.getElementById("product_price_2_0").innerHTML;
        }
        if(document.getElementById("product_price_7_0")) {
            var first = document.getElementById("product_price_7_0").innerHTML;
        }
        if(document.getElementById("product_price_2_1")) {
            var second = document.getElementById("product_price_2_1").innerHTML;
        }
        if(document.getElementById("product_price_7_1")) {
            var second = document.getElementById("product_price_7_1").innerHTML;
        }
        first = Number(first);
        second = Number(second);
        var total = first + second;
        document.getElementById("product_total").innerHTML = total.toFixed(2);
        document.getElementById("grand_total").innerHTML = total.toFixed(2);
    }
    else if(count_products == 3) {
        if(document.getElementById("product_price_2_0")) {
            var first = document.getElementById("product_price_2_0").innerHTML;
        }
        if(document.getElementById("product_price_7_0")) {
            var first = document.getElementById("product_price_7_0").innerHTML;
        }
        if(document.getElementById("product_price_2_1")) {
            var second = document.getElementById("product_price_2_1").innerHTML;
        }
        if(document.getElementById("product_price_7_1")) {
            var second = document.getElementById("product_price_7_1").innerHTML;
        }
        if(document.getElementById("product_price_2_2")) {
            var third = document.getElementById("product_price_2_2").innerHTML;
        }
        if(document.getElementById("product_price_7_2")) {
            var third = document.getElementById("product_price_7_2").innerHTML;
        }
        first = Number(first);
        second = Number(second);
        third = Number(third);
        var total = first + second + third;
        document.getElementById("product_total").innerHTML = total.toFixed(2);
        document.getElementById("grand_total").innerHTML = total.toFixed(2);
    }
    else if(count_products == 4) {
        if(document.getElementById("product_price_2_0")) {
            var first = document.getElementById("product_price_2_0").innerHTML;
        }
        if(document.getElementById("product_price_7_0")) {
            var first = document.getElementById("product_price_7_0").innerHTML;
        }
        if(document.getElementById("product_price_2_1")) {
            var second = document.getElementById("product_price_2_1").innerHTML;
        }
        if(document.getElementById("product_price_7_1")) {
            var second = document.getElementById("product_price_7_1").innerHTML;
        }
        if(document.getElementById("product_price_2_2")) {
            var third = document.getElementById("product_price_2_2").innerHTML;
        }
        if(document.getElementById("product_price_7_2")) {
            var third = document.getElementById("product_price_7_2").innerHTML;
        }
        if(document.getElementById("product_price_2_3")) {
            var fourth = document.getElementById("product_price_2_3").innerHTML;
        }
        if(document.getElementById("product_price_7_3")) {
            var fourth = document.getElementById("product_price_7_3").innerHTML;
        }
        first = Number(first);
        second = Number(second);
        third = Number(third);
        fourth = Number(fourth);
        var total = first + second + third + fourth;
        document.getElementById("product_total").innerHTML = total.toFixed(2);
        document.getElementById("grand_total").innerHTML = total.toFixed(2);
    }
    else if(count_products == 5) {
        if(document.getElementById("product_price_2_0")) {
            var first = document.getElementById("product_price_2_0").innerHTML;
        }
        if(document.getElementById("product_price_7_0")) {
            var first = document.getElementById("product_price_7_0").innerHTML;
        }
        if(document.getElementById("product_price_2_1")) {
            var second = document.getElementById("product_price_2_1").innerHTML;
        }
        if(document.getElementById("product_price_7_1")) {
            var second = document.getElementById("product_price_7_1").innerHTML;
        }
        if(document.getElementById("product_price_2_2")) {
            var third = document.getElementById("product_price_2_2").innerHTML;
        }
        if(document.getElementById("product_price_7_2")) {
            var third = document.getElementById("product_price_7_2").innerHTML;
        }
        if(document.getElementById("product_price_2_3")) {
            var fourth = document.getElementById("product_price_2_3").innerHTML;
        }
        if(document.getElementById("product_price_7_3")) {
            var fourth = document.getElementById("product_price_7_3").innerHTML;
        }
        if(document.getElementById("product_price_2_4")) {
            var fifth = document.getElementById("product_price_2_4").innerHTML;
        }
        if(document.getElementById("product_price_7_4")) {
            var fifth = document.getElementById("product_price_7_4").innerHTML;
        }
        first = Number(first);
        second = Number(second);
        third = Number(third);
        fourth = Number(fourth);
        fifth = Number(fifth);
        var total = first + second + third + fourth + fifth;
        document.getElementById("product_total").innerHTML = total.toFixed(2);
        document.getElementById("grand_total").innerHTML = total.toFixed(2);
    }
    else if(count_products == 6) {
        if(document.getElementById("product_price_2_0")) {
            var first = document.getElementById("product_price_2_0").innerHTML;
        }
        if(document.getElementById("product_price_7_0")) {
            var first = document.getElementById("product_price_7_0").innerHTML;
        }
        if(document.getElementById("product_price_2_1")) {
            var second = document.getElementById("product_price_2_1").innerHTML;
        }
        if(document.getElementById("product_price_7_1")) {
            var second = document.getElementById("product_price_7_1").innerHTML;
        }
        if(document.getElementById("product_price_2_2")) {
            var third = document.getElementById("product_price_2_2").innerHTML;
        }
        if(document.getElementById("product_price_7_2")) {
            var third = document.getElementById("product_price_7_2").innerHTML;
        }
        if(document.getElementById("product_price_2_3")) {
            var fourth = document.getElementById("product_price_2_3").innerHTML;
        }
        if(document.getElementById("product_price_7_3")) {
            var fourth = document.getElementById("product_price_7_3").innerHTML;
        }
        if(document.getElementById("product_price_2_4")) {
            var fifth = document.getElementById("product_price_2_4").innerHTML;
        }
        if(document.getElementById("product_price_7_4")) {
            var fifth = document.getElementById("product_price_7_4").innerHTML;
        }
        if(document.getElementById("product_price_2_5")) {
            var sixth = document.getElementById("product_price_2_5").innerHTML;
        }
        if(document.getElementById("product_price_7_5")) {
            var sixth = document.getElementById("product_price_7_5").innerHTML;
        }
        first = Number(first);
        second = Number(second);
        third = Number(third);
        fourth = Number(fourth);
        fifth = Number(fifth);
        sixth = Number(sixth);
        var total = first + second + third + fourth + fifth + sixth;
        document.getElementById("product_total").innerHTML = total.toFixed(2);
        document.getElementById("grand_total").innerHTML = total.toFixed(2);
    }
    else {
        var first = document.getElementById("product_price_0").innerHTML;
        var second = document.getElementById("product_price_1").innerHTML;
        var third = document.getElementById("product_price_2").innerHTML;
        var fourth = document.getElementById("product_price_3").innerHTML;
        var fifth = document.getElementById("product_price_4").innerHTML;
        var sixth = document.getElementById("product_price_5").innerHTML;
        var seventh = document.getElementById("product_price_6").innerHTML;
        first = Number(first);
        second = Number(second);
        third = Number(third);
        fourth = Number(fourth);
        fifth = Number(fifth);
        sixth = Number(sixth);
        seventh = Number(seventh);
        var total = first + second + third + fourth + fifth + sixth + seventh;
        document.getElementById("product_total").innerHTML = total.toFixed(2);
        document.getElementById("grand_total").innerHTML = total.toFixed(2);
    }

    shipping_settings();

    var client = document.getElementById("client").value;
    var total = document.getElementById("product_total").innerText;
    document.getElementById("place-order-btn").setAttribute("onclick", "saveTotal("+client+", "+total+");");
}

if(document.getElementById("shipping")) {
    var shipping = document.getElementById("shipping");
    var selected_shipping = shipping.options[shipping.selectedIndex].value;
}

function shipping_settings() {
    if(document.getElementById("shipping")) {
        var shipping = document.getElementById("shipping");
        var selected_shipping = shipping.options[shipping.selectedIndex].value;
    }

    for(var i=0;i<count_products;i++) {
        if(document.getElementById("product_price_2_"+i)) {
            var country = document.getElementById("country_2_"+i).value;
            var price_transport = document.getElementById("price_transport_2_"+i).value;
            var price_free_transport = document.getElementById("price_free_transport_2_"+i).value;
            if(document.getElementById("shipping")) {
                if(selected_shipping.includes(country)) {
                    var product_price = document.getElementById("product_price_2_"+i).innerHTML;
                    product_price = Number(product_price);
                    if(product_price < price_free_transport) {
                        document.getElementById("seller_2_shipping_price_"+i).value = price_transport;
                    }
                    else {
                        document.getElementById("seller_2_shipping_price_"+i).value = '0.00';
                    }
                }
            }
        }
        if(document.getElementById("country_7_"+i)) {
            var country = document.getElementById("country_7_"+i).value;
            var price_transport = document.getElementById("price_transport_7_"+i).value;
            var price_free_transport = document.getElementById("price_free_transport_7_"+i).value;
            if(document.getElementById("shipping")) {
                if(selected_shipping.includes(country)) {
                    if(document.getElementById("product_price_7_"+i)) {
                        product_price = document.getElementById("product_price_7_"+i).innerHTML;
                    }
                    else {
                        var k = i + 1;
                        if(document.getElementById("product_price_7_"+k)) {
                            product_price = document.getElementById("product_price_7_"+k).innerHTML;
                        }
                    }
                    product_price = Number(product_price);
                    if(product_price < price_free_transport) {
                        document.getElementById("seller_7_shipping_price_"+i).value = price_transport;
                    }
                    else {
                        document.getElementById("seller_7_shipping_price_"+i).value = '0.00';
                    }
                }
            }
        }

        var shipping_price;
        if(document.getElementById("seller_2_shipping_price_0")) {
            var shipping_price_seller_2 = document.getElementById("seller_2_shipping_price_0").value;
            shipping_price_seller_2 = Number(shipping_price_seller_2);
        }
        if(document.getElementById("seller_7_shipping_price_0")) {
            var shipping_price_seller_7 = document.getElementById("seller_7_shipping_price_0").value;
            shipping_price_seller_7 = Number(shipping_price_seller_7);
        }
        if(document.getElementById("seller_2_shipping_price_0")) {
            shipping_total = shipping_price_seller_2;
        }
        if(document.getElementById("seller_7_shipping_price_0")) {
            shipping_total = shipping_price_seller_7;
        }
        if((document.getElementById("seller_2_shipping_price_0")) && (document.getElementById("seller_7_shipping_price_0"))) {
            shipping_total = shipping_price_seller_2 + shipping_price_seller_7;
        }

        if(document.getElementById("shipping")) {
            document.getElementById("shipping_total").innerHTML = shipping_total.toFixed(2);
        }

        var product_total = document.getElementById("product_total").innerHTML;
        product_total = Number(product_total);
        if(document.getElementById("shipping")) {
            var grand_total = product_total + Number(shipping_total);
        }
        else {
            var grand_total = product_total;
        }
        document.getElementById("grand_total").innerHTML = grand_total.toFixed(2);
    }
}

var client = document.getElementById("client").value;
var total = document.getElementById("product_total").innerText;
document.getElementById("place-order-btn").setAttribute("onclick", "saveTotal("+client+", "+total+");");

function saveTotal(k, d) {
    jQuery.ajax({
        type: "POST",
        url: "/minicartDelete.php",
        data: "client="+k+"&total="+d,
    });
}