<?php
define("HOSTNAME", "localhost");
define("USER", "root");
define("PASSWORD", "");
define("DATABASE", "tienda_db");

try {
    $con = new PDO("mysql:host=" . HOSTNAME . ";dbname=" . DATABASE, USER, PASSWORD);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>