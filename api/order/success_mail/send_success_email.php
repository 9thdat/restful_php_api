<?php
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Method:POST');
    header("Content-Type: application/json");
    header("Access-Control-Allow-Headers:Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");
    include_once("../../../model/product.php");
    include_once("../../../config/db_azure.php");
    include_once("../../../constants.php");
    date_default_timezone_set('Asia/Ho_Chi_Minh');


    $db = new db();
    $conn = $db -> connect();

    $dataInput = json_decode(file_get_contents("php://input", true));
    $data = $dataInput->data;
    $orderId = $dataInput->orderId;
    $email = $data->infor->email;
    $name = $data->infor->name;
    $address = $data->infor->address;
    $ward = $data->infor->ward;
    $district = $data->infor->district;
    $city = $data->infor->city;
    $phone = $data->infor->phone;
    $shipping_fee = $data->infor->shipping_fee;
    $discount_code = $data->infor->discount_code;
    $total_price = $data->infor->total_price;
    $note = $data->infor->note;
    $delivery_type = $data->infor->delivery_type;
    $payment_type = $data->infor->payment_type;

    $total_price_format = number_format($total_price, 0, ',', '.');
    $tbody = "";

    if (isset($data->product) && is_array($data->product)) {
        foreach ($data->product as $product) {
            $productId = $product->productId;
            $productQuery = new product($conn);
            $productQuery->setId($productId);
            $productName = $productQuery->show_name_by_id();

            $color = $product->color;
            $quantity = $product->quantity;
            $price = $product->price; 
            $totalPrice = $quantity * $price; 

            $priceFormatted = number_format($price, 0, ',', '.');
            $totalPriceFormatted = number_format($totalPrice, 0, ',', '.');

            $tbody_data = "<tr>
                            <td>$productId</td>
                            <td>$productName</td>
                            <td>$color</td>
                            <td>$quantity</td>
                            <td>$priceFormatted VNĐ</td>
                            <td>$totalPriceFormatted VNĐ</td>
                          </tr>";
            $tbody = $tbody . $tbody_data . " ";
        }
    }



        $mail = require __DIR__ . "/mailer.php";

        $mail->addAddress($email);
        $mail->Subject = "=?UTF-8?B?" . base64_encode("Xác nhận đơn hàng từ TechShop") . "?=";
        $mail->Body = <<<END

        <!DOCTYPE html>
        <html>
        <head>
          <title>Xác nhận đơn hàng</title>
          <style>
            body {
              font-family: Arial, sans-serif;
              line-height: 1.6;
              margin: 0;
              padding: 0;
              background-color: #f4f4f4;
            }
            .container {
              width: 80%;
              margin: 0 auto;
              background-color: #fff;
              padding: 20px;
              box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            h2 {
              text-align: center;
              color: #333;
            }
            img {
              display: block;
              margin: 0 auto;
              width: 100px;
            }
            ul {
              list-style: none;
              padding: 0;
            }
            table {
              width: 100%;
              border-collapse: collapse;
              margin-top: 20px;
              color: black;
            }
            table, th, td {
              border: 1px solid #ddd;
              text-align: center;
            }
            th, td {
              padding: 8px;
              text-align: left;
            }
            th {
              background-color: #f2f2f2;
            }
            b {
              color: #3366cc;
            }
            footer {
              margin-top: 20px;
              text-align: center;
              color: #777;
            }
          </style>
        </head>
        <body>
          <div class="container">
            <div style="text-align: center;">
              <img alt="" src="https://img.upanh.tv/2023/11/30/techShopLogo.jpg" style="object-fit: cover; border-radius: 50%; display: block; outline: none; text-decoration: none; height: 50px; width: 50px; font-size: 13px;" class="CToWUd" data-bit="iit">
            </div>
            <p>Xin chào, <b>{$name}</b></p>
            <p>Cảm ơn bạn đã mua hàng tại TechShop.</p>
            <ul>
              <li>Đơn hàng: <b>#{$orderId}</b></li>
              <li>Địa chỉ giao hàng: <b>{$address}, {$ward}, {$district}, {$city}</b></li>
              <li>Số điện thoại liên lạc: <b>{$phone}</b></li>
              <li>Phí vận chuyển: <b>{$shipping_fee} VNĐ</b></li>
              <li>Mã giảm giá: <b>{$discount_code}</b></li>
              <li>Tổng giá trị đơn hàng: <b>{$total_price_format} VNĐ</b></li>
              <li>Ghi chú: <b>{$note}</b></li>
              <li>Hình thức giao hàng: <b>{$delivery_type}</b></li>
              <li>Hình thức thanh toán: <b>{$payment_type}</b></li>
            </ul>
            <table border="1" cellpadding="6" cellspacing="0">
              <thead>
                <tr>
                  <th>Mã sản phẩm</th>
                  <th>Tên sản phẩm</th>
                  <th>Màu sắc</th>
                  <th>Số lượng</th>
                  <th>Giá bán</th>
                  <th>Thành tiền</th>
                </tr>
              </thead>
              <tbody>
                $tbody
              </tbody>
            </table>
            <footer>
              <p>Chúng tôi sẽ giao hàng đến địa chỉ của bạn trong vòng 3-5 ngày làm việc.</p>
              <p>Nếu có bất kỳ thắc mắc, vui lòng liên hệ với chúng tôi qua email hoặc điện thoại.</p>
              <p>Trân trọng,</p>
              <p>TechShop</p>
            </footer>
          </div>
        </body>
        </html>


        END;

        try {

            $mail->send();

            throwMessage(SUCCESS_RESPONSE, "Message sent, please check your inbox.");

        } catch (Exception $e) {

            throwMessage(SEND_EMAIL_ERROR, "{$mail->ErrorInfo}");

        }
    
?>