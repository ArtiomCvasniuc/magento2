<?php 
if (file_exists("conf/conf.php")) 
    @include 'conf/conf.php';

$conn = new mysqli($conf->db->host, $conf->db->user, $conf->db->pass, $conf->db->name);

if(isset($_POST['product_id'])) {
    $itemId = $_POST['item_id'];
    $productId = $_POST['product_id'];

    $query = "DELETE FROM mgkf_wishlist_item ";
    $query .= "WHERE wishlist_item_id = '".$itemId."' ";
    $result = $conn->query($query);

    $query = "DELETE FROM mgkf_wishlist_item_option ";
    $query .= "WHERE wishlist_item_id = '".$itemId."' ";
    $query .= "AND product_id = '".$productId."'";
    $result = $conn->query($query);
}
if(isset($_POST['guest'])) {
    $itemId = $_POST['item_id'];
    $productId = $_POST['id'];

    $query = "DELETE FROM mgkf_guest_wishlist_item ";
    $query .= "WHERE id = '".$itemId."'";
    $result = $conn->query($query);
}
?>