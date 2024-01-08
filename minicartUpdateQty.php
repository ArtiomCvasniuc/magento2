<?php
if (file_exists("conf/conf.php")) 
    @include 'conf/conf.php';

$conn = new mysqli($conf->db->host, $conf->db->user, $conf->db->pass, $conf->db->name);

$remote_ip = $_SERVER['REMOTE_ADDR'];

$query = "SELECT items_qty ";
$query .= "FROM mgkf_quote ";
$query .= "WHERE remote_ip = '".$remote_ip."' ";
$query .= "AND is_active = 1";
$result = $conn->query($query);
if($result->num_rows > 0) { 
    $row = $result->fetch_assoc();
    $qty = intval($row['items_qty']);

    echo $qty;
}
?>