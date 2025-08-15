<?php
$servername = "localhost";
$username = "root";
$password = ""; // your MySQL password, usually empty for XAMPP
$dbname = "hackathondb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>