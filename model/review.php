<?php 
class review{
    private $conn;
    private $product_id;
    private $customer_email;
    private $rating;
    private $content;
    private $admin_reply;
    private $created_at;
    private $updated_at;

    public function __construct($db){
        $this->conn = $db;

    }

    public function setProductId($product_id){
        $this->product_id = $product_id;
    }

    public function getByProductId(){
        $query = "SELECT* 
                  FROM review rv, customer c
                  WHERE product_id = :product_id and rv.CUSTOMER_EMAIL = c.EMAIL";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->execute();
        return $stmt;
    }


}











?>