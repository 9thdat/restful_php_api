<?php 
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Method:POST');
    header("Content-Type: application/json");
    header("Access-Control-Allow-Headers:Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");
    include_once("../../config/db_azure.php");
    include_once("../../model/customer.php");
    include_once("../../vendor/autoload.php");   
    include_once("../../constants.php");
    
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
                        // echo json_encode([
                        //     'status' => INVALID_USER_PASS,
                        //     'message' => 'Email or Password is incorrect.',
                        // ]);
                        http_response_code(INVALID_USER_PASS);
                        $message = 'Email or Password is incorrect.';
                        throwMessage(INVALID_USER_PASS, $message);

                    }else if ($STATUS != "active"){
                        http_response_code(USER_NOT_ACTIVE);
                        echo json_encode([
                            'status' => USER_NOT_ACTIVE,
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
                        $jwt = JWT::encode($payload, SECRET_KEY, 'HS256');
                        http_response_code(SUCCESS_RESPONSE);
                        echo json_encode([
                            'status' => SUCCESS_RESPONSE,
                            'jwt' => $jwt,
                            'message' => 'Login Successfully'
                        ]);

                    }
            }
        }else{
            http_response_code(INVALID_USER_PASS);
            echo json_encode([
                'status' => INVALID_USER_PASS,
                'message' => 'Email or Password is incorrect.',
            ]);
        }
    }else {
        http_response_code(REQUEST_METHOD_NOT_VALID);
        echo json_encode([
        'status' => REQUEST_METHOD_NOT_VALID,
        'message' => 'Access Denied',
    ]);
    }
    
    
?>