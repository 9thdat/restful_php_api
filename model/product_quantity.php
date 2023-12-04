<?php 
class product_quantity{
    private $conn;
    public $product_id;
    public $color;
    public $amount;

    public function __construct($db, $product_id= null, $color= null, $amount= null){
        $this->conn = $db;
        $this->product_id = $product_id;
        $this->color = $color;
        $this->amount = $amount;
    }

    public function check_quantity(){
        $query = "SELECT * FROM product_quantity WHERE product_id = :product_id AND color = :color";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":color", $this->color);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($this->amount > $row['QUANTITY']){
                return false ;
            }
        }
        return true;
    }


    public function update_sold_order(){
        $query = "UPDATE product_quantity 
                  SET quantity = quantity - :amount, sold = sold + :amount
                  WHERE product_id = :product_id AND color = :color";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":color", $this->color);
        try{
            $stmt->execute();
            return true; 
        } catch (PDOException $e) {
            return false;
        }
    }
    public function update_quantity_return(){
        $query = "UPDATE product_quantity 
                  SET quantity = quantity + :amount, sold = sold - :amount
                  WHERE product_id = :product_id AND color = :color";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":color", $this->color);
        try{
            $stmt->execute();
            return true; 
        } catch (PDOException $e) {
            return false;
        }
    }
}





?>