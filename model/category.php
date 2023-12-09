<?php
class category{
    private $conn;
    public $id;
    public $name;
    
    public function __construct($db){
        $this->conn = $db;
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function setName($name) {
        $this->name = $name;
    }

    //create data
    public function create(){
        $query = "INSERT INTO category (id, name) VALUES (:id, :name)";
        $stmt = $this->conn->prepare($query);
        //clean data (lọc ký tự đặc biệt)
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        //bind data
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":name", $this->name);
        if($stmt->execute()){
            return true;
        }
        printf("Error %s. \n", $stmt-> error);
        return false;
    }

    public function update(){
        $query = "UPDATE category SET name = :name WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        //clean data (lọc ký tự đặc biệt)
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        //bind data
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":name", $this->name);
        if($stmt->execute()){
            return true;
        }
        printf("Error %s. \n", $stmt-> error);
        return false;
    }

    public function delete(){
        $query = "DELETE FROM category WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        //clean data (lọc ký tự đặc biệt)
        $this->id = htmlspecialchars(strip_tags($this->id));
        //bind data
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        if($stmt->execute()){
            return true;
        }
        printf("Error %s. \n", $stmt-> error);
        return false;
    }
    
    public function showall(){
        $query = "SELECT * FROM category";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

}