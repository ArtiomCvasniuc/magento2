<?php 
if (file_exists("conf/conf.php")) 
    @include 'conf/conf.php';

$conn = new mysqli($conf->db->host, $conf->db->user, $conf->db->pass, $conf->db->name);

if(isset($_POST['client'])) {
    $client = $_POST['client'];
    $total = $_POST['total'];    

    if(strpos($client, ".") !== false) {
        $query = "DELETE FROM mgkf_cart_total WHERE remote_ip = '$client'";
        $result = $conn->query($query);
        
        $query = "INSERT INTO mgkf_cart_total ";
        $query .= "(customer_id, remote_ip, total) VALUES ('', '$client', '$total')";
        $result = $conn->query($query);
    }
    else {
        $query = "DELETE FROM mgkf_cart_total WHERE customer_id = '$client'";
        $result = $conn->query($query);

        $query = "INSERT INTO mgkf_cart_total ";
        $query .= "(customer_id, remote_ip, total) VALUES ('$client', NULL, '$total')";
        $result = $conn->query($query);
    }
}
if(isset($_POST['item_id'])) {
    $entityId = $_POST['entity_id'];
    $itemId = $_POST['item_id'];
    $productTotal = $_POST['product_total'];
    $grandTotal = $_POST['grand_total'];
    $updatedTotal = $grandTotal - $productTotal;

    $query = "DELETE FROM mgkf_quote_item ";
    $query .= "WHERE item_id = '".$itemId."'";
    $result = $conn->query($query);

    $query = "SELECT COUNT(item_id) as qty ";
    $query .= "FROM mgkf_quote_item ";
    $query .= "WHERE quote_id = '".$entityId."' ";
    $query .= "AND parent_item_id IS NULL";
    $result = $conn->query($query);
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $qty = $row['qty'];
    }
    else {
        $qty = 0;
    }

    $query = "UPDATE mgkf_quote ";
    $query .= "SET items_count = '".$qty."', ";
    $query .= "items_qty = '".$qty."' ";
    $query .= "WHERE entity_id = '".$entityId."'";
    $result = $conn->query($query);
}
?>