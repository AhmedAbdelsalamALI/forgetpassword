<?php
header("Content-Type: application/json");
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        echo json_encode([
            "status" => "success",
            "user_id" => $user["id"],
            "name" => $user["name"]
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "الإيميل أو كلمة السر خطأ"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "طلب غير صالح"]);
}
?>
