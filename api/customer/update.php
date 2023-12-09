<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: PUT");
    header("Access-Control-Allow-Headers:*");
    include_once("../../config/db_azure.php");
    include_once("../../model/customer.php");
    include_once("../../vendor/autoload.php");   
    include_once("../../constants.php");

    use \Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    $db = new db();
    $connect = $db -> connect();
    if ($_SERVER['REQUEST_METHOD'] == "PUT") {
        $customer = new customer($connect); 
        try{
            $allheaders = getallheaders();
            $jwt = $allheaders['Authorization'];

            $customer_data = JWT::decode($jwt, new Key(SECRET_KEY, 'HS256'));
            $data = $customer_data->data;
            
            $customer->setEmail($data->email);
            
            $data_update = json_decode(file_get_contents("php://input", true));
            if ($data_update->name){
                $customer->setName(validateParameter('name', $data_update->name, STRING, false));
            }
            if ($data_update->password){
                $customer->setPassword(validateParameter('password', $data_update->password, STRING, false));
            }
            if ($data_update->phone){
                $customer->setPhone(validateParameter('phone', $data_update->phone, INTEGER, false));
            }
            if ($data_update->gender){
                $customer->setGender($data_update->gender);
            }
            if ($data_update->address){
                $customer->setAddress(validateParameter('address', $data_update->address, STRING, false));
            }
            if ($data_update->ward){
                $customer->setWard(validateParameter('ward', $data_update->ward, STRING, false));
            }
            if ($data_update->district){
                $customer->setDistrict(validateParameter('district', $data_update->district, STRING, false));
            }
            if ($data_update->city){
                $customer->setCity(validateParameter('city', $data_update->city, STRING, false));
            }
            if ($data_update->image){
                $customer->setImage($data_update->image);
            }

            if ($customer->update()){
                throwMessage(SUCCESS_RESPONSE, "Updated successfully.");
            }else{
                throwMessage(SUCCESS_RESPONSE, "Failed to update.");
            }

        }catch(Exception $e){
            throwMessage(JWT_PROCESSING_ERROR, $e->getMessage());
        }
    }else {
        throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
    }





?>