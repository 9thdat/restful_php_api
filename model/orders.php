<?php 
    date_default_timezone_set('Asia/Ho_Chi_Minh');
class orders{
    private $conn;
    private $id;
    private $customer_email;
    private $name;
    private $address;
    private $ward;
    private $district;
    private $city;
    private $phone;
    private $discount_id;
    private $shipping_fee;
    private $total_price;
    private $note;
    private $order_date;
    private $completed_date;
    private $delivery_type;
    private $payment_type;
    private $status;

    
    public function __construct($db, $customer_email= null, $name = null, $address= null, $ward = null, $district = null, $city = null, $phone= null, $shipping_fee= null, $discount_id = null, $total_price= null, $note = null, $delivery_type= null, $payment_type= null) {
        $this->conn = $db;
        $this->customer_email = $customer_email;
        $this->name = $name;
        $this->address = $address;
        $this->ward = $ward;
        $this->district = $district;
        $this->city = $city;
        $this->phone = $phone;
        $this->shipping_fee = $shipping_fee;
        $this->discount_id = $discount_id;
        $this->total_price = $total_price;
        $this->note = $note;
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

    public function getStatus(){
        $query = "SELECT STATUS FROM orders WHERE WHERE id = :id and customer_email = :customer_email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customer_email", $this->customer_email);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        $row = $stmt->rowCount();
        if($row>0){
            foreach($stmt as $row){
                extract($row);
                return $STATUS;
            }
        }
        return false;

    }

    public function order(){
        $query = "INSERT INTO orders (CUSTOMER_EMAIL, NAME, ADDRESS, WARD, DISTRICT, CITY, PHONE, DISCOUNT_ID, 
                SHIPPING_FEE, TOTAL_PRICE, NOTE,ORDER_DATE, DELIVERY_TYPE, PAYMENT_TYPE, STATUS) 
            VALUES (:customer_email, :name, :address, :ward, :district, :city, :phone, :discount_id, 
                :shipping_fee, :total_price, :note, :order_date, :delivery_type, :payment_type, 'Processing');
            SELECT LAST_INSERT_ID();";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customer_email", $this->customer_email);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":ward", $this->ward);
        $stmt->bindParam(":district", $this->district);
        $stmt->bindParam(":city", $this->city);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":discount_id", $this->discount_id);
        $stmt->bindParam(":shipping_fee", $this->shipping_fee);
        $stmt->bindParam(":total_price", $this->total_price);
        $stmt->bindParam(":note", $this->note);
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