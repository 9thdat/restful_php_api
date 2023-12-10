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
        
        $address = $data->infor->address;
        $phone = $data->infor->phone;
        $shipping_fee = $data->infor->shipping_fee;
        $discount_id = $data->infor->discount_id;
        $total_price = $data->infor->total_price;
        $delivery_type = $data->infor->delivery_type;
        $payment_type = $data->infor->payment_type;

        
        if (isset($data->product) && is_array($data->product)) {
            foreach ($data->product as $product) {
                $product_id = $product->id;
                $color = $product->color;
                $quantity = $product->quantity;
                $product_check = new product_quantity($connect, $product_id, $color, $quantity);
                if(!$product_check->check_quantity()){
                    throwMessage(FAILED_ORDER, "ID: {$product_id}, Color: {$color}. Ordered not enough quantity {$quantity}");
                    die();
                }
            }
            
            $orders = new orders($connect, $customer_email, $address, $phone, $shipping_fee, $discount_id, $total_price, $delivery_type, $payment_type);
            $order_id = $orders->order();
            if ($order_id == -1){
                throwMessage(FAILED_ORDER, "Order Unsuccessfully, Can't create order");
            }else{
                foreach ($data->product as $product) {
                    $product_id = $product->id;
                    $color = $product->color;
                    $quantity = $product->quantity;
                    $price = $product->price;

                    $order_detail = new order_detail($connect, $order_id, $product_id, $color, $quantity, $price);
                    if(!$order_detail->add()){
                        throwMessage(FAILED_ORDER, "Order Unsuccessfully, Can't add order detail");
                    }
                    $product_update = new product_quantity($connect, $product_id, $color, $quantity);
                    $product_update->update_sold_order();

                }
                throwMessage(SUCCESS_RESPONSE,"Order Successfully");
            }
        } else {
            throwMessage(INVALID_DATA_INPUT, "Product or Data Invalid");
        }
        
    }catch(Exception $e){
        throwMessage(JWT_PROCESSING_ERROR, $e->getMessage());
    }
}else{
    throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
}














?>