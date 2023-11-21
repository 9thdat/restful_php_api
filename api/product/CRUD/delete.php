<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: DELETE");
    header("Access-Control-Allow-Headers:Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");
    include_once("../../config/db_azure.php");
    include_once("../../model/category.php");

    $db = new db();
    $connect = $db->connect();

    $category = new category($connect);

    $data = json_decode(file_get_contents("php://input"));
    $category->id = $data-> id;
    

    if($category->delete()){
        echo json_encode(array('message', 'category deleted'));
    }else{
        echo json_encode(array('message', 'category not deleted'));
    }


?>