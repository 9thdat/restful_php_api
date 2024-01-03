<?php 
class parameter_adapter{
    private $conn;
    private $id;

    private $product_id;
    private $model;
    private $function;
    private $input;
    private $output;
    private $maximum;
    private $size;
    private $tech;
    private $madeIn;
    private $brandOf;
    private $brand;

    public function __construct($db){
        $this->conn = $db;
    }

    public function setProductId($product_id){
        $this->product_id = $product_id;
    }

    public function getByProductId(){
        $query = "SELECT * FROM parameter_adapter WHERE PRODUCT_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->product_id);
        $stmt->execute();
        return $stmt;
    }

    public function showBrand(){
        $query = "SELECT DISTINCT BRAND 
                FROM product
                WHERE CATEGORY = 2;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function sort($brand = null, $price = null, $numberport = null, $output = null, $charger = null){
        $query = "SELECT *
                  FROM product p
                  INNER JOIN parameter_adapter pa ON p.ID = pa.PRODUCT_ID
                  WHERE ";
    
        if (is_array($price) && count($price) === 2 && is_numeric($price[0]) && is_numeric($price[1])) {
            $query .= " p.PRICE BETWEEN " . $price[0] . " AND " . $price[1] . " ";
        }
    
        $conditions = array();
    
        if ($brand !== null) {
            if (is_array($brand) && count($brand) > 0) {
                $inValues = "'" . implode("','", $brand) . "'";
                $conditions[] = "p.BRAND IN (" . $inValues . ")";
            } else {
                $conditions[] = "p.BRAND = '" . $brand . "'";
            }
        }
    
    
        if ($output !== null) {
            if (is_array($output) && count($output) > 0) {
                $likeConditions = array();
                foreach ($output as $value) {
                    if ($value != "Lightning"){
                        $value[4] = '%';
                    }
                    $likeConditions[] = "pa.OUTPUT LIKE '%" . $value . "%'";
                }
                $conditions[] = "(" . implode(" OR ", $likeConditions) . ")";
            } else {
                if ($output != "Lightning"){
                    $output[4] = '%';
                }
                $conditions[] = "pa.OUTPUT LIKE '%" . $output . "%'";
            }
        }

        if ($numberport !== null) {
            if (is_array($numberport) && count($numberport) > 0) {
                $likeConditions = array();
                foreach ($numberport as $value) {
                    if ($value == "1 cổng"){
                        $likeConditions[] = "p.NAME NOT LIKE '%cổng%'";
                    }
                    $likeConditions[] = "p.NAME LIKE '%" . $value . "%'";
                }
                $conditions[] = "(" . implode(" OR ", $likeConditions) . ")";
            } else {
                if ($numberport == "1 cổng"){
                    $conditions[] = "p.NAME NOT LIKE '%cổng%'";
                }else{
                    $conditions[] = "p.NAME LIKE '%" . $numberport . "%'";
                }
                
            }
        }
    
        
        if (count($conditions) > 0) {
            $query .= " AND " . implode(" AND ", $conditions);
        } else {
            $query .= " AND 1";
        }


        
        $checkCharger = "CAST(SUBSTRING_INDEX(MAXIMUM, ' ', 1) AS DECIMAL(10))";
        if ($charger != null && is_array($charger) && count($charger) > 0) {
            $chargerConditions = array();
            foreach ($charger as $item) {
                switch ($item) {
                    case "15":
                        $chargerConditions[] = "$checkCharger < 15";
                        break;
                    case "1525":
                        $chargerConditions[] = "$checkCharger BETWEEN 15 AND 25";
                        break;
                    case "2660":
                        $chargerConditions[] = "$checkCharger BETWEEN 26 AND 60";
                        break;
                    case "60":
                        $chargerConditions[] = "$checkCharger > 60";
                        break;
                }
            }
            if (!empty($chargerConditions)) {
                $query .= " AND (" . implode(" OR ", $chargerConditions) . ")";
            }
        }else {
            switch($charger){
                case "15":
                    $query .= "AND $checkCharger < 15 ";
                    break;
                case "1525":
                    $query .= "AND $checkCharger BETWEEN 15 AND 25 ";
                    break;
                case "2660":
                    $query .= "AND $checkCharger BETWEEN 26 AND 60 ";
                    break;
                case "60":
                    $query .= "AND $checkCharger > 60 ";
                    break;
            }
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

}



?>