<?php
class db {
    private $servername = "9thdat.mysql.database.azure.com";
    private $username = "dat";
    private $password = "a@b@123456";
    private $db = "techshop";
    private $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->db", $this->username, $this->password, array(
                PDO::MYSQL_ATTR_SSL_CA => '/path/to/BaltimoreCyberTrustRoot.crt.pem', // Đường dẫn tới SSL certificate
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false // Tắt việc xác thực SSL server, chỉ sử dụng khi cần thiết
            ));
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Kết nối thành công! \n";
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        return $this->conn;
    }
}
?>
