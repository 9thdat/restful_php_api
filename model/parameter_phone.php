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

}

?>
