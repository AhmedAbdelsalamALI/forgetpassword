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
// الجزء الخاص بمعالجة إرسال الرد من الطبيب
// -----------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['consultation_id']) && isset($_POST['doctor_response'])) {
    $consultationId = (int)$_POST['consultation_id']; // تحويل إلى عدد صحيح لضمان الأمان
    $doctorResponse = trim($conn->real_escape_string($_POST['doctor_response']));

    if (empty($doctorResponse)) {
        $responseMessage = "<p style='color: red;'>خطأ: رد الطبيب لا يمكن أن يكون فارغًا.</p>";
    } else {
        // تحديث عمود doctor_response في الاستشارة المحددة
        $stmt = $conn->prepare("UPDATE medical_consultations SET doctor_response = ? WHERE id = ?");
        $stmt->bind_param("si", $doctorResponse, $consultationId); // "si" تعني string, integer

        if ($stmt->execute()) {
            $responseMessage = "<p style='color: green;'>تم إرسال ردك بنجاح.</p>";
        } else {
            $responseMessage = "<p style='color: red;'>حدث خطأ أثناء حفظ الرد: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WellCare - استشارات المرضى</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e0f7fa; /* لون أزرق فاتح مريح */
            margin: 0;
            padding: 0;
            direction: rtl; /* للغة العربية */
            text-align: right; /* محاذاة النص لليمين */
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 15px 30px;
            text-align: center;
            font-size: 24px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header .logo {
            font-weight: bold;
        }
        .header nav a {
            color: white;
            text-decoration: none;
            margin-left: 25px; /* مسافة بين الروابط في الاتجاه اليمين-يسار */
            font-weight: bold;
            transition: color 0.3s ease;
        }
        .header nav a:hover {
            color: #cce5ff;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: #007bff;
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e0f7fa;
            padding-bottom: 15px;
        }
        .consultation-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .consultation-card h3 {
            color: #343a40;
            margin-top: 0;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .consultation-card h3 i {
            margin-left: 10px; /* مسافة للأيقونة */
            color: #007bff;
        }
        .consultation-card p {
            color: #495057;
            line-height: 1.7;
            margin-bottom: 10px;
        }
        .consultation-card .timestamp {
            font-size: 0.9em;
            color: #6c757d;
            text-align: left; /* تاريخ ووقت الإرسال */
            margin-bottom: 15px;
        }
        .consultation-card img {
            max-width: 100%;
            height: auto;
            display: block;
            margin-top: 15px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            /* إضافة هذه الأنماط لضمان عرض أفضل للصور */
            max-width: 200px; /* لتقليل حجم الصورة إذا كانت كبيرة جداً */
            height: auto;
        }
        .response-section {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px dashed #ced4da;
        }
        .response-section textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #007bff; /* لون حدود أزرق للتمييز */
            border-radius: 5px;
            min-height: 80px;
            margin-bottom: 10px;
            box-sizing: border-box; /* لضمان أن البادينج لا يوسع العرض */
            resize: vertical;
        }
        .response-section button {
            background-color: #28a745; /* لون أخضر لزر الإرسال */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .response-section button:hover {
            background-color: #218838;
        }
        .doctor-response-display {
            background-color: #e2f0d9; /* خلفية خضراء فاتحة للرد */
            border-left: 5px solid #28a745;
            padding: 10px 15px;
            margin-top: 15px;
            border-radius: 5px;
            color: #218838;
        }
        .no-consultations {
            text-align: center;
            color: #6c757d;
            font-size: 1.1em;
            padding: 30px;
            background-color: #e9ecef;
            border-radius: 8px;
        }
        .alert-message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .alert-message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">WellCare</div>
        <nav>
            <a href="doc_page.html">Home</a>
            <a href="doctor_consultations.php">Medical Consultations</a>
        </nav>
    </div>

    <div class="container">
        <h1><i class="fas fa-notes-medical"></i> استشارات المرضى</h1>

        <?php
        // عرض رسالة النجاح أو الخطأ بعد إرسال الرد
        if (isset($responseMessage)) {
            echo "<div class='alert-message'>" . $responseMessage . "</div>";
        }

        // استعلام لجلب جميع الاستشارات، مرتبة من الأحدث للأقدم
        $sql = "SELECT id, consultation_text, image_path, created_at, doctor_response FROM medical_consultations ORDER BY created_at DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // عرض كل استشارة
            while($row = $result->fetch_assoc()) {
                echo "<div class='consultation-card'>";
                echo "<h3><i class='fas fa-user-circle'></i> استشارة جديدة (ID: " . $row["id"] . ")</h3>";
                echo "<p><strong>الاستشارة:</strong> " . nl2br(htmlspecialchars($row["consultation_text"])) . "</p>"; // nl2br لعرض فواصل الأسطر

                // عرض الصورة إذا كانت موجودة
                if (!empty($row["image_path"])) {
                    // بما أن doctor_consultations.php موجود في 'wellcare-frontend/doc_pages/'
                    // ومجلد 'uploads' موجود في 'wellcare-frontend/'
                    // نحتاج للرجوع خطوة للخلف للوصول إلى مجلد uploads
                    $imageSrc = '../' . $row["image_path"]; 
                    echo "<img src='" . htmlspecialchars($imageSrc) . "' alt='صورة مرفقة بالاستشارة'>";
                } else {
                    echo "<p style='color: #888;'>لا توجد صورة مرفقة.</p>";
                }
                echo "<div class='timestamp'>تاريخ الإرسال: " . $row["created_at"] . "</div>";

                // قسم عرض الرد الحالي للطبيب أو نموذج الرد
                echo "<div class='response-section'>";
                if (!empty($row["doctor_response"])) {
                    echo "<p><strong><i class='fas fa-user-md'></i> رد الطبيب:</strong></p>";
                    echo "<div class='doctor-response-display'>" . nl2br(htmlspecialchars($row["doctor_response"])) . "</div>";
                    // يمكنك إضافة زر "تعديل الرد" إذا أردت
                } else {
                    echo "<p><strong><i class='fas fa-reply-all'></i> الرد على الاستشارة:</strong></p>";
                    // نموذج الرد
                    echo "<form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' method='POST'>";
                    echo "<input type='hidden' name='consultation_id' value='" . $row["id"] . "'>";
                    echo "<textarea name='doctor_response' placeholder='اكتب ردك هنا...' required></textarea>";
                    echo "<button type='submit'><i class='fas fa-paper-plane'></i> إرسال الرد</button>";
                    echo "</form>";
                }
                echo "</div>"; // End response-section
                echo "</div>"; // End consultation-card
            }
        } else {
            echo "<div class='no-consultations'>لا توجد استشارات جديدة لعرضها حالياً.</div>";
        }

        $conn->close(); // إغلاق الاتصال بقاعدة البيانات
        ?>

    </div>
</body>
</html>