<?php
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Method:POST');
    header("Content-Type: application/json");
    header("Access-Control-Allow-Headers:Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");
    include_once("../../../../model/parameter_phone.php");
    include_once("../../../../config/db_azure.php");
    include_once("../../../../constants.php");
    date_default_timezone_set('Asia/Ho_Chi_Minh');


    $db = new db();
    $conn = $db -> connect();

    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
    }

    $data= json_decode(file_get_contents("php://input", true));

    $brand = null; $os = null; $price = null; $ram = null; $rom = null; $charger = null;
    
    if(isset($data->brand)){
        if (strpos($data->brand, '-') === false) {
            $brand = $data->brand;
        } else {
            $brand = explode("-", $data->brand);
        }
    }
        
    if(isset($data->os)){
        if (strpos($data->os, '-') === false) {
            $os = $data->os;
        } else {
            $os = explode("-", $data->os);
        }
    }

    $price = isset($data->price) ? explode("-", $data->price) : null;
    
    if(isset($data->ram)){
        if (strpos($data->ram, '-') === false) {
            $ram = $data->ram;
        } else {
            $ram = explode("-", $data->ram);
        }
    }

    if(isset($data->rom)){
        if (strpos($data->rom, '-') === false) {
            $rom = $data->rom;
        } else {
            $rom = explode("-", $data->rom);
        }
    }

    if(isset($data->charger)){
        if (strpos($data->charger, '-') === false) {
            $charger = $data->charger;
        } else {
            $charger = explode("-", $data->charger);
        }
    }


    $phone = new parameter_phone($conn);
    $sort = $phone->sort($brand, $os, $price, $ram, $rom, $charger);
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