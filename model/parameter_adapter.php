<?php 
class parameter_adapter{
    private $conn;
    private $id;

    private $product_id;
    private $model;
    private $function;
    private $input;
    private $output;
    private $maximum;
    private $size;
    private $tech;
    private $madeIn;
    private $brandOf;
    private $brand;

    public function __construct($db){
        $this->conn = $db;
    }

    public function setProductId($product_id){
        $this->product_id = $product_id;
    }

    public function getByProductId(){
        $query = "SELECT * FROM parameter_adapter WHERE PRODUCT_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->product_id);
        $stmt->execute();
        return $stmt;
    }

}



?>