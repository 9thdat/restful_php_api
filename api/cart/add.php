<?php 
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: PUT");
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

if($_SERVER["REQUEST_METHOD"] == "PUT"){
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
        $cart->quantity = $data->quantity;
        
        $check_cart = $cart->check_cart();
        if ($cart->add_to_cart($check_cart)){
            $check_cart_detail = $cart->check_cart_detail();
            if ($cart->add_to_cart_detail($check_cart_detail)){
                throwMessage(SUCCESS_RESPONSE, "Successfully add product to cart");
                
            }else{
                throwMessage(FAILED_ADD_PRODUCT_TO_CART, "Failed");
            }
        
        }else{
            throwMessage(FAILED_ADD_PRODUCT_TO_CART, "Failed ");
        }
    }catch(Exception $e){
        throwMessage(JWT_PROCESSING_ERROR, $e->getMessage());
    }
}else{
    throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
}














?>