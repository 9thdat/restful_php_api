<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    include_once("../../config/db_azure.php");
    include_once("../../model/product.php");

    $db = new db();
    $connect = $db->connect();

    $product = new product($connect);
    
    $product->id = isset($_GET["id"]) ? $_GET["id"] : die();

    $product->show_by_id();

    $image_data = base64_encode($product->image);

    $product_array = [];
    $product_array['product'] = [];
    
    $product_item = array(
        'id' => $product->id,
        'name' => $product->name,
        'price' => $product->price,
        'description' => $product->description,
        'category' => $product->category,
        'brand' => $product->brand,
        'pre_discount' => $product->pre_discount,
        'discount_percent' => $product->discount_percent,
        'image' => $image_data, 
        'color' => $product->color
    );
    
    array_push($product_array['product'], $product_item);
    $json_data = json_encode($product_array, JSON_PRETTY_PRINT);
    echo $json_data;
?>