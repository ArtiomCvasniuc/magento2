<?php
if (file_exists("conf/conf.php")) 
    @include 'conf/conf.php';

$conn = new mysqli($conf->db->host, $conf->db->user, $conf->db->pass, $conf->db->name);

if(isset($_POST['customer_id'])) {
    $customerId = $_POST['customer_id'];
    $companyName = $_POST['company_name'];
    $companyCountry = $_POST['company_country'];
    $companyCity = $_POST['company_city'];
    $companyAddress = $_POST['company_address'];
    $companyCui = $_POST['company_cui'];
    $companyRegNumber = $_POST['company_reg_number'];
    $companyBank = $_POST['company_bank'];
    $companyAccount = $_POST['company_account'];
    $companyResponsible = $_POST['company_responsible'];    

    $responsible = explode(" ", $companyResponsible);
    $firstname = $responsible[0];
    $lastname = $responsible[1];

    $query = "UPDATE mgkf_customer_grid_flat SET ";
    $query .= "billing_company = '".$companyName."', ";
    $query .= "billing_country_id = '".$companyCountry."', ";
    $query .= "billing_city = '".$companyCity."', ";
    $query .= "billing_street = '".$companyAddress."', ";
    $query .= "billing_firstname = '".$firstname."', ";
    $query .= "billing_lastname = '".$lastname."' ";
    $query .= "WHERE entity_id = '".$customerId."'";
    $result = $conn->query($query);

    $query = "SELECT entity_id ";
    $query .= "FROM mgkf_customer_address_entity ";
    $query .= "WHERE parent_id = '".$customerId."' ";
    $query .= "LIMIT 0,1";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $entityId = $row['entity_id'];

    $query = "UPDATE mgkf_customer_address_entity ";
    $query .= "SET street = '".$companyAddress."' ";
    $query .= "WHERE entity_id = '".$entityId."'";
    $result = $conn->query($query);

    $query = "UPDATE mgkf_customer_address_entity ";
    $query .= "SET company = '".$companyName."' ";
    $query .= "WHERE entity_id = '".$entityId."'";
    $result = $conn->query($query);

    $query = "UPDATE mgkf_customer_address_entity_int ";
    $query .= "SET value = '".$companyCui."' ";
    $query .= "WHERE attribute_id = 238 ";
    $query .= "AND entity_id = '".$entityId."'";
    $result = $conn->query($query);

    $query = "UPDATE mgkf_customer_address_entity_varchar ";
    $query .= "SET value = '".$companyRegNumber."' ";
    $query .= "WHERE attribute_id = 239 ";
    $query .= "AND entity_id = '".$entityId."'";
    $result = $conn->query($query);

    $query = "UPDATE mgkf_customer_address_entity_varchar ";
    $query .= "SET value = '".$companyBank."' ";
    $query .= "WHERE attribute_id = 240 ";
    $query .= "AND entity_id = '".$entityId."'";
    $result = $conn->query($query);

    $query = "UPDATE mgkf_customer_address_entity_varchar ";
    $query .= "SET value = '".$companyAccount."' ";
    $query .= "WHERE attribute_id = 241 ";
    $query .= "AND entity_id = '".$entityId."'";
    $result = $conn->query($query);

    $query = "UPDATE mgkf_customer_entity ";
    $query .= "SET firstname = '".$firstname."', ";
    $query .= "lastname = '".$lastname."' ";
    $query .= "WHERE entity_id = '".$customerId."'";
    $result = $conn->query($query);
}
if(isset($_POST['customer'])) {
    $customerId = $_POST['customer'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $country = $_POST['country'];
    $city = $_POST['city'];
    $region = $_POST['region'];
    $postCode = $_POST['postcode'];
    $phone = $_POST['phone'];

    $query = "INSERT INTO mgkf_customer_address_entity ";
    $query .= "(parent_id, is_active, city, country_id, firstname, lastname, postcode, region, street, telephone) VALUES ";
    $query .= "('".$customerId."', '1', '".$city."', '".$country."', '".$firstname."', '".$lastname."', '".$postCode."', '".$region."', '".$address."', '".$phone."')";
    $result = $conn->query($query);
}
?>