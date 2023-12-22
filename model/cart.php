<?php 
    date_default_timezone_set('Asia/Ho_Chi_Minh');

class cart{
    private $conn;
    private $customer_email;
    private $product_id;
    private $color;
    private $quantity;

    public function __construct($db){
        $this->conn = $db;
    }
    public function setCustomerEmail($customer_email){
        $this->customer_email = $customer_email;
    }
    public function setProductId($product_id){
        $this->product_id = $product_id;
    }
    public function setColor($color){
        $this->color = $color;
    }
    public function setQuantity($quantity){
        $this->quantity = $quantity;
    }

    public function read(){
        $query = "SELECT PRODUCT_ID, COLOR, QUANTITY  FROM cart, cart_detail 
                  WHERE customer_email = :customer_email AND cart.id = cart_detail.cart_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":customer_email", $this->customer_email);
        $stmt->execute();
        return $stmt;
    }
    public function check_cart(){
        $query = "SELECT * FROM cart WHERE customer_email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->customer_email);
        $stmt->execute();
        if ($stmt->rowCount()>0){
            return true;
        }
        return false;
    }

    public function add_to_cart(bool $s){
        $timenow = date("Y-m-d H:i:s", time());
        if (!$s){
            $query = "INSERT INTO cart(customer_email, created_at) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->customer_email);
            $stmt->bindParam(2, $timenow);
            if ($stmt->execute()){
                return true;
            }
            return false;
            
        }else{
            $query = "UPDATE cart SET updated_at = ? WHERE customer_email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $timenow);
            $stmt->bindParam(2, $this->customer_email);
            if ($stmt->execute()){
                return true;
            }
            return false;
        }
    }

    public function check_cart_detail(){
        $query = "SELECT * 
                  FROM cart_detail cd, cart c 
                  WHERE c.id = cd.cart_id AND customer_email = :customer_email
                    AND product_id = :product_id 
                    AND color = :color";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customer_email", $this->customer_email);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":color", $this->color);
        $stmt->execute();
        if ($stmt->rowCount()>0){
            return true;
        }
        return false;
        
    }

    public function add_to_cart_detail(bool $s){
        if(!$s){
            $query = "INSERT INTO cart_detail(cart_id, product_id, color, quantity)
                    VALUES(
                            (SELECT id FROM cart WHERE customer_email = :customer_email),
                            :product_id,
                            :color,
                            :quantity)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":customer_email", $this->customer_email);
            $stmt->bindParam(":product_id", $this->product_id);
            $stmt->bindParam(":color", $this->color);
            $stmt->bindParam(":quantity", $this->quantity);
            if ($stmt->execute()){
                return true;
            }
            return false;
            
        }else{
            $query = "UPDATE cart_detail 
                      SET quantity = quantity + :quantity 
                      WHERE product_id = :product_id AND color = :color
                        AND cart_id = (SELECT ID FROM cart WHERE customer_email = :customer_email) ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":quantity", $this->quantity);
            $stmt->bindParam(":product_id", $this->product_id);
            $stmt->bindParam(":color", $this->color);
            $stmt->bindParam(":customer_email", $this->customer_email);
            if ($stmt->execute()){
                return true;
            }
            return false;
            
        }

    }

    public function delete(){
        $timenow = date("Y-m-d H:i:s", time());
        $query = "UPDATE cart SET updated_at = ? WHERE customer_email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $timenow);
        $stmt->bindParam(2, $this->customer_email);
        $stmt->execute();
        
        $query = "DELETE FROM cart_detail 
                  WHERE product_id = :product_id 
                   AND color = :color 
                   AND cart_id = (SELECT ID FROM cart WHERE customer_email = :customer_email)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":color", $this->color);
        $stmt->bindParam(":customer_email", $this->customer_email);
        $stmt->execute();
        if ($stmt->rowCount()>0){
            return true;
        }
        return false;
    }

    public function deleteAll(){
        $timenow = date("Y-m-d H:i:s", time());
        $query = "UPDATE cart SET updated_at = ? WHERE customer_email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $timenow);
        $stmt->bindParam(2, $this->customer_email);
        $stmt->execute();
        
        $query = "DELETE FROM cart_detail 
                  WHERE cart_id = (SELECT ID FROM cart WHERE customer_email = :customer_email)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customer_email", $this->customer_email);
        $stmt->execute();
        if ($stmt->rowCount()>0){
            return true;
        }
        return false;
    }

    public function update_quantity(){
        $timenow = date("Y-m-d H:i:s", time());
        $query = "UPDATE cart SET updated_at = ? WHERE customer_email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $timenow);
        $stmt->bindParam(2, $this->customer_email);
        $stmt->execute();
        
        $query = "UPDATE cart_detail SET quantity = :quantity 
                  WHERE product_id = :product_id 
                   AND color = :color 
                   AND cart_id = (SELECT ID FROM cart WHERE customer_email = :customer_email)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":color", $this->color);
        $stmt->bindParam(":customer_email", $this->customer_email);
        $stmt->execute();
        if ($stmt->rowCount()>0){
            return true;
        }
        return false;
    }


}


?>