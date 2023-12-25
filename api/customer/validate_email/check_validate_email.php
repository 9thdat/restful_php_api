<?php
header("Access-Control-Allow-Origin:*");
header('Access-Control-Allow-Method:POST');
header("Content-Type: application/json");
header("Access-Control-Allow-Headers:Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");
include_once("../../../config/db_azure.php");
include_once("../../../constants.php");
date_default_timezone_set('Asia/Ho_Chi_Minh');


$db = new db();
$conn = $db->connect();


$data = json_decode(file_get_contents("php://input", true));

$email = $data->email;
$key = $data->key;


$key_hash = hash("sha256", $key);


$query = "SELECT VALIDATE_KEY_EXPIRES_AT FROM customer_validate_email
        WHERE validate_key_hash = ? and email = ?";

$stmt = $conn->prepare($query);

$stmt->bindParam(1, $key_hash);
$stmt->bindParam(2, $email);

$stmt->execute();

$num = $stmt->rowCount();
if ($num>0){
    foreach($stmt as $row){
        extract($row);
        if (strtotime($VALIDATE_KEY_EXPIRES_AT) <= time()) {
            throwMessage(TIME_EXPIRED, "Key has expired");
            die();
        }
        
        throwMessage(SUCCESS_RESPONSE, "Email authentication successful");
    }
}else{
    throwMessage(NOT_FOUND, "Key incorrect");
}
?>