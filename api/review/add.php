<?php 
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers:*");
include_once("../../config/db_azure.php");
include_once("../../model/review.php");
include_once("../../vendor/autoload.php");
include_once("../../constants.php");

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
}

$db = new db();
$connect = $db->connect();
try{
    $allheaders = getallheaders();
    $jwt = $allheaders['Authorization'];

    $customer_data = JWT::decode($jwt, new Key(SECRET_KEY, 'HS256'));
    $data = $customer_data->data;
    $email = $data->email;

    $data_update = json_decode(file_get_contents("php://input"));
    $product_id = $data_update->product_id;
    $rating = $data_update->rating;
    $content = $data_update->content;

    $review = new review($connect, $product_id, $email, $rating, $content);

    if ($review->add()){
        throwMessage(SUCCESS_RESPONSE, "Add review successfully.");

    }else{
        throwMessage(SUCCESS_RESPONSE, "Failed to add.");
        
    }
}catch(Exception $e){
    throwMessage(JWT_PROCESSING_ERROR, $e->getMessage());
}





?>