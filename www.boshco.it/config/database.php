<?php
class Database {
    private $host = "31.11.39.210";
    private $db_name = "Sql1853582_1";
    private $username = "Sql1853582";
    private $password = "A5qSUd5JM94t-.E";
    private $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Errore di connessione: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
    // Helper function to get the correct path to a page
function getPagePath($page) {
    if (strpos($_SERVER['PHP_SELF'], 'view_pages') !== false) {
        return "../pages/$page";
    } else {
        return $page;
    }
}
}
?>