<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers:Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");
    include_once("../../config/db_azure.php");
    include_once("../../model/customer.php");

    $db = new db();
    $connect = $db->connect();

    $customer = new customer($connect);

    $data = json_decode(file_get_contents("php://input"));
    $customer->email = $data->email;
    $customer->name = $data->name;
    $customer->password = $data->password;  
    $customer->phone = $data->phone;
    $customer->gender = $data->gender;
    $customer->birthday = $data->birthday;
    $customer->address = $data->address;
    $customer->quarter = $data->quarter;
    $customer->district = $data->district;
    $customer->city = $data->city;

    if($customer->signup()){
        echo json_encode(array("message" => "200 OK"), JSON_PRETTY_PRINT);
    }else{
        echo json_encode(array("message" => "400 BAD REQUEST"), JSON_PRETTY_PRINT);
    }

?>