<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    include_once("../../config/db_azure.php");
    include_once("../../model/customer.php");    

    $db = new db();
    $connect = $db -> connect();

    $customer = new customer($connect);

    
    $customer->email = isset($_GET["email"])? $_GET["email"] : die();
    $customer->password = isset($_GET["password"]) ? $_GET["password"] : die();

    if($customer->login()){
        echo json_encode(array("message" => "200 OK"), JSON_PRETTY_PRINT);
    }else{
        echo json_encode(array("message" => "404 NOT FOUND"), JSON_PRETTY_PRINT);
    }
    
?>