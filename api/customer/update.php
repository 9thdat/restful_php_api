<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: PUT");
    header("Access-Control-Allow-Headers:*");
    include_once("../../config/db_azure.php");
    include_once("../../model/customer.php");
    include_once("../../vendor/autoload.php");   
    include_once("../../constants.php");

    use \Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    $db = new db();
    $connect = $db -> connect();
    if ($_SERVER['REQUEST_METHOD'] == "PUT") {
        $customer = new customer($connect); 
        try{
            $allheaders = getallheaders();
            $jwt = $allheaders['Authorization'];

            $customer_data = JWT::decode($jwt, new Key(SECRET_KEY, 'HS256'));
            $data = $customer_data->data;
            
            $email = $data->email;
            $customer->setEmail($email);
            
            $data_update = json_decode(file_get_contents("php://input", true));
            if (isset($data_update->name) && !empty($data_update->name)){
                $customer->setName(validateParameter('name', $data_update->name, STRING, false));
            }
            if (isset($data_update->new_password) && !empty($data_update->new_password)){
                if (isset($data_update->old_password) && !empty($data_update->old_password)){
                    if (checkOldPass($connect, $email, $data_update->old_password)){
                        $customer->setPassword(validateParameter('password', $data_update->new_password, STRING, false));
                    }else{
                        throwMessage(OLD_PASSWORD_NOT_VALID, "Old password incorrect");
                    }
                }
                
            }
            if (isset($data_update->phone) && !empty($data_update->phone)){
                $customer->setPhone(validateParameter('phone', $data_update->phone, INTEGER, false));
            }
            if (isset($data_update->gender) && !empty($data_update->gender)){
                $customer->setGender($data_update->gender);
            }
            if (isset($data_update->address) && !empty($data_update->address)){
                $customer->setAddress(validateParameter('address', $data_update->address, STRING, false));
            }
            if (isset($data_update->ward) && !empty($data_update->ward)){
                $customer->setWard(validateParameter('ward', $data_update->ward, STRING, false));
            }
            if (isset($data_update->district) && !empty($data_update->district)){
                $customer->setDistrict(validateParameter('district', $data_update->district, STRING, false));
            }
            if (isset($data_update->city) && !empty($data_update->city)){
                $customer->setCity(validateParameter('city', $data_update->city, STRING, false));
            }
            if (isset($data_update->image) && !empty($data_update->image)){
                $customer->setImage($data_update->image);
            }

            if ($customer->update()){
                throwMessage(SUCCESS_RESPONSE, "Updated successfully.");
            }else{
                throwMessage(SUCCESS_RESPONSE, "Failed to update.");
            }

        }catch(Exception $e){
            throwMessage(JWT_PROCESSING_ERROR, $e->getMessage());
        }
    }else {
        throwMessage(REQUEST_METHOD_NOT_VALID, 'Access Denied');
    }

function checkOldPass($connect, $email, $oldPass){
    $customer = new customer($connect);
    $customer->setEmail(htmlentities($email));

    $login = $customer->login();
    $num = $login->rowCount();
    if ($num>0){
        foreach($login as $row){
                extract($row);
                if(hash("sha256", $oldPass) == $PASSWORD){
                    return true;
                }
        }
    }
    return false;
}



?>