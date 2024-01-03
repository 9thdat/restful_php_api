<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include_once("../../../../config/db_azure.php");
include_once("../../../../model/product.php");
include_once("../../../../model/parameter_cable.php");
include_once("../../../../constants.php");

$db = new db();
$conn = $db->connect();

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
}

$cable = new parameter_cable($conn);
$prop = isset($_GET["prop"]) ? $_GET["prop"] : die();

$data = array();

switch ($prop) {
    case "brand":
        $showBrand = $cable->showBrand();
        $num = $showBrand->rowCount();
        if ($num > 0) {
            foreach ($showBrand as $row) {
                extract($row);
                array_push($data, $BRAND);
            }
        }
        break;
    case "input":
        array_push($data, "Type C");
        array_push($data, "Type A");
        break;
    case "output":
        array_push($data, "Lightning");
        array_push($data, "Type C");
        break;
    case "length":
        array_push($data, "Dưới 1 m");
        array_push($data, "Từ 1 - 2 m");
        break;
    case "charger":
        array_push($data, "Dưới 15 W");
        array_push($data, "Từ 15-25 W");
        array_push($data, "Từ 26-60 W");
        array_push($data, "Trên 60 W");
        break;
    default:
        throwMessage(404, "NOT FOUND");
}

echo json_encode([
    'status' => SUCCESS_RESPONSE,
    'data' => $data,
]);


?>
