<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    include_once("../../config/db_azure.php");
    include_once("../../model/customer.php");
    include_once("../../vendor/autoload.php");   
    include_once("../../constants.php");

    
    use \Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    $db = new db();
    $connect = $db -> connect();

    $customer = new customer($connect); 

    if($_SERVER["REQUEST_METHOD"] == "GET"){
        try{
            $allheaders = getallheaders();
            $jwt = $allheaders['Authorization'];

            $customer_data = JWT::decode($jwt, new Key(SECRET_KEY, 'HS256'));
            $data = $customer_data->data;

            throwMessage(SUCCESS_RESPONSE, $data);
        }catch(Exception$e){
            
            throwMessage(JWT_PROCESSING_ERROR, $e->getMessage());
        }
    }else {
        throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
    }



?>