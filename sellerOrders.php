<?php
if (file_exists("conf/conf.php")) 
    @include 'conf/conf.php';

$conn = new mysqli($conf->db->host, $conf->db->user, $conf->db->pass, $conf->db->name);

$order_id = $_POST['order_id'];
$url = $_POST['url'];
ob_start();
echo '<input type="hidden" id="url" value="'.$url.'">';

if(isset($_POST['return_completed'])) {
    $order_id = $_POST['order_id'];
    
    $query1 = "UPDATE mgkf_marketplace_sellerorder ";
    $query1 .= "SET status = 'return_completed' ";
    $query1 .= "WHERE increment_id = '".$order_id."'";
    $result1 = $conn->query($query1);

    $query2 = "UPDATE mgkf_sales_order ";
    $query2 .= "SET state = 'return_completed', status = 'return_completed' ";
    $query2 .= "WHERE increment_id = '".$order_id."'";
    $result2 = $conn->query($query2);

    $query3 = "UPDATE mgkf_sales_order_grid ";
    $query3 .= "SET status = 'return_completed' ";
    $query3 .= "WHERE increment_id = '".$order_id."'";
    $result3 = $conn->query($query3);
}
if(isset($_POST['return_taken'])) {
    $order_id = $_POST['order_id'];
    
    $query1 = "UPDATE mgkf_marketplace_sellerorder ";
    $query1 .= "SET status = 'return_taken' ";
    $query1 .= "WHERE increment_id = '".$order_id."'";
    $result1 = $conn->query($query1);

    $query2 = "UPDATE mgkf_sales_order ";
    $query2 .= "SET state = 'return_taken', status = 'return_taken' ";
    $query2 .= "WHERE increment_id = '".$order_id."'";
    $result2 = $conn->query($query2);

    $query3 = "UPDATE mgkf_sales_order_grid ";
    $query3 .= "SET status = 'return_taken' ";
    $query3 .= "WHERE increment_id = '".$order_id."'";
    $result3 = $conn->query($query3);
}
if(isset($_POST['not_picked'])) {
    $order_id = $_POST['inc_id'];
    
    $query1 = "UPDATE mgkf_marketplace_sellerorder ";
    $query1 .= "SET status = 'not_picked' ";
    $query1 .= "WHERE increment_id = '".$order_id."'";
    $result1 = $conn->query($query1);

    $query2 = "UPDATE mgkf_sales_order ";
    $query2 .= "SET state = 'not_picked', status = 'not_picked' ";
    $query2 .= "WHERE increment_id = '".$order_id."'";
    $result2 = $conn->query($query2);

    $query3 = "UPDATE mgkf_sales_order_grid ";
    $query3 .= "SET status = 'not_picked' ";
    $query3 .= "WHERE increment_id = '".$order_id."'";
    $result3 = $conn->query($query3);
}
if(isset($_POST['confirm'])) {
    $order_id = $_POST['inc_id'];
    
    $query1 = "UPDATE mgkf_marketplace_sellerorder ";
    $query1 .= "SET status = 'confirmed' ";
    $query1 .= "WHERE increment_id = '".$order_id."'";
    $result1 = $conn->query($query1);

    $query2 = "UPDATE mgkf_sales_order ";
    $query2 .= "SET state = 'confirmed', status = 'confirmed' ";
    $query2 .= "WHERE increment_id = '".$order_id."'";
    $result2 = $conn->query($query2);

    $query3 = "UPDATE mgkf_sales_order_grid ";
    $query3 .= "SET status = 'confirmed' ";
    $query3 .= "WHERE increment_id = '".$order_id."'";
    $result3 = $conn->query($query3);
}
if (isset($_POST['order_refused'])) {
    $query1 = "UPDATE mgkf_marketplace_sellerorder ";
    $query1 .= "SET is_returned = 1, status = 'returned' ";
    $query1 .= "WHERE increment_id = '".$order_id."'";
    $result1 = $conn->query($query1);

    $query2 = "UPDATE mgkf_sales_order ";
    $query2 .= "SET status = 'returned' ";
    $query2 .= "WHERE increment_id = '".$order_id."'";
    $result2 = $conn->query($query2);
}
if (isset($_POST['awb_submit'])) {
    $awb = $_POST['awb'];
    $email = $_POST['email'];
    $courier = $_POST['courier'];
    $order_id = $_POST['inc_id'];

    $query1 = "UPDATE mgkf_marketplace_sellerorder ";
    $query1 .= "SET awb_number = '".$awb."', ";
    $query1 .= "courier = '".$courier."', ";
    $query1 .= "is_shipped = 1, ";
    $query1 .= "is_invoiced = 1, ";
    $query1 .= "status = 'processing' ";
    $query1 .= "WHERE increment_id = '".$order_id."'";
    $result1 = $conn->query($query1);

    $query2 = "UPDATE mgkf_sales_order ";
    $query2 .= "SET awb_number = '".$awb."', ";
    $query2 .= "courier = '".$courier."', ";
    $query2 .= "status = 'processing', ";
    $query2 .= "state = 'processing' ";
    $query2 .= "WHERE increment_id = '".$order_id."'";
    $result2 = $conn->query($query2);

    $query3 = "UPDATE mgkf_sales_order_grid ";
    $query3 .= "SET status = 'processing' ";
    $query3 .= "WHERE increment_id = '".$order_id."'";
    $result3 = $conn->query($query3);

    $to = $email;
    $subject = 'Order Shipping';
    $body_message = "Your order #'.$order_id.' was shipped. AWB Number is '".$awb."'. Courier Name is '".$courier."'";
    $body_message.= '\nBest regards, test Marketplace';
    $from = 'test@test.com';

    $headers = 'From: ' . $from . "\r\n";

    $mail_sent = mail($to, $subject, $body_message, $headers);
}

if (isset($_POST['not_in_stock'])) {
    $order_id = $_POST['inc_id'];
    $email = $_POST['email'];

    $query1 = "UPDATE mgkf_marketplace_sellerorder ";
    $query1 .= "SET status = 'cancelled', ";
    $query1 .= "is_canceled = 1, ";
    $query1 .= "cancelling_reason = 'not_in_stock' ";
    $query1 .= "WHERE increment_id = '".$order_id."'";
    $result1 = $conn->query($query1);

    $query2 = "UPDATE mgkf_sales_order ";
    $query2 .= "SET state = 'canceled', ";
    $query2 .= "status = 'canceled', ";
    $query2 .= "cancelling_reason = 'not_in_stock' ";
    $query2 .= "WHERE increment_id = '".$order_id."'";
    $result2 = $conn->query($query2);

    $query3 = "UPDATE mgkf_sales_order_grid ";
    $query3 .= "SET status = 'canceled' ";
    $query3 .= "WHERE increment_id = '".$order_id."'";
    $result3 = $conn->query($query3);

    $to = $email;
    $subject = 'Order Cancelling';
    $body_message = 'Your order #'.$order_id.' was cancelled because product is not in stock';
    $body_message.= 'Best regards, test Marketplace';
    $from = 'test@test.com';

    $headers = 'From: ' . $from . "\r\n";

    $mail_sent = mail($to, $subject, $body_message, $headers);
}
if (isset($_POST['has_defects'])) {
    $order_id = $_POST['inc_id'];
    $email = $_POST['email'];

    $query1 = "UPDATE mgkf_marketplace_sellerorder ";
    $query1 .= "SET status = 'cancelled', ";
    $query1 .= "is_canceled = 1, ";
    $query1 .= "cancelling_reason = 'has_defects' ";
    $query1 .= "WHERE increment_id = '".$order_id."'";
    $result1 = $conn->query($query1);
    
    $query2 = "UPDATE mgkf_sales_order ";
    $query2 .= "SET state = 'canceled', ";
    $query2 .= "status = 'canceled', ";
    $query2 .= "cancelling_reason = 'has_defects' ";
    $query2 .= "WHERE increment_id = '".$order_id."'";
    $result2 = $conn->query($query2);

    $query3 = "UPDATE mgkf_sales_order_grid ";
    $query3 .= "SET status = 'canceled' ";
    $query3 .= "WHERE increment_id = '".$order_id."'";
    $result3 = $conn->query($query3);

    $to = $email;
    $subject = 'Order Cancelling';
    $body_message = 'Your order #'.$order_id.' was cancelled because product has defects\r\n';
    $body_message.= 'Best regards, test Marketplace';
    $from = 'atytude@atytude.com';

    $headers = 'From: ' . $from . "\r\n";

    $mail_sent = mail($to, $subject, $body_message, $headers);
}
header('location: '.$url.'');
?>