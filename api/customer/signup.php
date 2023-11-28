<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers:Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");
    include_once("../../config/db_azure.php");
    include_once("../../model/customer.php");
    include_once("../../constants.php");

    $db = new db();
    $connect = $db->connect();

    $customer = new customer($connect);

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $data = json_decode(file_get_contents("php://input"));
        $customer->email = $data->email;
        $customer->name = $data->name;
        $customer->password = $data->password;  
        $customer->phone = $data->phone;
        $customer->gender = $data->gender;
        $customer->birthday = $data->birthday;
        $customer->address = $data->address;
        $customer->ward = $data->ward;
        $customer->district = $data->district;
        $customer->city = $data->city;

        if ($customer->find()){
            if($customer->signup()){
                throwMessage(SUCCESS_RESPONSE, 'User add Successfully');
            }else{
                throwMessage(SUCCESS_RESPONSE, "Failed to sign up.");
            }
        }else{
            throwMessage(SUCCESS_RESPONSE, "Email already Exists");
        }
    }else{
        throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
    }

?>