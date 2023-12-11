<?php
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Method:POST');
    header("Content-Type: application/json");
    header("Access-Control-Allow-Headers:Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");
    include_once("../../../config/db_azure.php");
    include_once("../../../constants.php");
    date_default_timezone_set('Asia/Ho_Chi_Minh');


    $db = new db();
    $conn = $db -> connect();

    $data = json_decode(file_get_contents("php://input", true));
    $email = $data->email;


    $token = bin2hex(random_bytes(16));

    $token_hash = hash("sha256", $token);

    $expiry = date("Y-m-d H:i:s", time() + 60 * 30);


    $query = "UPDATE customer
            SET reset_token_hash = ?,
                reset_token_expires_at = ?
            WHERE email = ?";

    $stmt = $conn->prepare($query);

    $stmt->bindParam(1, $token_hash);
    $stmt->bindParam(2, $expiry);
    $stmt->bindParam(3, $email);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {

        $mail = require __DIR__ . "/mailer.php";

        // $mail->setFrom("noreply@example.com");
        $mail->addAddress($email);
        $mail->Subject = "Password Reset";
        $mail->Body = <<<END

        Click <a href="http://localhost/restful_php_api/api/customer/reset_password/reset_password.php?token=$token">here</a> 
        to reset your password.

        END;

        try {

            $mail->send();

        } catch (Exception $e) {

            throwMessage(SEND_EMAIL_ERROR, "{$mail->ErrorInfo}");

        }
        throwMessage(SUCCESS_RESPONSE, "Message sent, please check your inbox.");
    }else{
        throwMessage(INVALID_EMAIL, "Email is not registered");
    }

    


?>