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
        $mail->Subject = "Password Reset from Techshop";
        $mail->Body = <<<END

        

        <!DOCTYPE html>
        <html>
        <head>
            <title>Password Reset</title>
        </head>
        <body>
        <div style="max-width:525px;margin:0 auto;text-align:center;padding:0 4px 16px">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="center" valign="top" bgcolor="#ffffff" style="padding: 27px 20px 0 15px; font-family: Helvetica, Arial, sans-serif; height: 100%!important;">
                        <p style="text-align: left; margin: 0;">
                            <img src="https://img.upanh.tv/2023/11/30/techShopLogo.jpg" width="70" height="auto" alt="TechShop Logo" title="" style="width: 70px; height: auto; border: 0; line-height: 100%; outline: none; text-decoration: none;" class="CToWUd">
                        </p>
                    </td>
                </tr>
                <tr>
                    <td align="left" valign="top" bgcolor="#ffffff" style="padding: 40px 20px; color: #353740; text-align: left; line-height: 1.5; font-family: Helvetica, Arial, sans-serif;">
                        <h1 style="color: #202123; font-size: 32px; margin: 0 0 20px;">Password Reset</h1>
                        <p>We received a request to reset your TechShop password. Click "Reset Password" to create a new password. Please set a new password immediately.</p>
                        <p style="margin: 24px 0 0; text-align: left;">
                            <a href="https://techshopui.vercel.app/forget/reset?token=$token" style="display: inline-block; text-decoration: none; background: #10a37f; border-radius: 3px; color: white; font-family: Helvetica, sans-serif; font-size: 16px; line-height: 24px; font-weight: 400; padding: 12px 20px 11px; margin: 0px;" target="_blank">
                                Reset Password
                            </a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td align="left" valign="top" bgcolor="#ffffff" style="padding: 0 20px 20px; color: #6e6e80; font-size: 13px; line-height: 1.4; text-align: left; background: #ffffff;">
                        <p>If you are having any issues with your account, please contact us at <a href="mailto:suppore@techshop.com" target="_blank">support@example.com</a></p>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        </div>
        
        

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