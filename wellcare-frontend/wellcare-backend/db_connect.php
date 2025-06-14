<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "wellcare_doctors";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}
?>
