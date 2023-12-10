<?php 
class discount{
    private $conn;
    private $id;
    private $code;
    private $type;
    private $value;
    private $description;
    private $start_date;
    private $end_date;
    private $min_apply;
    private $max_speed;
    private $quantity;
    private $status;
    private $created_at;
    private $updated_at;
    private $disabled_at;
    

    public function __construct($db){
        $this->conn = $db;
    }
    public function setCode($code){
        $this->code = $code;
    }

    public function validate(){
        $query = "SELECT * FROM discount WHERE code = :code";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":code", $this->code);
        $stmt->execute();
        return $stmt;
    }

    


    
}




















?>
