<?php
session_start();
require 'db.php';

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];

    // تحقق من وجود البريد مسبقاً
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $error = "⚠️ البريد الإلكتروني مستخدم بالفعل.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $password, $role])) {
            $success = "✅ تم إنشاء الحساب بنجاح. يمكنك الآن تسجيل الدخول.";
        } else {
            $error = "حدث خطأ أثناء التسجيل.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تسجيل حساب جديد</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa, #80deea);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .register-box {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        .register-box h2 {
            margin-bottom: 20px;
            color:rgb(29, 105, 187);
        }

        .register-box input,
        .register-box select {
            padding: 10px;
            margin: 10px 0;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        .register-box button {
            padding: 10px;
            background-color:rgb(25, 44, 218);
            color: #fff;
            border: none;
            border-radius: 8px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .register-box button:hover {
            background-color:rgb(39, 110, 192);
        }

        .register-box .message {
            margin-bottom: 10px;
        }

        .register-box .success {
            color: green;
        }

        .register-box .error {
            color: red;
        }

        .register-box p {
            margin-top: 15px;
        }

        .register-box a {
            color:rgb(28, 137, 183);
            text-decoration: none;
        }

        .register-box a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-box">
        <h2>تسجيل حساب جديد</h2>

        <?php if ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="name" placeholder="الاسم الكامل" required>
            <input type="email" name="email" placeholder="البريد الإلكتروني" required>
            <input type="password" name="password" placeholder="كلمة المرور" required>
            <select name="role" required>
                <option value="">اختر النوع</option>
                <option value="patient">مريض</option>
                <option value="doctor">دكتور</option>s
            </select>
            <button type="submit">تسجيل</button>
        </form>

        <p>لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a></p>
    </div>
</body>
</html>
