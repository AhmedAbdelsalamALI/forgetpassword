<?php
header("Content-Type: application/json");
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "الإيميل مسجل بالفعل"]);
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "تم التسجيل بنجاح"]);
        } else {
            echo json_encode(["status" => "error", "message" => "حدث خطأ أثناء التسجيل"]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "طلب غير صالح"]);
}
?>
