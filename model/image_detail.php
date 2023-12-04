<?php 
class image_detail{
    public $conn;
    public $id;
    public $product_id;
    public $color;
    public $ordinal;
    public $image;
    public function __construct($db, $product_id = null, $color = null){
        $this->conn = $db;
        $this->product_id = $product_id;
        $this->color = $color;
    }
    public function show_by_productid(){
        $query = "SELECT * FROM image_detail WHERE product_id = :product_id ";
        if($this->color != null){
            $query .= " AND color = :color ";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":product_id", $this->product_id);
        if ($this->color != null){
            $stmt->bindParam(":color", $this->color);
        }   
        $stmt->execute();
        return $stmt;
    }
}





?>