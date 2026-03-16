<?php
    function getConnection() {
        $user = 'root';
        $pass = 'root';
        $dsn  = 'mysql:host=localhost;dbname=eaa;charset=utf8';
        try {
            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
?>