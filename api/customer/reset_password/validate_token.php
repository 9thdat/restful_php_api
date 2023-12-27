<?php

use GrahamCampbell\ResultType\Success;

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

$token_hash = hash("sha256", $token);

$query = "SELECT EMAIL, RESET_TOKEN_EXPIRES_AT FROM customer
        WHERE reset_token_hash = ? ";

$stmt = $conn->prepare($query);
$stmt->bindParam(1, $token_hash);

$stmt->execute();

$num = $stmt->rowCount();
$email = null;
if ($num>0){
    foreach($stmt as $row){
        extract($row);
        if (strtotime($RESET_TOKEN_EXPIRES_AT) <= time()) {
            throwMessage(TIME_EXPIRED, "Token has expired");
        }
        
    }

    if ($stmt->rowCount()>0){
        throwMessage(SUCCESS_RESPONSE, "Valid Tokens");
    }

}else{
    throwMessage(NOT_FOUND, "Invalid Token");
}




