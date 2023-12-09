<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers:*");
    include_once("../../config/db_azure.php");
    include_once("../../model/customer.php");
    include_once("../../constants.php");

    $db = new db();
    $connect = $db->connect();

    $customer = new customer($connect);

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $data = json_decode(file_get_contents("php://input"));
        $customer->setEmail($data->email);
        $customer->setName($data->name);
        $customer->setPassword($data->password);
        $customer->setPhone($data->phone);
        $customer->setGender($data->gender);
        $customer->setBirthday($data->birthday);
        $customer->setAddress($data->address);
        $customer->setWard($data->ward);
        $customer->setDistrict($data->district);
        $customer->setCity($data->city);

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