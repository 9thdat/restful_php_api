<?php 
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Method:POST');
    header("Content-Type: application/json");
    include_once("../../config/db_azure.php");
    include_once("../../model/customer.php");    

    $db = new db();
    $connect = $db -> connect();

    $customer = new customer($connect);

    // $customer->email = isset($_GET["email"])? $_GET["email"] : die();
    // $customer->password = isset($_GET["password"]) ? $_GET["password"] : die();

    $data = json_decode(file_get_contents("php://input", true));
    $customer->email = htmlentities($data->email);

    $login = $customer->login();
    $num = $login->rowCount();

    if ($num > 0){
        foreach($login as $row){
            extract($row);
            $password_input = htmlentities($data->password);
            if(hash("sha256", $password_input) == $PASSWORD){
                http_response_code(200); 
                echo json_encode(array("message" => "200 OK"), JSON_PRETTY_PRINT);

            }else{
                http_response_code(404); 
                echo json_encode(array("message" => "404 NOT FOUND"), JSON_PRETTY_PRINT);
            }
        }
    }else{
        http_response_code(404); 
                echo json_encode(array("message" => "404 NOT FOUNDd"), JSON_PRETTY_PRINT);
    }



    // if($customer->login()){
    //     echo json_encode(array("message" => "200 OK"), JSON_PRETTY_PRINT);
    // }else{
    //     echo json_encode(array("message" => "404 NOT FOUND"), JSON_PRETTY_PRINT);
    // }
    
?>