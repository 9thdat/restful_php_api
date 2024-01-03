<?php

class parameter_phone {
    private $conn;
    private $id;
    private $productId;
    private $screen;
    private $operatingSystem;
    private $backCamera;
    private $frontCamera;
    private $chip;
    private $ram;
    private $rom;
    private $sim;
    private $batteryCharger;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function setProductId($productId) {
        $this->productId = $productId;
    }

    public function getByProductId() {
        $query = "SELECT * FROM parameter_phone WHERE PRODUCT_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->productId);
        $stmt->execute();
        return $stmt;
    }

    public function showOs(){
        $query = "SELECT DISTINCT OPERATING_SYSTEM FROM parameter_phone";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    public function showRam(){
        $query = "SELECT DISTINCT RAM
                FROM parameter_phone
                ORDER BY 
                CASE 
                    WHEN RAM LIKE '%GB' THEN CAST(SUBSTRING_INDEX(RAM, ' ', 1) AS UNSIGNED)
                    ELSE 0
                END ASC;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    public function showRom(){
        $query = "SELECT DISTINCT ROM
                FROM parameter_phone
                ORDER BY 
                CASE 
                    WHEN ROM LIKE '%TB' THEN CAST(SUBSTRING_INDEX(ROM, ' ', 1) AS UNSIGNED) * 1024
                    WHEN ROM LIKE '%GB' THEN CAST(SUBSTRING_INDEX(ROM, ' ', 1) AS UNSIGNED)
                    ELSE 0
                END ASC;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function showBrand(){
        $query = "SELECT DISTINCT BRAND 
                FROM product
                WHERE CATEGORY = 1;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // public function sort($brand = null, $os = null){
    //     $queryBrand = "SELECT *
    //           FROM product p
    //           INNER JOIN parameter_phone pp ON p.ID = pp.PRODUCT_ID
    //           WHERE p.BRAND IN (";

    //     $inValues = "'" . implode("','", $brand) . "'";
    //     $queryBrand .= $inValues . ")";


    //     $queryOs = "SELECT *
    //           FROM product p
    //           INNER JOIN parameter_phone pp ON p.ID = pp.PRODUCT_ID
    //           WHERE ";

    //     $likeConditions = array();
    //     foreach ($os as $value) {
    //         $likeConditions[] = "pp.OPERATING_SYSTEM LIKE '%" . $value . "%'";
    //     }
    //     $queryOs .= implode(" OR ", $likeConditions);
        
    //     // $queryPrice = "";
        
    //     // $queryRam = "";, $price = null, $ram = null, $rom = null
        
    //     // $queryRom = "";
        
    //     // $queryCharger = "";

    //     $stmt = $this->conn->prepare($query);
    //     $stmt->execute();
    //     return $stmt;
        
    // }


    public function sort($brand = null, $os = null, $price = null, $ram = null, $rom = null, $charger = null){
        $query = "SELECT *
                  FROM product p
                  INNER JOIN parameter_phone pp ON p.ID = pp.PRODUCT_ID
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
    
        if ($os !== null) {
            if (is_array($os) && count($os) > 0) {
                $likeConditions = array();
                foreach ($os as $value) {
                    $likeConditions[] = "pp.OPERATING_SYSTEM LIKE '%" . $value . "%'";
                }
                $conditions[] = "(" . implode(" OR ", $likeConditions) . ")";
            } else {
                $conditions[] = "pp.OPERATING_SYSTEM LIKE '%" . $os . "%'";
            }
        }
    
        if ($ram !== null) {
            if (is_array($ram) && count($ram) > 0) {
                $likeConditions = array();
                foreach ($ram as $value) {
                    $likeConditions[] = "pp.RAM LIKE '%" . $value . "%'";
                }
                $conditions[] = "(" . implode(" OR ", $likeConditions) . ")";
            } else {
                $conditions[] = "pp.RAM LIKE '%" . $ram . "%'";
            }
        }
    
        if ($rom !== null) {
            if (is_array($rom) && count($rom) > 0) {
                $likeConditions = array();
                foreach ($rom as $value) {
                    $likeConditions[] = "pp.ROM LIKE '%" . $value . "%'";
                }
                $conditions[] = "(" . implode(" OR ", $likeConditions) . ")";
            } else {
                $conditions[] = "pp.ROM LIKE '%" . $rom . "%'";
            }
        }
    
        if (count($conditions) > 0) {
            $query .= " AND " . implode(" AND ", $conditions);
        } else {
            $query .= " AND 1";
        }
        
        $checkCharger = "CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(BATTERY_CHARGER, 'mAh ', -1), ' ', 1) AS UNSIGNED) ";
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
