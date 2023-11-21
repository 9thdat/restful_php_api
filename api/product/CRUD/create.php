<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers:Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");
    include_once("../../config/db_azure.php");
    include_once("../../model/category.php");

    $db = new db();
    $connect = $db->connect();

    $category = new category($connect);

    $data = json_decode(file_get_contents("php://input"));
    $category->id = $data-> id;
    $category->name = $data-> name;
    

    if($category->create()){
        echo json_encode(array('message', 'category created'));
    }else{
        echo json_encode(array('message', 'category not created'));
    }


?>