<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers:*");
    
    
    include_once("../../config/db_azure.php");
    include_once("../../model/customer.php");
    include_once("../../constants.php");
    include_once("../../vendor/autoload.php");   

    
    use \Firebase\JWT\JWT;

    $db = new db();
    $connect = $db -> connect();

    $customer = new customer($connect);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = json_decode(file_get_contents("php://input"));
        $customer->email = htmlentities($data->email);

        $login = $customer->login();
        $num = $login->rowCount();
        if ($num>0){
            foreach($login as $row){
                    extract($row);
                    $password_input = htmlentities($data->password);
                    if(hash("sha256", $password_input) != $PASSWORD){
                        
                        $message = 'Email or Password is incorrect.';
                        throwMessage(INVALID_USER_PASS, $message);

                    }else if ($STATUS != "active"){
                        echo json_encode([
                            'status' => USER_NOT_ACTIVE,
                            'message' => 'User is not activated. Please contact to admin.',
                        ]);
                            
                    }else{
                        $payload = [
                            'iat' => time(),
                            'iss' => 'localhost',
                            'exp' => time() + (60*60),
                            'data' => [
                                'email' => $EMAIL,
                                'name' => $NAME
                            ]
                        ];
                        $jwt = JWT::encode($payload, SECRET_KEY, 'HS256');
                        echo json_encode([
                            'status' => SUCCESS_RESPONSE,
                            'jwt' => $jwt,
                            'message' => 'Login Successfully'
                        ]);

                    }
            }
        }else{
            throwMessage(INVALID_USER_PASS, 'Email or Password is incorrect.');
        }
    }else {
        throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
    }
    
    
?>