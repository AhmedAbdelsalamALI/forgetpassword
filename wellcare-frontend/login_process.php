<?php
// بيانات الاتصال بقاعدة البيانات
$servername = "localhost"; // غالبًا ما يكون "localhost"
$username = "root"; // استبدل باسم مستخدم قاعدة البيانات الخاص بك
$password = ""; // استبدل بكلمة مرور قاعدة البيانات الخاصة بك
$dbname = "wellcare_doctors";

// إنشاء اتصال بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من وجود أخطاء في الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// التحقق من إرسال النموذج بطريقة POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // استلام البيانات من النموذج
    $doctorId = $_POST["doctorId"];
    $password = $_POST["password"];

    // تجهيز استعلام SQL لمنع حقن SQL
    $sql = "SELECT doctor_id, username, password FROM doctors WHERE doctor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctorId); // "i" يشير إلى أن doctor_id عدد صحيح

    // تنفيذ الاستعلام
    $stmt->execute();

    // الحصول على نتيجة الاستعلام
    $result = $stmt->get_result();

    // التحقق من وجود طبيب بالمعرف المدخل
    if ($result->num_rows > 0) {
        // جلب بيانات الطبيب
        $row = $result->fetch_assoc();

        // هنا في التطبيقات الحقيقية، يجب عليك التحقق من كلمة المرور المشفرة
        // باستخدام password_verify($password, $row["password"])
        // ولكن بما أننا قمنا بإدخال كلمات مرور نصية بسيطة للتوضيح:
        if ($password == $row["password"]) {
            echo "تم تسجيل الدخول بنجاح! مرحبًا بك يا دكتور " . $row["username"];
           // header("Location: doc_page.html");
           // exit();
        } else {
            // كلمة المرور غير صحيحة
            //echo "كلمة المرور غير صحيحة.";
        }
    } else {
        // لم يتم العثور على طبيب بهذا المعرف
        echo "معرف الطبيب غير موجود.";
    }

    // إغلاق البيان
    $stmt->close();
}

// إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>