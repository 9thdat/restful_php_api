<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    include_once("../../config/db_azure.php");
    include_once("../../model/product.php");
    

    $db = new db();
    $connect = $db->connect();

    $product = new product($connect);
    
    $id = isset($_GET["id"]) ? $_GET["id"] : die();
    $product->setId($id);

    $show_by_id = $product->show_by_id();
    $row = $show_by_id->rowCount();
    
    if ($row > 0){
        $product_array = [];
        $product_array['product'] = [];

        foreach ($show_by_id as $row){
            extract($row);

            $image_data = base64_encode($IMAGE); 

            $product_item = array(
                'id' => $ID,
                'name' => $NAME,
                'price' => $PRICE,
                'description' => $DESCRIPTION,
                'category' => $CATEGORY,
                'brand' => $BRAND,
                'pre_discount' => $PRE_DISCOUNT,
                'discount_percent' => $DISCOUNT_PERCENT,
                'image' => $image_data
            );
            array_push($product_array['product'], $product_item);
        }
        $json_data = json_encode($product_array, JSON_PRETTY_PRINT);
        echo $json_data;
    }
    else{
        throwMessage(NOT_FOUND, "NOT FOUND");
    }
?>