<?php 
date_default_timezone_set('Asia/Ho_Chi_Minh');
class review{
    private $conn;
    private $id;
    private $product_id;
    private $customer_email;
    private $rating;
    private $content;
    private $admin_reply;
    private $created_at;
    private $updated_at;

    public function __construct($db, $product_id = null, $customer_email = null, $rating = null, $content = null){
        $this->conn = $db;
        $this->product_id = $product_id;
        $this->customer_email = $customer_email;
        $this->rating = $rating;
        $this->content = $content;

    }
    public function setId($id){
        $this->id = $id;
    }

    public function setProductId($product_id){
        $this->product_id = $product_id;
    }

    public function getByProductId(){
        $query = "SELECT* 
                  FROM review rv, customer c
                  WHERE product_id = :product_id and rv.CUSTOMER_EMAIL = c.EMAIL
                  ORDER BY CREATED_AT DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->execute();
        return $stmt;
    }

    public function add(){
        $query = "INSERT INTO review(PRODUCT_ID, CUSTOMER_EMAIL, RATING, CONTENT, CREATED_AT) 
                  VALUES(:product_id, :customer_email, :rating, :content, :created_at )";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":customer_email", $this->customer_email);
        $stmt->bindParam(":rating", $this->rating);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindValue(":created_at", date("Y-m-d H:i:s", time()));
        if ($stmt->execute()) {
            return true;
        } else {
            printf("Error %s. \n", $stmt->error);
            return false;
        }
    }


}











?>