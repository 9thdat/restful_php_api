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
    public $category_name;

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
        return $stmt;
    }

    //show by id
    public function show_by_category(){
        $query = "SELECT * 
                FROM product , category 
                Where category.name = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->category_name);
        $stmt->execute();
        return $stmt;
    }
    
    public function show_by_category_brand(){
        $query = "SELECT product.id as ID, product.name as NAME, PRICE, DESCRIPTION, CATEGORY, BRAND, PRE_DISCOUNT, DISCOUNT_PERCENT, IMAGE, COLOR
                  FROM product, category ";

        if ($this->brand || $this->category_name ){
            $query .= " WHERE ";
            if ($this->brand && $this->category_name){
                $query .= "product.brand = :brand AND category.name = :category_name
                            AND product.category = category.id";
            }else{
                if ($this->brand){
                    $query .= "product.brand = :brand";
                }
                if ($this->category_name){
                    $query .= "category.name = :category_name";
                }
            }

        }
        $stmt = $this->conn->prepare($query);
        if ($this->brand) {
            $stmt->bindParam(':brand', $this->brand);
        }
        if ($this->category_name) {
            $stmt->bindParam(':category_name', $this->category_name);
        }
        $stmt->execute();
        return $stmt;
    }

    public function search(){
        if ($this->name == "") {
            return null;
        }

        $keywords = explode(" ", $this->name);

        $query = "SELECT * FROM product WHERE ";
        
        for ($i = 0; $i < count($keywords); $i++) {
            $param = "keyword{$i}";
            $query .= "name LIKE :$param";
        
            if ($i < count($keywords) - 1) {
                $query .= " AND ";
            }
        }
    
        if (stripos($this->name, "Apple") !== false){
            $query .= " OR brand =:brand";
        }
    
        $sort = " ORDER BY 
                CASE 
                    WHEN category = 1 THEN 0 
                    WHEN category = 2 THEN 1 
                    WHEN category = 3 THEN 2 
                    WHEN category = 4 THEN 3 
                ELSE 4 
                END, name";
                
        $query .= $sort;
    
        $stmt = $this->conn->prepare($query);
    
        foreach ($keywords as $i => $keyword) {
            $param = "keyword{$i}";
            $stmt->bindValue(":$param", "%$keyword%", PDO::PARAM_STR);
        }
    
        if (stripos($this->name, "Apple") !== false) {
            $stmt->bindValue(":brand", "Apple", PDO::PARAM_STR);
        }
    
        $stmt->execute();
    
        return $stmt;
    }

    

}
?>