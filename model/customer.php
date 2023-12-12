<?php 
class customer{
    private $conn;
    private $email;
    private $name;
    private $password;
    private $phone;
    private $gender;
    private $birthday;
    private $address;
    private $ward;
    private $district;
    private $city;
    private $image;
    private $status;

    public function __construct($connect){
        $this->conn= $connect;
    }
    public function setEmail($email) {
        $this->email = $email;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function setGender($gender) {
        $this->gender = $gender;
    }

    public function setBirthday($birthday) {
        $this->birthday = $birthday;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function setWard($ward) {
        $this->ward = $ward;
    }

    public function setDistrict($district) {
        $this->district = $district;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function setImage($image) {
        $this->image = $image;
    }

    public function setStatus($status) {
        $this->status = $status;
    }
    public function login(){
        $query = "SELECT * FROM customer WHERE email = :email ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        return $stmt;
        
    }
    public function read(){
        $query = "SELECT * FROM customer WHERE email = :email ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        return $stmt;
        
    }
    public function find(){
        $query = "SELECT * FROM customer WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        $num = $stmt->rowCount();
        if($num > 0){
            return true;
        }else{
            return false;
        }
    }
    public function signup(){
        $query = "INSERT INTO customer(email, name, password, phone, gender, birthday, address, ward, district, city, status)
                  VALUES (:email, :name, SHA2(:password, 256), :phone, :gender, STR_TO_DATE(:birthday, '%d-%m-%Y'), :address, :ward, :district, :city, :status)";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":birthday", $this->birthday);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":ward", $this->ward);
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
    public function update(){
        $query = "UPDATE customer SET ";
        if( $this-> name != null){
            $query .=	" name = '" . $this->name . "',";
        }
        if( $this-> password != null){
            $query .=	" password = SHA2('" . $this->password . "', 256),";
        }
        if( $this-> phone != null){
            $query .=	" phone = '" . $this->phone . "',";
        }
        if( $this-> gender != null){
            $query .=	" gender = '" . $this->gender . "',";
        }
        if( $this-> birthday != null){
            $query .=	" birthday = STR_TO_DATE('" . $this->birthday . "' , '%d-%m-%Y'),";
        }
        if( $this-> address != null){
            $query .=	" address = '" . $this->address . "',";
        }
        if( $this-> ward != null){
            $query .=	" ward = '" . $this->ward . "',";
        }
        if( $this-> district != null){
            $query .=	" district = '" . $this->district . "',";
        }
        if( $this-> city != null){
            $query .=	" city = '" . $this->city . "',";
        }
        if( $this-> image != null){
            $query .=	" image = '" . $this->image . "',";
        }
        $query = substr($query, 0, -1);
        $query .= " WHERE email = :email";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    

}


?>