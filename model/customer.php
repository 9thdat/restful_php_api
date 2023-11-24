<?php 
class customer{
    private $conn;
    public $email;
    public $name;
    public $password;
    public $phone;
    public $gender;
    public $birthday;
    public $address;
    public $quarter;
    public $district;
    public $city;
    public $image;
    public $status;

    public function __construct($connect){
        $this->conn= $connect;
    }
    public function login(){
        $query = "SELECT * FROM customer WHERE email = :email ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        return $stmt;
        // if($stmt->execute()){
        //     $user = $stmt->fetch(PDO::FETCH_ASSOC);
        //     if($user){
        //         return true;
        //     }else{
        //         return false;
        //     }
        // }
        
    }
    public function signup(){
        $query = "INSERT INTO customer(email, name, password, phone, gender, birthday, address, quarter, district, city, status)
                  VALUES (:email, :name, SHA2(:password, 256), :phone, :gender, STR_TO_DATE(:birthday, '%d-%m-%Y'), :address, :quarter, :district, :city, :status)";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":birthday", $this->birthday);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":quarter", $this->quarter);
        $stmt->bindParam(":district", $this->district);
        $stmt->bindParam(":city", $this->city);
        $this->status = "active";
        $stmt->bindParam(":status", $this->status);
    
        if ($stmt->execute()) {
            return true;
        } else {
            printf("Error %s. \n", $stmt->error);
            return false;
        }
    }
    

}


?>