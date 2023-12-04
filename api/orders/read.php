<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: *");
include_once("../../config/db_azure.php");
include_once("../../model/orders.php");
include_once("../../model/order_detail.php");
include_once("../../vendor/autoload.php");
include_once("../../constants.php");

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

$db = new db();
$connect = $db->connect();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        $orders = new orders($connect);
        $allheaders = getallheaders();
        $jwt = $allheaders['Authorization'];

        $customer_data = JWT::decode($jwt, new Key(SECRET_KEY, 'HS256'));
        $data = $customer_data->data;

        $orders->customer_email = $data->email;

        $read = $orders->read();
        $row = $read->rowCount();

        if ($row > 0) {
            $orders_array['orders'] = [];

            foreach ($read as $row) {
                extract($row);
                $orders_item = array(
                    'id' => $ID,
                    'address' => $ADDRESS,
                    'phone' => $PHONE,
                    'discount' => $DISCOUNT,
                    'shipping_fee' => $SHIPPING_FEE,
                    'total_price' => $TOTAL_PRICE,
                    'order_date' => $ORDER_DATE,
                    'canceled_date' => $CANCELED_DATE,
                    'completed_date' => $COMPLETED_DATE,
                    'delivery_type' => $DELIVERY_TYPE,
                    'payment_type' => $PAYMENT_TYPE,
                    'status' => $STATUS
                );

                $order_detail = new order_detail($connect, $ID);
                $read_oi = $order_detail->read();
                $row2 = $read_oi->rowCount();

                if ($row2 > 0) {
                    $order_detail_array = [];

                    foreach ($read_oi as $row2) {
                        extract($row2);
                        $oi_item = array(
                            'id' => $ID,
                            'order_id' => $ORDER_ID,
                            'product_id' => $PRODUCT_ID,
                            'color' => $COLOR,
                            'quantity' => $QUANTITY,
                            'price' => $PRICE
                        );

                        array_push($order_detail_array, $oi_item);
                    }

                    $orders_item['order_detail'] = $order_detail_array;
                }

                array_push($orders_array['orders'], $orders_item);
            }

            $json_data = json_encode($orders_array, JSON_PRETTY_PRINT);
            echo $json_data;
        }
    } catch (Exception $e) {
        throwMessage(JWT_PROCESSING_ERROR, $e->getMessage());
    }
} else {
    throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
}
?>
