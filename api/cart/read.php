<?php
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");
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

if($_SERVER["REQUEST_METHOD"] == "GET"){
    try{
        $cart = new cart($connect);
        $allheaders = getallheaders();
        $jwt = $allheaders['Authorization'];

        $customer_data = JWT::decode($jwt, new Key(SECRET_KEY, 'HS256'));
        $data = $customer_data->data;
        
        $cart->customer_email = $data->email;

        $read = $cart->read();
        $row = $read->rowCount();

        if($row>0){
            $product_array = [];
            $product_array['product_cart'] = [];

            foreach($read as $row){
                extract($row);
                $quantity = $QUANTITY;
                $color = $COLOR;

                $db2 = new db();
                $conn = $db2->connect();

                $product = new product($conn);
                $product->id = $PRODUCT_ID;
                
                $show_by_id_cart = $product->show_by_id_cart();
                $row2 = $show_by_id_cart->rowCount();


                foreach ($show_by_id_cart as $row2){
                    extract($row2);
        
                    $image_data = base64_encode($IMAGE_COLOR); 
        
                    $product_item = array(
                        'id' => $ID,
                        'name' => $NAME,
                        'price' => $PRICE_PRODUCT,
                        'category' => $CATEGORY,
                        'brand' => $BRAND,
                        'pre_discount' => $PRE_DISCOUNT,
                        'discount_percent' => $DISCOUNT_PERCENT,
                        'color' => $COLOR,
                        'quantity' => $quantity,
                        'stock' =>$QUANTITY_STOCK,
                        'image' => $image_data
                    );
                    array_push($product_array['product_cart'], $product_item);
                }

            }

            $json_data = json_encode($product_array, JSON_PRETTY_PRINT);
            echo $json_data;
            
        }

    }catch(Exception$e){
        throwMessage(JWT_PROCESSING_ERROR, $e->getMessage());
    }
}else {
    throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
}



?>