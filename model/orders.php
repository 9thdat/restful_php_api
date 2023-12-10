<?php 
    date_default_timezone_set('Asia/Ho_Chi_Minh');
class orders{
    private $conn;
    private $id;
    private $customer_email;
    private $address;
    private $phone;
    private $discount_id;
    private $shipping_fee;
    private $total_price;
    private $order_date;
    private $completed_date;
    private $delivery_type;
    private $payment_type;
    private $status;

    
    public function __construct($db, $customer_email= null, $address= null, $phone= null, $shipping_fee= null, $discount_id = null, $total_price= null, $delivery_type= null, $payment_type= null) {
        $this->conn = $db;
        $this->customer_email = $customer_email;
        $this->address = $address;
        $this->phone = $phone;
        $this->shipping_fee = $shipping_fee;
        $this->discount_id = $discount_id;
        $this->total_price = $total_price;
        $this->delivery_type = $delivery_type;
        $this->payment_type = $payment_type;
    }
    public function setCustomerEmail($customer_email){
        $this->customer_email = $customer_email;
    }
    public function setId($id){
        $this->id = $id;
    }
    public function read(){
        $query = "SELECT * FROM orders WHERE customer_email = :customer_email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customer_email", $this->customer_email);
        $stmt->execute();
        return $stmt;
    }
    public function order(){
        $query = "INSERT INTO orders (CUSTOMER_EMAIL, ADDRESS, PHONE, DISCOUNT_ID, SHIPPING_FEE, TOTAL_PRICE,
            ORDER_DATE, DELIVERY_TYPE, PAYMENT_TYPE, STATUS) 
            VALUES (:customer_email, :address, :phone, :discount_id, :shipping_fee, :total_price,
            :order_date, :delivery_type, :payment_type, 'Processing');
            SELECT LAST_INSERT_ID();";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customer_email", $this->customer_email);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":discount_id", $this->discount_id);
        $stmt->bindParam(":shipping_fee", $this->shipping_fee);
        $stmt->bindParam(":total_price", $this->total_price);
        $stmt->bindValue(":order_date", date("Y-m-d", time()));
        $stmt->bindParam(":delivery_type", $this->delivery_type);
        $stmt->bindParam(":payment_type", $this->payment_type);
        try {
            $stmt->execute();
            $lastInsertedId = $this->conn->lastInsertId();
            return $lastInsertedId;
        } catch (PDOException $e) {
            echo $e;
            return -1;
        }
    }

    public function cancel() {
        $query = "UPDATE orders SET STATUS = 'Cancelled', CANCELED_DATE = :canceled_date 
                  WHERE id = :order_id and customer_email = :customer_email";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindValue(":canceled_date", date("Y-m-d", time()));
        $stmt->bindParam(":order_id", $this->id);
        $stmt->bindParam(":customer_email", $this->customer_email);
    
        try {
            $stmt->execute();
            return true; 
        } catch (PDOException $e) {
            return false; 
        }
    }



}

?>