<?php
include_once("../../../config/db_azure.php");
date_default_timezone_set('Asia/Ho_Chi_Minh');

$db = new db();
$conn = $db->connect();

$token = $_POST["token"];

$token_hash = hash("sha256", $token);



$query = "SELECT EMAIL, RESET_TOKEN_EXPIRES_AT FROM customer
        WHERE reset_token_hash = ?";

$stmt = $conn->prepare($query);

$stmt->bindParam(1, $token_hash);

$stmt->execute();

$num = $stmt->rowCount();
$email = null;
if ($num>0){
    foreach($stmt as $row){
        extract($row);
        if (strtotime($RESET_TOKEN_EXPIRES_AT) <= time()) {
            die("token has expired");
        }
        $email = $EMAIL;
        
    }
}else{
    die("token not found");
}


if (strlen($_POST["password"]) < 8) {
    die("Password must be at least 8 characters");
}


if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}


$query = "UPDATE customer
        SET password = SHA2(:password, 256),
            reset_token_hash = NULL,
            reset_token_expires_at = NULL
        WHERE email = :email";

$stmt = $conn->prepare($query);
$stmt->bindParam(":password", $_POST["password"]);
$stmt->bindParam(":email", $email);

$stmt->execute();

echo "Password updated. You can now login.";