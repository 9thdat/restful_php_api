<?php
class order_detail{
    private $conn;
    private $order_id;
    private $product_id;
    private $color;
    private $quantity;
    private $price;

    public function __construct($db, $order_id= null, $product_id= null, $color= null, $quantity= null, $price= null){
        $this->conn = $db;
        $this->order_id = $order_id;
        $this->product_id = $product_id;
        $this->color = $color;
        $this->quantity = $quantity;
        $this->price = $price;
    }
    public function read(){
        $query = "SELECT * FROM order_detail WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":order_id", $this->order_id);
        $stmt->execute();
        return $stmt;
    }

    public function add(){
        $query = "INSERT INTO order_detail(order_id, product_id, color, quantity, price) 
                  VALUES (:order_id, :product_id, :color, :quantity, :price)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":order_id", $this->order_id);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":color", $this->color);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":price", $this->price);
        
        try{
            $stmt->execute();
            return true; 
        } catch (PDOException $e) {
            return false;
        }
    }
}


?>