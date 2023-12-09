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

}

?>
