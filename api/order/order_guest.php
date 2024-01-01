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

        $email = $data->infor->email;
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
                if (sendSuccesEmail($data, $order_id) == true){
                    throwMessage(SUCCESS_RESPONSE,"Order Successfully");
                }else{
                    throwMessage(FAILED_ORDER, "Send Email unsuccessfully" );
                }
                
                
                throwMessage(SUCCESS_RESPONSE,"Order Successfully");
            }
        } else {
            throwMessage(INVALID_DATA_INPUT, "Product or Data Invalid");
        }
        
}else{
    throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
}

function sendSuccesEmail($data, $orderId){

    $url_ward = 'http://localhost/restful_php_api/api/order/success_mail/send_success_email.php';
    $jsonData = json_encode([
        'orderId' => $orderId,
        'data'=> $data
    ]);

    $curlHandle = curl_init($url_ward);

    curl_setopt_array($curlHandle, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $jsonData,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json']
    ]);

    $response = curl_exec($curlHandle);

    if ($response) {
        $responseData = json_decode($response);
        $status = $responseData->status;
        if ($status == 200){
            return true;
        }else{
            return false;
        }
    } else {
    }
    curl_close($curlHandle);

}

?>