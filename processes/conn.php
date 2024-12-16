<?php
$host = "sql12.freesqldatabase.com";
$username = "sql12752345";
$password = "YqrN9ClQW2";
$dbname = "sql12752345";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}