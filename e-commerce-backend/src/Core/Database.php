<?php
namespace App\Core;
    require_once 'src/Config/Config.php';
class Database {
   protected $connection;
   public function __construct($host, $user, $password, $dbname) {
    try {
        $this->connection = new \PDO('mysql:host=' . $host . ';dbname=' . $dbname, $user, $password);
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->logMessage("Connected Successfully");
        } catch (\PDOException $e) {
            $this->logMessage("Connection Error: " . $e->getMessage());
    }
}
protected function logMessage($message) {
    error_log($message);
}

public function executeQuery($query) {
    try {
        return $this->connection->exec($query);
    } catch (\PDOException $e) {
        $this->logMessage("Query Error: " . $e->getMessage());
    }
    return false;
}
    public function prepare($query) {
  try {
    $statement = $this->connection->prepare($query);
    return $statement; // Return the prepared statement object
  } catch (\PDOException $e) {
    // Handle potential PDO exceptions 
    error_log("Error preparing query: " . $e->getMessage());
    return false; 
  }
}
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    public function commit() {
        return $this->connection->commit();
    }

    public function rollBack() {
        return $this->connection->rollBack();
    }
    }
