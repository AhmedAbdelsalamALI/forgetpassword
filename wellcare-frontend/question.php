<?php
// إعدادات الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wellcare_db";

// إنشاء اتصال بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// -----------------------------------------------------
// الجزء الخاص بمعالجة إرسال الاستشارة من المريض
// -----------------------------------------------------
$responseMessage = ""; // لرسائل النجاح/الخطأ التي ستعرض في الصفحة

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $consultationText = trim($conn->real_escape_string($_POST['consultation_text']));

    if (empty($consultationText)) {
        $responseMessage = "<div class='message error'>خطأ: نص الاستشارة لا يمكن أن يكون فارغًا.</div>";
    } else {
        $imagePath = NULL; // افتراضياً لا توجد صورة

        // التحقق من وجود ملف الصورة وتحميله
        if (isset($_FILES['consultation_image']) && $_FILES['consultation_image']['error'] == 0) {
            $targetDir = "uploads/"; 
            // تأكد أن هذا المجلد موجود وقابل للكتابة في مسار مشروعك
            // C:\xampp\htdocs\wellcare-frontend\uploads\
            
            $fileName = basename($_FILES["consultation_image"]["name"]);
            $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = uniqid() . "." . $fileType; // توليد اسم فريد للصورة
            $targetFilePath = $targetDir . $newFileName;
            $allowTypes = array('jpg', 'png', 'jpeg', 'gif');

            if (in_array($fileType, $allowTypes)) {
                if ($_FILES['consultation_image']['size'] > 5 * 1024 * 1024) { // حجم أقصى 5 ميجابايت
                    $responseMessage = "<div class='message error'>خطأ: حجم الصورة كبير جداً (الحد الأقصى 5 ميجابايت).</div>";
                } else {
                    if (move_uploaded_file($_FILES["consultation_image"]["tmp_name"], $targetFilePath)) {
                        $imagePath = $targetFilePath;
                    } else {
                        // هذا الخطأ عادةً ما يشير إلى مشكلة في الصلاحيات أو المسار
                        $responseMessage = "<div class='message error'>خطأ: حدث خطأ أثناء تحميل الصورة. (تحقق من مجلد uploads وصلاحياته).</div>";
                    }
                }
            } else {
                $responseMessage = "<div class='message error'>خطأ: نوع الملف غير مسموح به. يُسمح فقط بملفات JPG, JPEG, PNG, GIF.</div>";
            }
        }

        // إذا لم يكن هناك خطأ في تحميل الصورة أو لم تكن هناك صورة، قم بحفظ الاستشارة في قاعدة البيانات
        // نتحقق من عدم وجود كلمة 'خطأ' في رسالة $responseMessage
        if (!strpos($responseMessage, 'خطأ')) { 
            $stmt = $conn->prepare("INSERT INTO medical_consultations (consultation_text, image_path) VALUES (?, ?)");
            $stmt->bind_param("ss", $consultationText, $imagePath);

            if ($stmt->execute()) {
                // ****** التعديل الجديد هنا: POST/Redirect/GET ******
                // حفظ رسالة النجاح في session قبل إعادة التوجيه
                session_start(); // ابدأ جلسة PHP
                $_SESSION['response_message'] = "<div class='message success'>تم إرسال استشارتك بنجاح. سيتم الرد عليك قريباً.</div>";
                header("Location: " . $_SERVER['PHP_SELF']); // إعادة توجيه إلى نفس الصفحة باستخدام GET
                exit(); // إنهاء السكريبت لمنع تنفيذ أي كود بعد إعادة التوجيه
                // ***************************************************
            } else {
                $responseMessage = "<div class='message error'>حدث خطأ أثناء حفظ الاستشارة: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }
    }
}

// ****** التعديل الجديد هنا: عرض الرسالة من session ******
session_start(); // ابدأ جلسة PHP (إذا لم تكن قد بدأت في الأعلى)
if (isset($_SESSION['response_message'])) {
    $responseMessage = $_SESSION['response_message'];
    unset($_SESSION['response_message']); // حذف الرسالة بعد عرضها لمرة واحدة
}
// *****************************************************
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WellCare - أسئلة و أجوبة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css"> 
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: flex-start; /* تغيير ليسمح للمحتوى بالتدفق عموديا من الأعلى */
            align-items: center; /* سنبقى على هذا لتوسيط المحتوى الرئيسي أفقياً */
            min-height: 100vh;
            margin: 0;
            flex-direction: column; /* لجعل العناصر تتكدس عموديا */
            padding-top: 80px; /* مسافة للـ header الثابت */
        }
        .header {
            width: 100%;
            background-color: #fff;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed; /* لجعل الهيدر ثابتا في الأعلى */
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .header .logo {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .header nav a {
            margin-right: 20px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 500px;
            text-align: center;
            margin-top: 30px; /* لزيادة المسافة بعد الهيدر */
            margin-bottom: 50px; /* لإعطاء مساحة للمحتوى السفلي */
        }
        h2 {
            color: #333;
            margin-bottom: 30px;
            position: relative;
            display: inline-block;
            padding: 10px 20px;
            border: 1px solid #007bff;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: right;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            resize: vertical;
            min-height: 100px;
        }
        input[type="file"] {
            width: 100%;
            padding: 10px 0;
        }
        .submit-button {
            background-color: #007bff;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .submit-button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px; /* أضفت هامش سفلي للرسالة */
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

        /* أنماط قسم عرض الاستشارات في الأسفل */
        .consultations-list-section {
            width: 90%;
            max-width: 800px; /* يمكن أن تكون أعرض من نموذج الإرسال */
            margin-top: 40px;
            margin-bottom: 50px; /* هامش سفلي جيد */
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: right; /* محاذاة النص لليمين */
        }
        .consultations-list-section h3 {
            color: #333;
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .consultations-list-section h3 i {
            margin-left: 10px; /* مسافة للأيقونة */
            color: #007bff;
        }
        .consultation-item {
            background-color: #fcfcfc;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        .consultation-item p {
            color: #444;
            line-height: 1.6;
            margin-bottom: 8px;
        }
        .consultation-item .timestamp {
            font-size: 0.85em;
            color: #777;
            text-align: left; /* تاريخ ووقت الإرسال */
            margin-top: 10px;
        }
        .consultation-item img {
            max-width: 100%;
            height: auto;
            display: block;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            /* إضافة هذه الأنماط لضمان عرض أفضل للصور */
            max-width: 200px; /* لتقليل حجم الصورة إذا كانت كبيرة جداً */
            height: auto;
        }
        .doctor-response-display {
            background-color: #e6f7ff; /* لون أزرق فاتح جدا للرد */
            border-left: 4px solid #007bff;
            padding: 10px 15px;
            margin-top: 15px;
            border-radius: 5px;
            color: #0056b3;
            font-weight: bold;
        }
        .no-consultations-yet {
            text-align: center;
            color: #888;
            padding: 20px;
            background-color: #f0f0f0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">WellCare</div>
        <nav>
            <a href="index.html">Home</a> <a href="question.php">Medical Questions</a>
        </nav>
    </div>

    <div class="container">
        <h2>أسئلة و أجوبة</h2>
        <div class="form-description">
            <p style="color: #666; margin-bottom: 25px;">اطرح سؤالك وسيتم الرد عليك من قبل الدكتور المختص</p>
        </div>

        <?php if (!empty($responseMessage)) { echo $responseMessage; } ?>

        <form action="question.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <textarea name="consultation_text" placeholder="اكتب استشارتك هنا..." required></textarea>
            </div>
            <div class="form-group">
                <label for="consultation_image" style="text-align: right; margin-bottom: 10px;">إرفاق صورة (اختياري):</label>
                <input type="file" name="consultation_image" id="consultation_image" accept="image/*">
            </div>
            <button type="submit" class="submit-button">إرسال</button>
        </form>
    </div>

    <div class="consultations-list-section">
        <h3><i class="fas fa-history"></i> استشاراتك السابقة والردود عليها</h3>

        <?php
        // استعلام لجلب جميع الاستشارات، مرتبة من الأحدث للأقدم
        // (إذا كان لديك نظام مستخدمين، هنا ستضيف WHERE user_id = [id_المستخدم_الحالي])
        $sql = "SELECT id, consultation_text, image_path, created_at, doctor_response FROM medical_consultations ORDER BY created_at DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // عرض كل استشارة
            while($row = $result->fetch_assoc()) {
                echo "<div class='consultation-item'>";
                echo "<p><strong>سؤالك:</strong> " . nl2br(htmlspecialchars($row["consultation_text"])) . "</p>";
                
                // عرض الصورة إذا كانت موجودة
                if (!empty($row["image_path"])) {
                    // بما أن question.php موجود في 'wellcare-frontend/'
                    // ومجلد 'uploads' موجود أيضاً في 'wellcare-frontend/'
                    // فإن المسار النسبي هو نفسه المحفوظ في قاعدة البيانات
                    $imageSrc = $row["image_path"]; 
                    echo "<img src='" . htmlspecialchars($imageSrc) . "' alt='صورة مرفقة بالاستشارة'>";
                } else {
                    echo "<p style='color: #888; font-size: 0.9em;'>لا توجد صورة مرفقة.</p>";
                }
                echo "<div class='timestamp'>تاريخ الإرسال: " . $row["created_at"] . "</div>";

                // عرض رد الطبيب إذا كان موجودًا
                if (!empty($row["doctor_response"])) {
                    echo "<div class='doctor-response-display'>";
                    echo "<p><strong><i class='fas fa-user-md'></i> رد الطبيب:</strong></p>";
                    echo "<p>" . nl2br(htmlspecialchars($row["doctor_response"])) . "</p>";
                    echo "</div>";
                } else {
                    echo "<p style='color: #007bff; font-weight: bold;'>لم يتم الرد على هذه الاستشارة بعد. يرجى الانتظار.</p>";
                }
                echo "</div>"; // End consultation-item
            }
        } else {
            echo "<div class='no-consultations-yet'>لم يتم إرسال أي استشارات بعد.</div>";
        }

        // إغلاق الاتصال بقاعدة البيانات في النهاية
        $conn->close(); 
        ?>
    </div>

</body>
</html>