<?php
class product{
    private $conn;
    public $id;
    public $name;
    public $price;
    public $description;
    public $image;
    public $category;
    public $brand;
    public $pre_discount;
    public $discount_percent;
    public $color;

    public function __construct($db){
        $this->conn = $db;
    }

    //read data
    public function read(){
        $query = "SELECT * FROM product ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    //show by id
    public function show_by_id(){
        $query = "SELECT * FROM product where id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->name = $row["NAME"];
        $this->price = $row["PRICE"];
        $this->description = $row["DESCRIPTION"];
        $this->image = $row["IMAGE"];
        $this->category = $row["CATEGORY"];
        $this->brand = $row["BRAND"];
        $this->pre_discount = $row["PRE_DISCOUNT"];
        $this->discount_percent = $row["DISCOUNT_PERCENT"];
        $this->color = $row["COLOR"]; 
    }

    //show by id
    public function show_by_category(){
        $query = "SELECT * FROM product where category = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->category);
        $stmt->execute();
        return $stmt;
    }
    
    public function show_by_category_brand(){
        $query = "SELECT * FROM product where brand=? and category = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->brand);
        $stmt->bindParam(2, $this->category);
        $stmt->execute();
        return $stmt;
    }

    public function search(){
        $query = "SELECT * FROM product where name LIKE :keyword";
        // $query = "SELECT * 
        //         FROM product 
        //         where name LIKE :keyword
        //         ORDER BY 
        //         CASE 
        //             WHEN category = 1 THEN 0 
        //             WHEN category = 2 THEN 1
        //             WHEN category = 3 THEN 2
        //             WHEN category = 4 THEN 3 
        //             ELSE 4 
        //             END, name";
        $keyword = "%" . $this->name . "%";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":keyword", $keyword, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt;
    }
    

}