<?php
// Update these values only if your XAMPP MySQL configuration differs.
$host = 'localhost';
$db = 'parkease';
$user = 'root';
$pass = '';

$mysqli = new mysqli($host, $user, $pass);
if ($mysqli->connect_error) {
    die('Database connection failed. Start MySQL in XAMPP and verify includes/db.php credentials.');
}

if (!$mysqli->select_db($db)) {
    $schemaFile = __DIR__ . '/../database/parkease.sql';
    if (!file_exists($schemaFile)) {
        die("Unknown database '$db'. Place database/parkease.sql in the project and reload.");
    }

    if (!$mysqli->query("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
        die('Failed to create database: ' . $mysqli->error);
    }

    if (!$mysqli->select_db($db)) {
        die('Failed to select database after creation: ' . $mysqli->error);
    }

    $sql = file_get_contents($schemaFile);
    if ($sql === false) {
        die('Failed to read database schema file: ' . $schemaFile);
    }

    $statements = array_filter(array_map('trim', preg_split('/;\s*(\r\n|\n|\r)/', $sql)));
    foreach ($statements as $statement) {
        if ($statement === '') {
            continue;
        }
        if (!$mysqli->query($statement)) {
            die('Database schema import error: ' . $mysqli->error);
        }
    }
}

$checkTable = $mysqli->query("SHOW TABLES LIKE 'users'");
if ($checkTable && $checkTable->num_rows === 0) {
    $schemaFile = __DIR__ . '/../database/parkease.sql';
    if (!file_exists($schemaFile)) {
        die("Database is missing tables and the schema file is unavailable: $schemaFile");
    }

    $sql = file_get_contents($schemaFile);
    if ($sql === false) {
        die('Failed to read database schema file: ' . $schemaFile);
    }

    $statements = array_filter(array_map('trim', preg_split('/;\s*(\r\n|\n|\r)/', $sql)));
    foreach ($statements as $statement) {
        if ($statement === '') {
            continue;
        }
        if (!$mysqli->query($statement)) {
            die('Database schema import error while restoring tables: ' . $mysqli->error);
        }
    }
}

// Add location data when upgrading an existing installation created from an older schema.
$slotColumns = $mysqli->query("SHOW COLUMNS FROM slots LIKE 'latitude'");
if ($slotColumns && $slotColumns->num_rows === 0) {
    if (!$mysqli->query("ALTER TABLE slots ADD latitude DECIMAL(10,7) NULL, ADD longitude DECIMAL(10,7) NULL")) {
        die('Failed to add parking location fields: ' . $mysqli->error);
    }
}
if (!$mysqli->query("UPDATE slots SET latitude=21.1702400, longitude=72.8310600 WHERE latitude IS NULL OR longitude IS NULL OR (latitude=19.0760000 AND longitude=72.8777000)")) {
    die('Failed to initialize parking location data: ' . $mysqli->error);
}

if (!$mysqli->set_charset('utf8mb4')) {
    die('Failed to set database charset: ' . $mysqli->error);
}
?>
