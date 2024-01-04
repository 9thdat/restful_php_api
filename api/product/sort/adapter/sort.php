<?php
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Method:POST');
    header("Content-Type: application/json");
    header("Access-Control-Allow-Headers:Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");
    include_once("../../../../model/parameter_adapter.php");
    include_once("../../../../config/db_azure.php");
    include_once("../../../../constants.php");
    date_default_timezone_set('Asia/Ho_Chi_Minh');


    $db = new db();
    $conn = $db -> connect();

    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
    }

    $data= json_decode(file_get_contents("php://input", true));

    $brand = null; $price = null; $numberport = null; $output = null; $charger = null;
    
    if(isset($data->brand) && strlen($data->brand) > 0){
        if (strpos($data->brand, '-') === false) {
            $brand = $data->brand;
        } else {
            $brand = explode("-", $data->brand);
        }
    }
        
    if(isset($data->numberport) && strlen($data->numberport) > 0){
        if (strpos($data->numberport, '-') === false) {
            $numberport = $data->numberport;
        } else {
            $numberport = explode("-", $data->numberport);
        }
    }

    $price = isset($data->price) && strlen($data->price) > 0 ? explode("-", $data->price) : null;
    
    if(isset($data->output) && strlen($data->output) > 0){
        if (strpos($data->output, '-') === false) {
            $output = $data->output;
        } else {
            $output = explode("-", $data->output);
        }
    }


    if(isset($data->charger) && strlen($data->charger) > 0){
        if (strpos($data->charger, '-') === false) {
            $charger = $data->charger;
        } else {
            $charger = explode("-", $data->charger);
        }
    }


    $adapter = new parameter_adapter($conn);
    $sort = $adapter->sort($brand, $price, $numberport, $output, $charger);
    $num = $sort->rowCount();
    
    if($num > 0){
        $product_array = [];
        $product_array['product'] = [];

        foreach($sort as $row) {
            extract($row);


            $product_item = array(
                'id' => $ID,
                'name' => $NAME,
                'price' => $PRICE,
                'description' => $DESCRIPTION,
                'category' => $CATEGORY,
                'brand' => $BRAND,
                'pre_discount' => $PRE_DISCOUNT,
                'discount_percent' => $DISCOUNT_PERCENT,
                'image' => base64_encode($IMAGE) 
            );
            array_push($product_array['product'], $product_item);
        }
        $json_data = json_encode($product_array, JSON_PRETTY_PRINT);
        echo $json_data;

    }





?>