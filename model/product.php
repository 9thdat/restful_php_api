<?php
class product{
    private $conn;
    private $id;
    private $name;
    private $price;
    private $description;
    private $image;
    private $category;
    private $brand;
    private $pre_discount;
    private $discount_percent;
    private $category_name;

    public function __construct($db){
        $this->conn = $db;
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function setName($name) {
        $this->name = $name;
    }
    public function setBrand($brand) {
        $this->brand = $brand;
    }

    public function setCategory($category) {
        $this->category = $category;
    }

    public function setCategoryName($categoryName) {
        $this->category_name = $categoryName;
    }


    public function read(){
        $query = "SELECT * FROM product ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    public function find(){
        $query = "SELECT * FROM product WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        $num = $stmt->rowCount();
        if($num > 0){
            return true;
        }else{
            return false;
        }
    }

    public function show_by_id(){
        $query = "SELECT * FROM product where id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt;
    }

    public function show_by_id_cart(){
        $query = "SELECT p.ID, NAME, PRICE as PRICE_PRODUCT, CATEGORY, BRAND, PRE_DISCOUNT, DISCOUNT_PERCENT, i.COLOR, QUANTITY as QUANTITY_STOCK, i.IMAGE as IMAGE_COLOR
                  FROM product p, product_quantity pq, image_detail i
                  WHERE p.id = pq.PRODUCT_ID and p.id = i.PRODUCT_ID and p.id = ? and i.ORDINAL = -1";
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
        $query = "SELECT product.id as ID, product.name as NAME, PRICE, DESCRIPTION, CATEGORY, BRAND, PRE_DISCOUNT, DISCOUNT_PERCENT, IMAGE
                  FROM product, category ";

        if ($this->brand || $this->category_name ){
            $query .= " WHERE ";
            if ($this->brand && $this->category_name){
                $query .= "product.brand = :brand AND category.name = :category_name";
            }else{
                if ($this->brand){
                    $query .= " product.brand = :brand";
                }
                if ($this->category_name){
                    $query .= " category.name = :category_name";
                }
            }
            $query .= " AND product.category = category.id";

        }
        $stmt = $this->conn->prepare($query);
        if ($this->brand) {
            $stmt->bindParam(":brand", $this->brand);
        }
        if ($this->category_name) {
            $stmt->bindParam(":category_name", $this->category_name);
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

    public function show_all_brand_by_category(){
        $query = "SELECT distinct BRAND 
                  FROM product, category 
                  WHERE category.name = :category_name AND product.category = category.id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":category_name", $this->category_name);
        $stmt->execute();
        return $stmt;
    }

    public function getCategorybyId(){
        $query = "SELECT category.name
                  FROM product
                  JOIN category ON product.category = category.id
                  WHERE product.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
    
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($row) {
            return $row['name'];
        } else {
            return null; 
        }
    }
    

}
?>