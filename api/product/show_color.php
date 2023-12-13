<?php 
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");
include_once("../../config/db_azure.php");
include_once("../../model/product.php");

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
}

$db = new db();
$connect = $db->connect();

$product = new product($connect);

$id = isset($_GET["id"]) ? $_GET["id"] : die();
$product->setId($id);

$show_color = $product->show_color();
$row = $show_color->rowCount();

if($row>0){
    $color_array = [];
    $color_array['color'] = [];
    foreach($show_color as $row){
        extract($row);
        
        array_push($color_array['color'], $COLOR);
    }
    $json_data = json_encode($color_array, JSON_PRETTY_PRINT);
    echo $json_data;

}else{
    throwMessage(NOT_FOUND, "Not found color of product id: {$id}");
}






?>