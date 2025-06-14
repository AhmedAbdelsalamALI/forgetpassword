<?php
header("Content-Type: application/json");
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $user_id = $_POST["user_id"];

    // نتأكد إن المستخدم موجود
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode([
            ["title" => "مقال خاص 1", "body" => "محتوى خاص بالمستخدم"],
            ["title" => "مقال خاص 2", "body" => "مزيد من المحتوى الخاص"]
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "مستخدم غير معروف"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "طلب غير صالح"]);
}
?>
