<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    include_once("../../config/db_azure.php");
    include_once("../../model/image_detail.php");

    $db = new db();
    $connect = $db->connect();

    $image_detail = new image_detail($connect);
    $product_id = isset($_GET["productid"]) ? $_GET["productid"] : die();
    $color = isset($_GET["color"]) ? $_GET["color"] : null;

    $image_detail->setProductId($product_id);
    $image_detail->setColor($color);

    $show_by_id = $image_detail->show_by_productid();
    $num = $show_by_id->rowCount();

    if ($num > 0){
        $image_array = [];
        $image_array['image'] = [];

        while ($row = $show_by_id->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            $image_item = array(
                'product_id' => $PRODUCT_ID,
                'color' => $COLOR,
                'ordinal' => $ORDINAL,
                'image' => base64_encode($IMAGE)
            );

            array_push($image_array['image'], $image_item);
        }
        $json_data = json_encode($image_array, JSON_PRETTY_PRINT);
        echo $json_data;

    }else{
        http_response_code(404); 
        echo json_encode([
        'status' => 404,
        'message' => 'NOT FOUND',
        ]);
    }

?>