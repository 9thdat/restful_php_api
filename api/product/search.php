<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    include_once("../../config/db_azure.php");
    include_once("../../model/product.php");
    include_once("../../constants.php");

    $db = new db();
    $connect = $db->connect();

    $product = new product($connect);

    $name = isset($_GET["key"]) ? $_GET["key"] : die();
    $product->setName($name);
    
    $search = $product->search();

    $num = $search->rowCount();

    if ($num > 0) {
        $product_array = [];
        $product_array['product'] = [];

        while ($row = $search->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            
            $product_item = array(
                'id' => $ID,
                'name' => $NAME,
                'price' => $PRICE,
                // 'description' => $DESCRIPTION,
                // 'category' => $CATEGORY,
                // 'brand' => $BRAND,
                'pre_discount' => $PRE_DISCOUNT,
                'discount_percent' => $DISCOUNT_PERCENT,
                'image' => base64_encode($IMAGE)
            );
            array_push($product_array['product'], $product_item);
            // print_r(json_encode($product_item). "\n"); 
        }
        $json_data = json_encode($product_array, JSON_PRETTY_PRINT);
        echo $json_data;

    }else {
        http_response_code(404); 
        throwMessage(NOT_FOUND, "NOT FOUND");
    }

?>