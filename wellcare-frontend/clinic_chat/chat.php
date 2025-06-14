<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'db.php';

$my_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// جلب المرضى فقط إذا كان المستخدم دكتور
$patients = [];
if ($role === 'doctor') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id != ? AND role = 'patient'");
    $stmt->execute([$my_id]);
    $patients = $stmt->fetchAll();
}

// جلب الدكاترة للجميع
$stmt = $pdo->prepare("SELECT * FROM users WHERE id != ? AND role = 'doctor'");
$stmt->execute([$my_id]);
$doctors = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>قائمة المستخدمين</title>
    <style>
        body {
            margin: 0;
            background: linear-gradient(135deg, #e0f7fa, #80deea);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            direction: rtl;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background-color: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #00796b;
        }

        a {
            color: #00796b;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .user-lists {
            display: flex;
            gap: 40px;
            margin-top: 20px;
            justify-content: <?= $role === 'doctor' ? 'space-between' : 'center' ?>;
        }

        .user-column {
            flex: 1;
            background-color: #f1f1f1;
            padding: 15px;
            border-radius: 8px;
        }

        .user-column h3 {
            text-align: center;
            color: #004d40;
            margin-bottom: 15px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin: 8px 0;
            padding: 5px;
            background-color: #ffffff;
            border-radius: 5px;
            text-align: center;
            transition: background 0.3s;
        }

        li:hover {
            background-color: #e0f2f1;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>مرحبًا، <?= htmlspecialchars($role == 'doctor' ? 'دكتور' : 'مريض') ?></h2>
    <a href="logout.php">تسجيل الخروج</a>

    <div class="user-lists">
        <?php if ($role === 'doctor'): ?>
            <div class="user-column">
                <h3>المرضى</h3>
                <ul>
                    <?php foreach ($patients as $user): ?>
                        <li><a href="?user=<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="user-column">
            <h3>الدكاترة</h3>
            <ul>
                <?php foreach ($doctors as $user): ?>
                    <li><a href="?user=<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <?php if (isset($_GET['user'])): ?>
        <hr>
        <div id="chat-box" class="chat-box" style="height: 300px; overflow-y: scroll; padding: 10px; border: 1px solid #ccc; border-radius: 8px; margin-top: 20px;"></div>
        <form class="chat-input" onsubmit="sendMessage(); return false;" style="display: flex; margin-top: 10px;">
            <input type="text" id="message" placeholder="اكتب رسالتك..." style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #ccc;">
            <button type="submit" style="margin-right: 10px; background-color: #00796b; color: white; border: none; padding: 10px 20px; border-radius: 8px;">إرسال</button>
        </form>

        <script>
        let receiver_id = <?= intval($_GET['user']) ?>;

        function fetchMessages() {
            fetch('fetch_messages.php?receiver_id=' + receiver_id)
            .then(res => res.json())
            .then(data => {
                let chatBox = document.getElementById('chat-box');
                chatBox.innerHTML = '';
                data.forEach(msg => {
                    let p = document.createElement('div');
                    p.textContent = (msg.sender_id == <?= $my_id ?> ? "أنا: " : "هو: ") + msg.message;
                    p.style.padding = "8px";
                    p.style.margin = "5px 0";
                    p.style.borderRadius = "8px";
                    p.style.backgroundColor = msg.sender_id == <?= $my_id ?> ? "#c8e6c9" : "#eeeeee";
                    p.style.textAlign = msg.sender_id == <?= $my_id ?> ? "right" : "left";
                    chatBox.appendChild(p);
                });
                chatBox.scrollTop = chatBox.scrollHeight;
            });
        }

        setInterval(fetchMessages, 1000);

        function sendMessage() {
            let message = document.getElementById('message').value;
            if (!message.trim()) return;

            fetch('send_message.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'receiver_id=' + receiver_id + '&message=' + encodeURIComponent(message)
            }).then(() => {
                document.getElementById('message').value = '';
                fetchMessages();
            });
        }

        fetchMessages();
        </script>
    <?php endif; ?>
</div>
</body>
</html>
