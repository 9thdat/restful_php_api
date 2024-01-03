<?php
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Method:POST');
    header("Content-Type: application/json");
    header("Access-Control-Allow-Headers:Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");
    include_once("../../../../model/parameter_cable.php");
    include_once("../../../../config/db_azure.php");
    include_once("../../../../constants.php");
    date_default_timezone_set('Asia/Ho_Chi_Minh');


    $db = new db();
    $conn = $db -> connect();

    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
    }

    $data= json_decode(file_get_contents("php://input", true));

    $brand = null; $price = null; $input = null; $output = null; $length = null; $charger = null;
    
    if(isset($data->brand) && strlen($data->brand) > 0){
        if (strpos($data->brand, '-') === false) {
            $brand = $data->brand;
        } else {
            $brand = explode("-", $data->brand);
        }
    }
        
    if(isset($data->input) && strlen($data->input) > 0){
        if (strpos($data->input, '-') === false) {
            $input = $data->input;
        } else {
            $input = explode("-", $data->input);
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

    if(isset($data->length) && strlen($data->length) > 0){
        if (strpos($data->length, '-') === false) {
            $length = $data->length;
        } else {
            $length = explode("-", $data->length);
        }
    }

    if(isset($data->charger) && strlen($data->charger) > 0){
        if (strpos($data->charger, '-') === false) {
            $charger = $data->charger;
        } else {
            $charger = explode("-", $data->charger);
        }
    }


    $cable = new parameter_cable($conn);
    $sort = $cable->sort($brand, $price, $input, $output, $length, $charger);
    $num = $sort->rowCount();
    
    if($num > 0){
        $product_array = [];
        $product_array['product'] = [];

        foreach($sort as $row) {
            extract($row);


            $product_item = array(
                'id' => $PRODUCT_ID,
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