<?php

$host = 'localhost';
$username = 'root';
$passwords = ['', 'root', 'admin', 'Aj@y2252']; // Standard default local MySQL passwords and system specific password
$dbname = 'voting_system';

$conn = null;
$connected = false;

// Attempt connection using common default passwords
foreach ($passwords as $pwd) {
    try {
        // Disable throwing exceptions temporarily if we want to handle via properties,
        // or just catch the exception
        $conn = new mysqli($host, $username, $pwd);
        if (!$conn->connect_error) {
            $password = $pwd;
            $connected = true;
            break;
        }
    } catch (Exception $e) {
        // Catch exception if connection fails with wrong password
        continue;
    }
}

if (!$connected) {
    die("Database Connection failed. Please ensure MySQL is running in XAMPP and the root password is empty, 'root', or 'admin'.");
}

// Check if database exists, create it if it doesn't
if (!$conn->select_db($dbname)) {
    if ($conn->query("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci")) {
        $conn->select_db($dbname);
    } else {
        die("Failed to create database: " . $conn->error);
    }
}

// Self-healing database check: Re-import schema if tables are missing, outdated (missing 'phone'), or missing new seeded admin email
$tableExists = $conn->query("SHOW TABLES LIKE 'users'")->num_rows > 0;
$hasPhone = false;
$hasAdmin = false;
if ($tableExists) {
    $res = $conn->query("SHOW COLUMNS FROM users LIKE 'phone'");
    $hasPhone = $res && $res->num_rows > 0;
    
    $adminRes = $conn->query("SELECT user_id FROM users WHERE email = 'ac840165@gamil.com'");
    $hasAdmin = $adminRes && $adminRes->num_rows > 0;
}

if (!$tableExists || !$hasPhone || !$hasAdmin) {
    // Disable foreign key checks to safely drop tables
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    $conn->query("DROP TABLE IF EXISTS votes");
    $conn->query("DROP TABLE IF EXISTS candidates");
    $conn->query("DROP TABLE IF EXISTS users");
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

    // Import db.sql schema
    $sqlPath = __DIR__ . '/db.sql';
    if (file_exists($sqlPath)) {
        $sql = file_get_contents($sqlPath);
        
        // Basic SQL parser: split statements by semicolon
        $queries = explode(';', $sql);
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                if (!$conn->query($query)) {
                    error_log("SQL Setup Error: " . $conn->error . " in query: " . $query);
                }
            }
        }
    }
}

// Ensure the connection is set to the correct charset
$conn->set_charset("utf8mb4");
?>