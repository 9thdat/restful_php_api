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

if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
}

$data = json_decode(file_get_contents("php://input"));

$token = $data->token;
$password = $data->password;

$token_hash = hash("sha256", $token);


$query = "SELECT EMAIL, RESET_TOKEN_EXPIRES_AT FROM customer
        WHERE reset_token_hash = ? ";

$stmt = $conn->prepare($query);
$stmt->bindParam(1, $token_hash);
$stmt->execute();

$num = $stmt->rowCount();

if ($num>0){
    foreach($stmt as $row){
        extract($row);
        if (strtotime($RESET_TOKEN_EXPIRES_AT) <= time()) {
            throwMessage(TIME_EXPIRED, "Token has expired");
        }
    }

    $query2 = "UPDATE customer
            SET password = SHA2(:password, 256),
                reset_token_hash = NULL,
                reset_token_expires_at = NULL
            WHERE reset_token_hash = :token_hash ";

    $stmt2 = $conn->prepare($query2);
    $stmt2->bindParam(":password", $password);
    $stmt2->bindParam(":token_hash", $token_hash);
    $stmt2->execute();

    if ($stmt2->rowCount()>0){
        throwMessage(SUCCESS_RESPONSE, "Change New Password Successful");
    }

}else{
    throwMessage(NOT_FOUND, "Token not found");
}


