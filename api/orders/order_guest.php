<?php 
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers:*");
include_once("../../config/db_azure.php");
include_once("../../model/orders.php");
include_once("../../model/order_detail.php");
include_once("../../model/product_quantity.php");
include_once("../../model/discount.php");
include_once("../../vendor/autoload.php");
include_once("../../constants.php");


$db = new db();
$connect = $db -> connect();

if($_SERVER["REQUEST_METHOD"] == "PUT"){

        $data = json_decode(file_get_contents("php://input"));

        $data_email = $data->infor->email;
        $customer_email = ($data_email == null) ? $data_email : $customer_email;
        
        $name = $data->infor->name;
        $address = $data->infor->address;
        $ward = $data->infor->ward;
        $district = $data->infor->district;
        $city = $data->infor->city;
        $phone = $data->infor->phone;
        $shipping_fee = $data->infor->shipping_fee;
        $discount_code = $data->infor->discount_code;
        $total_price = $data->infor->total_price;
        $note = $data->infor->note;
        $delivery_type = $data->infor->delivery_type;
        $payment_type = $data->infor->payment_type;

        $discount = new discount($connect);
        $discount->setCode($discount_code);
        $discount_id = $discount->getId();

        
        if (isset($data->product) && is_array($data->product)) {
            foreach ($data->product as $product) {
                $productId = $product->productId;
                $color = $product->color;
                $quantity = $product->quantity;
                $product_check = new product_quantity($connect, $productId, $color, $quantity);
                if(!$product_check->check_quantity()){
                    throwMessage(FAILED_ORDER, "ID: {$productId}, Color: {$color}. Ordered not enough quantity {$quantity}");
                    die();
                }
            }
            
            $orders = new orders($connect, null, $name, $address, $ward, $district, $city, $phone, $shipping_fee, $discount_id, $total_price, $note, $delivery_type, $payment_type);
            $order_id = $orders->order();
            if ($order_id == -1){
                throwMessage(FAILED_ORDER, "Order Unsuccessfully, Can't create order");
            }else{
                foreach ($data->product as $product) {
                    $productId = $product->productId;
                    $color = $product->color;
                    $quantity = $product->quantity;
                    $price = $product->price;

                    $order_detail = new order_detail($connect, $order_id, $productId, $color, $quantity, $price);
                    if(!$order_detail->add()){
                        throwMessage(FAILED_ORDER, "Order Unsuccessfully, Can't add order detail");
                    }
                    $product_update = new product_quantity($connect, $productId, $color, $quantity);
                    $product_update->update_sold_order();

                    $discount->update_quantity();
                }
                throwMessage(SUCCESS_RESPONSE,"Order Successfully");
            }
        } else {
            throwMessage(INVALID_DATA_INPUT, "Product or Data Invalid");
        }
        
}else{
    throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
}



?>