<?php
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");
include_once("../../config/db_azure.php");
include_once("../../model/product.php");
include_once("../../model/parameter_adapter.php");
include_once("../../model/parameter_backupcharger.php");
include_once("../../model/parameter_cable.php");
include_once("../../model/parameter_phone.php");
include_once("../../constants.php");

$db = new db();
$conn = $db -> connect();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    //$data = json_decode(file_get_contents("php://input"));
    $product = new product($conn);
    $id = isset($_GET["id"]) ? $_GET["id"] : die();
   
    $product->setId($id);

    if (!$product->find()) {
        throwMessage(NOT_FOUND,"Product does not exist");
    }

    $categoryName = $product->getCategorybyId();

    $paramClass = "parameter_" . strtolower($categoryName);
    $param = new $paramClass($conn);
    $param->setProductId($id);
    $getByProductId = $param->getByProductId();

    $num = $getByProductId->rowCount();

    if ($num > 0) {
        echo json_encode([
            'status' => SUCCESS_RESPONSE,
            'data' => responseData($getByProductId, $categoryName),
        ]);
    } else {
        throwMessage(NOT_FOUND, "Not found param with product id:{$id}");
    }
} else {
    throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
}




function responseData($getByProductId, $categoryName) {
    $responseData = [];

    foreach ($getByProductId as $row) {
        extract($row);

        if ($categoryName == "phone"){
            $commonFields = [
                'product_id' => $PRODUCT_ID
            ];
        }else{
            $commonFields = [
                'product_id' => $PRODUCT_ID,
                'madein' => $MADEIN,
                'brandof' => $BRANDOF,
                'brand' => $BRAND
            ];
        }

        switch ($categoryName) {
            case 'phone':
                $responseData[] = array_merge($commonFields, [
                    'screen' => $SCREEN,
                    'operating_system' => $OPERATING_SYSTEM,
                    'back_camera' => $BACK_CAMERA,
                    'front_camera' => $FRONT_CAMERA,
                    'chip' => $CHIP,
                    'ram' => $RAM,
                    'rom' => $ROM,
                    'sim' => $SIM,
                    'battery_charger' => $BATTERY_CHARGER
                ]);
                break;

            case 'adapter':
                $responseData[] = array_merge($commonFields, [
                    'model' => $MODEL,
                    'function' => $FUNCTION,
                    'input' => $INPUT,
                    'output' => $OUTPUT,
                    'maximum' => $MAXIMUM,
                    'size' => $SIZE,
                    'tech' => $TECH
                ]);
                break;

            case 'cable':
                $responseData[] = array_merge($commonFields, [
                    'tech' => $TECH,
                    'function' => $FUNCTION,
                    'input' => $INPUT,
                    'output' => $OUTPUT,
                    'length' => $LENGTH,
                    'maximum' => $MAXIMUM
                ]);
                break;

            case 'backupcharger':
                $responseData[] = array_merge($commonFields, [
                    'efficiency' => $EFFICIENCY,
                    'capacity' => $CAPACITY,
                    'time_full_charge' => $TIMEFULLCHARGE,
                    'input' => $INPUT,
                    'output' => $OUTPUT,
                    'core' => $CORE,
                    'tech' => $TECH,
                    'size' => $SIZE,
                    'weight' => $WEIGHT
                ]);
                break;
        }
    }

    return $responseData;
}
?>
