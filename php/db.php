<?php
$host = 'localhost';
$dbname = 'food_expiry_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    exit('Дерекқорға қосылу мүмкін болмады. XAMPP-та MySQL қосулы екенін тексеріңіз.');
}
?>
