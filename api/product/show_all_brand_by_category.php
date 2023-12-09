<?php
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    include_once("../../config/db_azure.php");
    include_once("../../model/product.php");    

    $db = new db();
    $connect = $db->connect();

    $product = new product($connect);

    $category_name = isset($_GET["categoryName"]) ? $_GET["categoryName"] : null;
    $product->setCategoryName($category_name);
    $show_all_brand_by_category = $product->show_all_brand_by_category();

    $num = $show_all_brand_by_category->rowCount();

    if ($num>0){
        $brand_array = [];
        $brand_array['brand'] = [];
        
        foreach($show_all_brand_by_category as $row){
            extract($row);

            $brand_item = array(
                'brand' => $BRAND
            );
            array_push($brand_array['brand'], $brand_item);
        }
        $json_data = json_encode($brand_array, JSON_PRETTY_PRINT);
        echo $json_data;
    }else {
        http_response_code(404); 
        throwMessage(NOT_FOUND, "NOT FOUND");
    }


?>