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

        $mail->addAddress($email);
        $mail->Subject = "=?UTF-8?B?" . base64_encode("Verify email from techshop") . "?=";
        $mail->Body = <<<END

        <div style="max-width:525px;margin:0 auto;text-align:center;padding:0 4px 16px">
        
        <tr>
            <td style="font-size:0px;padding:0px;word-break:break-word" align="left">
                <table zcellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px">
                    <tbody>
                        <tr>
                            <img alt="" height="48" src="https://img.upanh.tv/2023/11/30/techShopLogo.jpg" style="object-fit:contain;border:0;border-radius:10px;display:block;outline:none;text-decoration:none;height:48px;width:100%;font-size:13px" width="48" class="CToWUd" data-bit="iit">                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>

        <tr>
            <td style="font-size:0px;padding:0px;word-break:break-word" align="center">
                <div style="font-family:system-ui,Segoe UI,sans-serif;font-size:15px;line-height:1.6;text-align:center;color:#333333">Your Techshop verification code is:
                </div>
            </td>
        </tr>
        <tr>
            <td style="font-size:0px;padding:0px;word-break:break-word" align="center">
                <div style="font-family:system-ui,Segoe UI,sans-serif;font-size:19px;font-weight:700;line-height:1.6;text-align:center;color:#333333">$key
                </div>
            </td>
        </tr>
        <tr>
            <td style="font-size:0px;padding:0px;word-break:break-word" align="center">
                <div style="font-family:system-ui,Segoe UI,sans-serif;font-size:15px;line-height:1.6;text-align:center;color:#333333">Don't share this code with anyone; our employees will never ask for the code.
                </div>
            </td>
        </tr>
        
        </div>
        

        END;

        try {

            $mail->send();

        } catch (Exception $e) {

            throwMessage(SEND_EMAIL_ERROR, "{$mail->ErrorInfo}");

        }
        throwMessage(SUCCESS_RESPONSE, "Message sent, please check your inbox.");
    }

    


?>