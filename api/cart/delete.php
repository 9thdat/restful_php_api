<?php
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers:*");
include_once("../../config/db_azure.php");
include_once("../../model/cart.php");
include_once("../../model/product.php");
include_once("../../vendor/autoload.php");
include_once("../../constants.php");

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
$db = new db();
$connect = $db -> connect();

if($_SERVER["REQUEST_METHOD"] == "DELETE"){
    try{
        $cart = new cart($connect);
        $allheaders = getallheaders();
        $jwt = $allheaders['Authorization'];

        $customer_data = JWT::decode($jwt, new Key(SECRET_KEY, 'HS256'));
        $data = $customer_data->data;
        
        $cart->customer_email = $data->email;

        $data = json_decode(file_get_contents("php://input"));
        
        $cart->product_id = $data->product_id;
        $cart->color = $data->color;
        if($cart->check_cart_detail()){
            if($cart->delete()){
                throwMessage(SUCCESS_RESPONSE, "Delete successful");
            }else{
                throwMessage(FAILED_DELETE_PRODUCT_TO_CART, "Delete unsuccessful");
            }
        }else{
            throwMessage(FAILED_DELETE_PRODUCT_TO_CART, "Product does not exist in cart");
        }
        
    }catch(Exception $e){
        throwMessage(JWT_PROCESSING_ERROR, $e->getMessage());
    }



}else{
    throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
}



?>