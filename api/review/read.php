<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: *");
include_once("../../config/db_azure.php");
include_once("../../model/review.php");
include_once("../../vendor/autoload.php");
include_once("../../constants.php");


if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
}

$db = new db();
$connect = $db->connect();

$data = json_decode(file_get_contents("php://input"));
$product_id = $data->product_id;

$review = new review($connect);
$review->setProductId($product_id);

$read = $review->getByProductId();
$row = $read->rowCount();

if($row > 0){
    $review_array = [];
    $review_array['review'] = [];
    foreach($read as $row){
        extract($row);

        $review_item = array(
            'id' => $ID,
            'customer_email' => $CUSTOMER_EMAIL,
            'name' => $NAME,
            'rating' => $RATING,
            'content' => $CONTENT,
            'admin_reply' => $ADMIN_REPLY,
            'created_at' => $CREATED_AT,
            'updated_at' => $UPDATED_AT
        );
        array_push($review_array['review'], $review_item);

    }
    $json_data = json_encode($review_array, JSON_PRETTY_PRINT);
    echo $json_data;
}else{
    throwMessage(NOT_FOUND, "Review of product does not exist");
}



?>