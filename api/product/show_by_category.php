<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    include_once("../../config/db_azure.php");
    include_once("../../model/product.php");

    $db = new db();
    $connect = $db->connect();

    $product = new product($connect);
    $product->category = isset($_GET["category"]) ? $_GET["category"] : die();

    $show_by_category = $product->show_by_category();

    $num = $show_by_category->rowCount();

    if ($num > 0) {
        $product_array = [];
        $product_array['product'] = [];

        while ($row = $show_by_category->fetch(PDO::FETCH_ASSOC)) {
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
                'image' => $image_data, // Trả về dữ liệu hình ảnh dưới dạng base64
                'color' => $COLOR
            );
            array_push($product_array['product'], $product_item);
            // print_r(json_encode($product_item). "\n"); 
        }
        $json_data = json_encode($product_array, JSON_PRETTY_PRINT);
        echo $json_data;

    }else {
        http_response_code(404); 
        echo json_encode(array("message" => "404 NOT FOUND"), JSON_PRETTY_PRINT);
    }

?>