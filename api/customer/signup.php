<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers:Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");
    include_once("../../config/db_azure.php");
    include_once("../../model/customer.php");

    $db = new db();
    $connect = $db->connect();

    $customer = new customer($connect);

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $data = json_decode(file_get_contents("php://input"));
        $customer->email = $data->email;
        $customer->name = $data->name;
        $customer->password = $data->password;  
        $customer->phone = $data->phone;
        $customer->gender = $data->gender;
        $customer->birthday = $data->birthday;
        $customer->address = $data->address;
        $customer->quarter = $data->quarter;
        $customer->district = $data->district;
        $customer->city = $data->city;

        if ($customer->find()){
            if($customer->signup()){
                echo json_encode([
                    'status' => 200,
                    'message' => 'User add Successfully',
                ]);
            }else{
                echo json_encode([
                    'status' => 400,
                    'message' => 'Server Problem',
                ]);
            }
        }else{
            echo json_encode([
                'status' => 400,
                'message' => 'Email already Exists',
            ]);
        }
    }else{
        echo json_encode([
            'status' => 400,
            'message' => 'Access Denied',
        ]);
    }

?>