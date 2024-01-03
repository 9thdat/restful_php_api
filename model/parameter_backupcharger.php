<?php 
class parameter_backupcharger{
    private $conn;
    private $id;

    private $product_id;
    private $efficiency;
    private $capacity;
    private $timefullcharge;
    private $input;
    private $output;
    private $weight;
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
        $query = "SELECT * FROM parameter_backupcharger WHERE PRODUCT_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->product_id);
        $stmt->execute();
        return $stmt;
    }

    public function showBrand(){
        $query = "SELECT DISTINCT BRAND 
                FROM product
                WHERE CATEGORY = 4;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function sort($brand = null, $price = null, $capacity = null, $input = null, $output = null, $charger = null){
        $query = "SELECT *
                  FROM product p
                  INNER JOIN parameter_backupcharger pb ON p.ID = pb.PRODUCT_ID
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
    
        if ($input !== null) {
            if (is_array($input) && count($input) > 0) {
                $likeConditions = array();
                foreach ($input as $value) {
                    if ($value == "Type C"){
                        $value[4] = '%';
                    }
                    $likeConditions[] = "pb.INPUT LIKE '%" . $value . "%'"; 
                }
                $conditions[] = "(" . implode(" OR ", $likeConditions) . ")";
            } else {
                if ($input == "Type C"){
                    $input[4] = '%';
                }
                $conditions[] = "pb.INPUT LIKE '%" . $input . "%'";
            }
        }
    
        if ($output !== null) {
            if (is_array($output) && count($output) > 0) {
                $likeConditions = array();
                foreach ($output as $value) {
                    if ($value != "Lightning"){
                        $value[4] = '%';
                    }
                    $likeConditions[] = "pb.OUTPUT LIKE '%" . $value . "%'";
                }
                $conditions[] = "(" . implode(" OR ", $likeConditions) . ")";
            } else {
                if ($output != "Lightning"){
                    $output[4] = '%';
                }
                $conditions[] = "pb.OUTPUT LIKE '%" . $output . "%'";
            }
        }
    
        
        if (count($conditions) > 0) {
            $query .= " AND " . implode(" AND ", $conditions);
        } else {
            $query .= " AND 1";
        }

        $checkCapacity = "CAST(SUBSTRING_INDEX(CAPACITY, ' ', 1) AS DECIMAL(10))";
        if ($capacity != null && is_array($capacity) && count($capacity) > 0) {
            $capactityConditions = array();
            foreach ($capacity as $item) {
                switch ($item) {
                    case "Dưới 10000 mAh":
                        $capactityConditions[] = "$checkCapacity < 10000 ";
                        break;
                    case "10000 mAh":
                        $capactityConditions[] = "$checkCapacity = 10000 ";
                        break;
                    case "15000 mAh":
                        $capactityConditions[] = "$checkCapacity = 15000 ";
                        break;
                    case "20000 mAh":
                        $capactityConditions[] = "$checkCapacity = 20000 ";
                        break;
                }
            }
            if (!empty($capactityConditions)) {
                $query .= " AND (" . implode(" OR ", $capactityConditions) . ") ";
            }
        }else{
            switch ($capacity) {
                case "Dưới 10000 mAh":
                    $capactityConditions[] = " AND $checkCapacity < 10000 ";
                    break;
                case "10000 mAh":
                    $capactityConditions[] = " AND $checkCapacity = 10000 ";
                    break;
                case "15000 mAh":
                    $capactityConditions[] = " AND $checkCapacity = 15000 ";
                    break;
                case "20000 mAh":
                    $capactityConditions[] = " AND $checkCapacity = 20000 ";
                    break;
            }
        }

        
        $checkCharger = "CAST(SUBSTRING_INDEX(SUBSTRING(SUBSTRING_INDEX(NAME, 'W', 1), -5), ' ', -1)AS DECIMAL(10,1))";
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