<?php
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    include_once("../../config/db_azure.php");
    include_once("../../model/category.php");
    include_once("../../constants.php");

    $db = new db();
    $connect = $db->connect();

    $category = new category($connect);
    // $category->category_name = isset($_GET["categoryName"]) ? $_GET["categoryName"] : null;
    
    $showall = $category->showall();
    $num = $showall->rowCount();

    if($num >0){
        $category_array = [];
        $category_array['category'] = [];
        foreach($showall as $row){
            extract($row);

            $category_item = array(
                'name' => $NAME
            );
            array_push($category_array['category'], $category_item);
        }
        $json_data = json_encode($category_array, JSON_PRETTY_PRINT);
        echo $json_data;
    }else{
        throwMessage(NOT_FOUND, "NOT FOUND");
    }





?>