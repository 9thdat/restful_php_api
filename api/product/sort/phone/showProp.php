<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include_once("../../../../config/db_azure.php");
include_once("../../../../model/product.php");
include_once("../../../../model/parameter_phone.php");
include_once("../../../../constants.php");

$db = new db();
$conn = $db->connect();

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
}

$phone = new parameter_phone($conn);
$prop = isset($_GET["prop"]) ? $_GET["prop"] : die();

$data = array();

switch ($prop) {
    case "brand":
        $showBrand = $phone->showBrand();
        $num = $showBrand->rowCount();
        if ($num > 0) {
            foreach ($showBrand as $row) {
                extract($row);
                array_push($data, $BRAND);
            }
        }
        break;
    case "os":
        array_push($data, "Android");
        array_push($data, "iOS");
        break;
    case "ram":
        $showRam = $phone->showRam();
        $num = $showRam->rowCount();
        if ($num > 0) {
            foreach ($showRam as $row) {
                extract($row);
                array_push($data, $RAM);
            }
        }
        break;
    case "rom":
        $showRom = $phone->showRom();
        $num = $showRom->rowCount();
        if ($num > 0) {
            foreach ($showRom as $row) {
                extract($row);
                array_push($data, $ROM);
            }
        }
        break;
    case "charger":
        array_push($data, "Dưới 15 w");
        array_push($data, "Từ 15-25w");
        array_push($data, "Từ 26-60w");
        array_push($data, "Trên 60w");
        break;
    default:
        throwMessage(404, "NOT FOUND");
}

echo json_encode([
    'status' => SUCCESS_RESPONSE,
    'data' => $data,
]);


?>
