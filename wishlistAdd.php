<?php 
if (file_exists("conf/conf.php")) 
    @include 'conf/conf.php';

$conn = new mysqli($conf->db->host, $conf->db->user, $conf->db->pass, $conf->db->name);

if(isset($_POST['guest_id'])) {
    $guest = $_POST['guest_id'];

    $query = "SELECT id ";
    $query .= "FROM mgkf_guest_wishlist ";
    $query .= "WHERE remote_ip = $guest";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $guestWishlistId = $row['id'];

    $query = "SELECT product_id ";
    $query .= "FROM mgkf_guest_wishlist_item ";
    $query .= "WHERE guest_wishlist_id = '".$guestWishlistId."'";
    $result = $conn->query($query);
    $guestWishlistArr = array();
    while($row = $result->fetch_assoc()) {
        $guestWishlistProductId = $row['product_id'];
        array_push($guestWishlistArr, $guestWishlistProductId);
    }
    $guestWishlistList = implode(",", $guestWishlistArr);
    echo $guestWishlistList;
}
if(isset($_POST['user_id'])) {
    $customerId = $_POST['user_id'];

    $query = "SELECT wishlist_id ";
    $query .= "FROM mgkf_wishlist ";
    $query .= "WHERE customer_id = '".$customerId."'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $wishlistId = $row['wishlist_id'];

    $query = "SELECT product_id ";
    $query .= "FROM mgkf_wishlist_item ";
    $query .= "WHERE wishlist_id = '".$wishlistId."'";
    $result = $conn->query($query);
    $wishlistArr = array();
    while($row = $result->fetch_assoc()) {
        $wishlistProductId = $row['product_id'];
        array_push($wishlistArr, $wishlistProductId);
    }
    $wishlistList = implode(",", $wishlistArr);
    echo $wishlistList;
}
if(isset($_POST['customer_id'])) {
    $customerId = $_POST['customer_id'];
    $productId = $_POST['product_id'];

    $query = "SELECT wishlist_id ";
    $query .= "FROM mgkf_wishlist ";
    $query .= "WHERE customer_id = '".$customerId."'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $wishlistId = $row['wishlist_id'];

    $query = "INSERT INTO mgkf_wishlist_item ";
    $query .= "(wishlist_id, product_id, store_id, qty) ";
    $query .= "VALUES ('".$wishlistId."', '".$productId."', '4', '1')";
    $result = $conn->query($query);
    $wishlistItemId = $conn->insert_id;

    $query = "INSERT INTO mgkf_wishlist_item_option ";
    $query .= "(wishlist_item_id, product_id, code) ";
    $query .= "VALUES ('".$wishlistItemId."', '".$productId."', '')";
    $result = $conn->query($query);
}
if(isset($_POST['guest'])) {
    $guest = $_POST['guest'];
    $productId = $_POST['product_id'];

    $query = "SELECT id ";
    $query .= "FROM mgkf_guest_wishlist ";
    $query .= "WHERE remote_ip = '".$guest."'";
    $result = $conn->query($query);
    if($result->num_rows < 1) {
        $query = "INSERT INTO mgkf_guest_wishlist ";
        $query .= "(remote_ip) VALUES ('".$guest."')";
        $result = $conn->query($query);
    }

    $query = "SELECT id ";
    $query .= "FROM mgkf_guest_wishlist ";
    $query .= "WHERE remote_ip = '".$guest."'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $guestWishlistId = $row['id'];

    $query = "INSERT INTO mgkf_guest_wishlist_item ";
    $query .= "(remote_ip, guest_wishlist_id, product_id) ";
    $query .= "VALUES ";
    $query .= "('".$guest."', '".$guestWishlistId."', '".$productId."')";
    $result = $conn->query($query);
}
?>