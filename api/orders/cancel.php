<?php 
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers:*");
include_once("../../config/db_azure.php");
include_once("../../model/orders.php");
include_once("../../model/order_detail.php");
include_once("../../model/product_quantity.php");
include_once("../../vendor/autoload.php");
include_once("../../constants.php");

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
$db = new db();
$connect = $db -> connect();

if($_SERVER["REQUEST_METHOD"] == "PUT"){
    try{
        $allheaders = getallheaders();
        $jwt = $allheaders['Authorization'];

        $customer_data = JWT::decode($jwt, new Key(SECRET_KEY, 'HS256'));
        $data = $customer_data->data;
        
        $customer_email = $data->email;

        $data = json_decode(file_get_contents("php://input"));
        $order_id = $data->id;

        $orders = new orders($connect, $customer_email);
        $orders->id = $order_id;
        if (!$orders->cancel()){
            throwMessage(FAILD_CANCEL_ORDER, "Cancel unsuccessfully");
        }else{
            $order_detail = new order_detail($connect, $order_id);
            $read_od = $order_detail->read();
            if($read_od->rowCount()>0){
                foreach($read_od as $row){
                    extract($row);
                    $product_return = new product_quantity($connect, $PRODUCT_ID, $COLOR, $QUANTITY);
                    if(!$product_return->update_quantity_return()){
                        throwMessage(FAILD_CANCEL_ORDER, "Cancel unsuccessfully");
                    }
                   
                }
            }else{
                throwMessage(FAILD_CANCEL_ORDER, "Order does not exist");
            }
            throwMessage(SUCCESS_RESPONSE, "Cancel successfully");
        }
        
       
    }catch(Exception $e){
        throwMessage(JWT_PROCESSING_ERROR, $e->getMessage());
    }
}else{
    throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
}


?>