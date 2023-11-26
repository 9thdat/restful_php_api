<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    include_once("../../config/db_azure.php");
    include_once("../../model/customer.php");
    include_once("../../vendor/autoload.php");   
    
    use \Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    $db = new db();
    $connect = $db -> connect();

    $customer = new customer($connect); 

    if($_SERVER["REQUEST_METHOD"] == "GET"){
        try{
            $allheaders = getallheaders();
            $jwt = $allheaders['Authorization'];

            $secret_key = "techshop";
            $customer_data = JWT::decode($jwt, new Key($secret_key, 'HS256'));
            $data = $customer_data->data;
            echo json_encode([
                'status' => 200,
                'message' => $data,
            ]);
        }catch(Exception$e){
            echo json_encode([
                'status' => 404,
                'message' => $e->getMessage(),
            ]);
        }
    }else {
        echo json_encode([
            'status' => 0,
            'message' => 'Access Denied',
        ]);
    }



?>