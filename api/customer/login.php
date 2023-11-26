<?php 
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Method:POST');
    header("Content-Type: application/json");
    include_once("../../config/db_azure.php");
    include_once("../../model/customer.php");
    include_once("../../vendor/autoload.php");   
    
    use \Firebase\JWT\JWT;

    $db = new db();
    $connect = $db -> connect();

    $customer = new customer($connect);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = json_decode(file_get_contents("php://input", true));
        $customer->email = htmlentities($data->email);

        $login = $customer->login();
        $num = $login->rowCount();
        if ($num>0){
            foreach($login as $row){
                    extract($row);
                    $password_input = htmlentities($data->password);
                    if(hash("sha256", $password_input) != $PASSWORD){
                        echo json_encode([
                            'status' => 404,
                            'message' => 'Email or Password is incorrect.',
                        ]);

                    }else if ($STATUS != "active"){
                        echo json_encode([
                            'status' => 404,
                            'message' => 'User is not activated. Please contact to admin.',
                        ]);
                    }else{
                        $payload = [
                            'iat' => time(),
                            'iss' => 'localhost',
                            'exp' => time() + (10*60),
                            'data' => [
                                'email' => $EMAIL,
                                'name' => $NAME
                            ]
                        ];
                        $secret_key = "techshop";
                        $jwt = JWT::encode($payload, $secret_key, 'HS256');
                        echo json_encode([
                            'status' => 1,
                            'jwt' => $jwt,
                            'message' => 'Login Successfully'
                        ]);

                    }
            }
        }else{
            echo json_encode([
                'status' => 404,
                'message' => 'Email or Password is incorrect.',
            ]);
        }
    }else {
        echo json_encode([
        'status' => 0,
        'message' => 'Access Denied',
    ]);
    }
    
    
?>