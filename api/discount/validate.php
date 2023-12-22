<?php
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST");
include_once("../../config/db_azure.php");
include_once("../../model/discount.php");
include_once("../../constants.php");
date_default_timezone_set('Asia/Ho_Chi_Minh');

$currentDate = date("Y-m-d", time());

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
}

$db = new db();
$connect = $db->connect();
$discount = new discount($connect);

$data = json_decode(file_get_contents("php://input"));
$code = $data->code;
$total_price = $data->total_price;

$discount->setCode($code);

$validate = $discount->validate();
$row = $validate->rowCount();


if ($row < 1) {
    throwMessage(DISCOUNT_NOT_VALID, "Discount does not exist");
}

foreach ($validate as $row) {
    extract($row);

    if ($STATUS == "disabled") {
        throwMessage(DISCOUNT_NOT_VALID, "Discount code is not available");
    }

    if ($QUANTITY < 1) {
        throwMessage(DISCOUNT_NOT_VALID, "Discount code has exceeded the maximum usage limit.");
    }

    if ($currentDate > $END_DATE || $STATUS == "expired") {
        throwMessage(DISCOUNT_NOT_VALID, "Discount code has expired, expired at {$END_DATE}");
    }

    if ($currentDate < $START_DATE) {
        throwMessage(DISCOUNT_NOT_VALID, "Discount code has not started yet, it begins on {$START_DATE}");
    }

    if ($total_price < $MIN_APPLY) {
        throwMessage(DISCOUNT_NOT_VALID, "Order has not reached the minimum value");
    }

    $discount_value = ($TYPE == "percent") ? $total_price * $VALUE : $VALUE;

    echo json_encode([
        'status' => SUCCESS_RESPONSE,
        'data' => [
            'id' => $ID,
            'discount_value' => ($MAX_SPEED != -1) ? ( ($discount_value > $MAX_SPEED) ? $MAX_SPEED : $discount_value ) : $discount_value
        ]
    ]);

    break;
}
?>
