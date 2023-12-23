<?php
    $root = $_SERVER['DOCUMENT_ROOT'];
    $file_path = $root . '/restful_php_api/env.php';
    if (file_exists($file_path)) {
        include_once($file_path);
    }
class db {
    private $servername = "9thdat.mysql.database.azure.com";
    private $username = "dat";
    private $password = PASSWORD_DB;
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
