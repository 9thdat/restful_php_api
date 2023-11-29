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
    
    $query = "SELECT * FROM customer WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0){
        throwMessage(INVALID_EMAIL, "Email already exists");
        die();
    }

    $query = "SELECT * FROM customer_validate_email WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0){
        $query = "DELETE FROM customer_validate_email WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();
    }


    $key = rand(100000, 999999);

    $key_hash = hash("sha256", $key);

    $expiry = date("Y-m-d H:i:s", time() + 60 * 30);



    $query = "INSERT INTO customer_validate_email(email, validate_key_hash, validate_key_expires_at)
              VALUES (?,?,?)";

    $stmt = $conn->prepare($query);

    $stmt->bindParam(1, $email);
    $stmt->bindParam(2, $key_hash);
    $stmt->bindParam(3, $expiry);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {

        $mail = require __DIR__ . "/mailer.php";

        // $mail->setFrom("noreply@example.com");
        $mail->addAddress($email);
        $mail->Subject = "=?UTF-8?B?" . base64_encode("Xác minh email từ techshop") . "?=";
        $mail->Body = <<<END

        Mã xác minh của bạn là: $key.

        END;

        try {

            $mail->send();

        } catch (Exception $e) {

            throwMessage(SEND_EMAIL_ERROR, "{$mail->ErrorInfo}");

        }
        throwMessage(SUCCESS_RESPONSE, "Message sent, please check your inbox.");
    }

    


?>