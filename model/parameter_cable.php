<?php

class parameter_cable {
    private $conn;
    private $id;

    private $productId;
    private $tech;
    private $function;
    private $input;
    private $output;
    private $length;
    private $maximum;
    private $madeIn;
    private $brandOf;
    private $brand;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function setProductId($productId) {
        $this->productId = $productId;
    }

    public function getByProductId() {
        $query = "SELECT * FROM parameter_cable WHERE PRODUCT_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->productId);
        $stmt->execute();
        return $stmt;
    }

    public function showBrand(){
        $query = "SELECT DISTINCT BRAND 
                FROM product
                WHERE CATEGORY = 3;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function sort($brand = null, $price = null, $input = null, $output = null, $length = null, $charger = null){
        $query = "SELECT *
                  FROM product p
                  INNER JOIN parameter_cable pc ON p.ID = pc.PRODUCT_ID
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
                    $value[4] = '%';
                    $likeConditions[] = "pc.INPUT LIKE '%" . $value . "%'"; 
                }
                $conditions[] = "(" . implode(" OR ", $likeConditions) . ")";
            } else {
                $input[4] = '%';
                $conditions[] = "pc.INPUT LIKE '%" . $input . "%'";
            }
        }
    
        if ($output !== null) {
            if (is_array($output) && count($output) > 0) {
                $likeConditions = array();
                foreach ($output as $value) {
                    if ($value != "Lightning"){
                        $value[4] = '%';
                    }
                    $likeConditions[] = "pc.OUTPUT LIKE '%" . $value . "%'";
                }
                $conditions[] = "(" . implode(" OR ", $likeConditions) . ")";
            } else {
                if ($output != "Lightning"){
                    $output[4] = '%';
                }
                $conditions[] = "pc.OUTPUT LIKE '%" . $output . "%'";
            }
        }
    
        
        if (count($conditions) > 0) {
            $query .= " AND " . implode(" AND ", $conditions);
        } else {
            $query .= " AND 1";
        }

        $checkLength = "CAST(SUBSTRING_INDEX(LENGTH, ' ', 1) AS DECIMAL(10,1))";
        if ($length != null && is_array($length) && count($length) > 0) {
            $lengthConditions = array();
            foreach ($length as $item) {
                switch ($item) {
                    case "1":
                        $lengthConditions[] = "$checkLength < 1 ";
                        break;
                    case "12":
                        $lengthConditions[] = "$checkLength BETWEEN 1 AND 2 ";
                        break;
                }
            }
            if (!empty($lengthConditions)) {
                $query .= " AND (" . implode(" OR ", $lengthConditions) . ") ";
            }
        }else{
            switch ($length) {
                case "1":
                    $lengthConditions[] = "AND $checkLength < 1 ";
                    break;
                case "12":
                    $lengthConditions[] = "AND $checkLength BETWEEN 1 AND 2 ";
                    break;
            }
        }

        
        $checkCharger = "CAST(SUBSTRING_INDEX(MAXIMUM, ' W', 1) AS DECIMAL(10,1)) ";
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
