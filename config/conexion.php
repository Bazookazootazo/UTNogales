<?php
$host = "localhost";
$db   = "mtbnog";
$user = "root";
$pass = "";
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $conn = new PDO($dsn, $user, $pass, $options);
     $query_limpieza = "DELETE FROM usuarios WHERE estatus = 'INACTIVO' AND fecha_baja <= DATE_SUB(NOW(), INTERVAL 30 DAY)";
     $conn->exec($query_limpieza);

} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
$con = mysqli_connect($host, $user, $pass, $db); 
?>