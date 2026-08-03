<?php
$host = "127.0.0.1";
$port = 3306;
$dbname = "db_hydro";
$username = "dev";
$password = "123";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    function create($pdo, $table, array $data) { 
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));
        return $pdo->lastInsertId();
    }

    function readAll($pdo, $table, $where = null) {
        $sql = "SELECT * FROM $table";
        if ($where) {
            $sql .= " WHERE $where";
        }
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 

    function read($pdo, $table, $where = null) {
        $sql = "SELECT * FROM $table";
        if ($where) {
            $sql .= " WHERE $where";
        }
        $stmt = $pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function update($pdo, $table, array $data, $where) {
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "$column = ?";
        }
        $set = implode(', ', $set);

        $sql = "UPDATE $table SET $set WHERE $where";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));
        return $stmt->rowCount();
    }

    function delete($pdo, $table, $where) {
        $sql = "DELETE FROM $table WHERE $where";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute();
    }

} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}