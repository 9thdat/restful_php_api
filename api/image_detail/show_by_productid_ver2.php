<?php 
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    include_once("../../config/db_azure.php");
    include_once("../../model/image_detail.php");

    $db = new db();
    $connect = $db->connect();

    $image_detail = new image_detail($connect);
    $product_id = isset($_GET["productid"]) ? $_GET["productid"] : die();

    $show_color = fetchProductColors($image_detail, $product_id);
    $num = $show_color->rowCount();

    if ($num === 0) {
        handleNotFound();
    }

    $image_array = [];
    $image_array['productId'] = $product_id;
    $image_array['color']= [];

    foreach ($show_color as $row) {
        extract($row);
        $color = $COLOR;

        $show_by_id = fetchImagesByColor($image_detail, $product_id, $color);
        $num_images = $show_by_id->rowCount();

        $image_array['color'][$color] = [];

        if ($num_images > 0) {
            $image_array['color'][$color]['images'] = [];
            foreach ($show_by_id as $row) {
                extract($row);
                if ($ORDINAL == -1) {
                    $image_array['color'][$color]['thumbnail'] = base64_encode($IMAGE);
                } else {
                    array_push($image_array['color'][$color]['images'], base64_encode($IMAGE));
                }
            }
        }
    }

    $json_data = json_encode($image_array, JSON_PRETTY_PRINT);
    echo $json_data;


    function handleNotFound() {
        http_response_code(404);
        echo json_encode([
            'status' => 404,
            'message' => 'NOT FOUND',
        ]);
        exit(); 
    }

    function fetchProductColors($image_detail, $product_id) {
        $image_detail->setProductId($product_id);
        return $image_detail->show_color_by_productid();
    }

    function fetchImagesByColor($image_detail, $product_id, $color) {
        $image_detail->setProductId($product_id);
        $image_detail->setColor($color);
        return $image_detail->show_by_productid();
    }

?>
