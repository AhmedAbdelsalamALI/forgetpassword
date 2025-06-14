<?php
// تكوين قاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medical_clinic";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// إنشاء قاعدة البيانات إذا لم تكن موجودة
$sql = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($sql) === TRUE) {
    // تحديد قاعدة البيانات
    $conn->select_db($dbname);
    
    // إنشاء جدول المواعيد
    $create_table = "CREATE TABLE IF NOT EXISTS appointments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        email VARCHAR(100),
        doctor VARCHAR(50) NOT NULL,
        appointment_date DATE NOT NULL,
        appointment_time TIME NOT NULL,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    
    $conn->query($create_table);
}

// معالجة إرسال النموذج
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'book') {
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $doctor = $conn->real_escape_string($_POST['doctor']);
    $date = $conn->real_escape_string($_POST['date']);
    $time = $conn->real_escape_string($_POST['time']);
    $notes = $conn->real_escape_string($_POST['notes']);
    
    // التحقق من عدم تكرار الموعد
    $check_sql = "SELECT * FROM appointments WHERE doctor = '$doctor' AND appointment_date = '$date' AND appointment_time = '$time'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $message = "هذا الموعد محجوز بالفعل، يرجى اختيار وقت آخر";
        $messageType = "error";
    } else {
        // إدراج البيانات
        $insert_sql = "INSERT INTO appointments (name, phone, email, doctor, appointment_date, appointment_time, notes) 
                       VALUES ('$name', '$phone', '$email', '$doctor', '$date', '$time', '$notes')";
        
        if ($conn->query($insert_sql) === TRUE) {
            $message = "تم حجز الموعد بنجاح! رقم الحجز: " . $conn->insert_id;
            $messageType = "success";
        } else {
            $message = "خطأ في حجز الموعد: " . $conn->error;
            $messageType = "error";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WellCare - نظام الحجز الطبي</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --baby-blue: #e6f2ff;
            --primary-blue: #89CFF0;
            --dark-blue: #1a73e8;
            --white: #ffffff;
            --light-gray: #f5f5f5;
            --dark-gray: #333333;
            --success: #4CAF50;
            --error: #f44336;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--baby-blue);
            color: var(--dark-gray);
            line-height: 1.6;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .header {
            background-color: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .logo h1 {
            color: var(--dark-blue);
            font-size: 24px;
        }

        .logo i {
            margin-left: 10px;
            color: var(--primary-blue);
        }

        .nav ul {
            display: flex;
            list-style: none;
        }

        .nav ul li {
            margin-right: 20px;
        }

        .nav ul li a {
            text-decoration: none;
            color: var(--dark-gray);
            font-weight: 500;
            transition: color 0.3s;
            cursor: pointer;
        }

        .nav ul li a:hover {
            color: var(--dark-blue);
        }

        .hero {
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            color: var(--white);
            padding: 60px 0;
            text-align: center;
            margin-bottom: 30px;
        }

        .hero h2 {
            font-size: 32px;
            margin-bottom: 15px;
        }

        .section {
            background-color: var(--white);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-gray);
        }

        .form-group label i {
            margin-left: 8px;
            color: var(--primary-blue);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--primary-blue);
            outline: none;
        }

        .btn {
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            margin: 5px;
        }

        .btn-primary {
            background-color: var(--dark-blue);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: #0d5bba;
        }

        .btn i {
            margin-left: 8px;
        }

        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-weight: 500;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .footer {
            background-color: var(--dark-gray);
            color: var(--white);
            padding: 20px 0;
            text-align: center;
            margin-top: 50px;
        }

        @media (max-width: 768px) {
            .header .container {
                flex-direction: column;
            }

            .nav ul {
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <h1><i class="fas fa-heartbeat"></i> WellCare</h1>
            </div>
            
        </div>
    </header>

    <main class="main">
        <section class="hero">
            <div class="container">
                <h2>نظام إدارة المواعيد الطبية</h2>
                <p>احجز موعدك بسهولة</p>
            </div>
        </section>

        <div class="container">
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- تبويب حجز الموعد -->
            <section id="booking" class="section">
                <h3><i class="fas fa-calendar-plus"></i> حجز موعد جديد</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="book">
                    
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> الاسم الكامل</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="phone"><i class="fas fa-phone"></i> رقم الهاتف</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> البريد الإلكتروني</label>
                        <input type="email" id="email" name="email">
                    </div>

                    <div class="form-group">
                        <label for="doctor"><i class="fas fa-user-md"></i> اختر الطبيب</label>
                        <select id="doctor" name="doctor" required>
                            <option value="">-- اختر الطبيب --</option>
                            <option value="dr_ahmed">د. أحمد محمد - أخصائي باطنة</option>
                            <option value="dr_sara">د. سارة علي - أخصائية أطفال</option>
                            <option value="dr_khaled">د. خالد محمود - أخصائي عظام</option>
                            <option value="dr_fatima">د. فاطمة حسن - أخصائية نساء</option>
                            <option value="dr_omar">د. عمر يوسف - أخصائي عيون</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date"><i class="fas fa-calendar-alt"></i> تاريخ الموعد</label>
                        <input type="date" id="date" name="date" required>
                    </div>

                    <div class="form-group">
                        <label for="time"><i class="fas fa-clock"></i> وقت الموعد</label>
                        <input type="time" id="time" name="time" required>
                    </div>

                    <div class="form-group">
                        <label for="notes"><i class="fas fa-comment"></i> ملاحظات</label>
                        <textarea id="notes" name="notes" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> تأكيد الحجز
                    </button>
                </form>
            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2023 WellCare. جميع الحقوق محفوظة</p>
        </div>
    </footer>

    <script>
        // إعداد التاريخ الأدنى لليوم الحالي
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date').min = today;

            // التحقق من ساعات العمل
            document.getElementById('time').addEventListener('change', function() {
                const selectedTime = this.value;
                const hours = parseInt(selectedTime.split(':')[0]);
                
                if (hours < 9 || hours > 17) {
                    alert('ساعات العمل من 9 صباحاً إلى 5 مساءً');
                    this.value = '';
                }
            });
        });
    </script>
</body>
</html>