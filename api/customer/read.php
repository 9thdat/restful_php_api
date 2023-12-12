<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Headers:*");
    include_once("../../config/db_azure.php");
    include_once("../../model/customer.php");
    include_once("../../vendor/autoload.php");   
    include_once("../../constants.php");

    
    use \Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    $db = new db();
    $connect = $db -> connect();

    $customer = new customer($connect); 

    if($_SERVER["REQUEST_METHOD"] == "GET"){
        try{
            $allheaders = getallheaders();
            $jwt = $allheaders['Authorization'];

            $customer_data = JWT::decode($jwt, new Key(SECRET_KEY, 'HS256'));
            $data = $customer_data->data;   
            $email = $data->email;

            $customer->setEmail(htmlentities($data->email));

            $read = $customer->read();
            $num = $read->rowCount();
            if ($num>0){
                foreach($read as $row){
                        extract($row);
                        echo json_encode([
                            'status' => SUCCESS_RESPONSE,
                            'data' => [
                                'email' => $EMAIL,
                                'name' => $NAME,
                                'phone' => $PHONE,
                                'gender' => $GENDER,
                                'birthday' => $BIRTHDAY,
                                'address' => $ADDRESS,
                                'ward' => $WARD,
                                'district' => $DISTRICT,
                                'city' => $CITY,
                                'image' => $IMAGE ? base64_encode($IMAGE) : null,
                                'status' => $STATUS
                            ]
                        ]);
                }
            }

        }catch(Exception$e){
            
            throwMessage(JWT_PROCESSING_ERROR, $e->getMessage());
        }
    }else {
        throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
    }


?>