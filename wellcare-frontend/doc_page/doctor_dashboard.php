<?php
// تكوين قاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medical_clinic";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// متغير لتحديد الدكتور (يمكنك تغييره حسب الدكتور المسجل دخوله)
$current_doctor = "dr_ahmed"; // أو أي دكتور آخر حسب نظام تسجيل الدخول

// استدعاء مواعيد الدكتور المحدد فقط
$sql = "SELECT * FROM appointments WHERE doctor = '$current_doctor' ORDER BY appointment_date ASC, appointment_time ASC";
$result = $conn->query($sql);

$appointments = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}

// أسماء الأطباء
$doctors = [
    'dr_ahmed' => 'د. أحمد محمد - أخصائي باطنة',
    'dr_sara' => 'د. سارة علي - أخصائية أطفال',
    'dr_khaled' => 'د. خالد محمود - أخصائي عظام',
    'dr_fatima' => 'د. فاطمة حسن - أخصائية نساء',
    'dr_omar' => 'د. عمر يوسف - أخصائي عيون'
];

$doctor_name = $doctors[$current_doctor] ?? 'الدكتور';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مواعيد الدكتور - <?php echo $doctor_name; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #1a73e8;
            --light-blue: #e3f2fd;
            --white: #ffffff;
            --light-gray: #f5f5f5;
            --dark-gray: #333333;
            --success: #4CAF50;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--light-blue);
            color: var(--dark-gray);
            line-height: 1.6;
        }

        .container {
            width: 95%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: var(--white);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .header h1 {
            color: var(--primary-blue);
            margin-bottom: 10px;
        }

        .header p {
            color: var(--dark-gray);
            font-size: 18px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: var(--white);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-right: 4px solid var(--primary-blue);
        }

        .stat-card i {
            font-size: 40px;
            margin-bottom: 10px;
            color: var(--primary-blue);
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
            color: var(--primary-blue);
        }

        .appointments-section {
            background-color: var(--white);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .section-title {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: var(--primary-blue);
        }

        .section-title i {
            margin-left: 10px;
        }

        .appointments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .appointments-table th,
        .appointments-table td {
            padding: 15px;
            text-align: right;
            border-bottom: 1px solid #e0e0e0;
        }

        .appointments-table th {
            background-color: var(--light-gray);
            font-weight: 600;
            color: var(--dark-gray);
        }

        .appointments-table tr:hover {
            background-color: #f9f9f9;
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-align: center;
            background-color: #d4edda;
            color: #155724;
        }

        .appointment-card {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border-right: 4px solid var(--primary-blue);
        }

        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .patient-name {
            font-size: 18px;
            font-weight: bold;
            color: var(--primary-blue);
        }

        .appointment-time {
            background-color: var(--primary-blue);
            color: var(--white);
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
        }

        .appointment-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .detail-item {
            display: flex;
            align-items: center;
        }

        .detail-item i {
            margin-left: 8px;
            color: var(--primary-blue);
            width: 16px;
        }

        .no-appointments {
            text-align: center;
            padding: 50px;
            color: #666;
        }

        .no-appointments i {
            font-size: 60px;
            color: #ccc;
            margin-bottom: 20px;
        }

        .refresh-btn {
            background-color: var(--primary-blue);
            color: var(--white);
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .refresh-btn:hover {
            background-color: #0d5bba;
        }

        @media (max-width: 768px) {
            .appointments-table {
                font-size: 14px;
            }
            
            .appointments-table th,
            .appointments-table td {
                padding: 10px 8px;
            }

            .appointment-details {
                grid-template-columns: 1fr;
            }

            .appointment-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-user-md"></i> لوحة تحكم الطبيب</h1>
            <p><?php echo $doctor_name; ?></p>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="stats">
            <div class="stat-card">
                <i class="fas fa-calendar-alt"></i>
                <div class="stat-number"><?php echo count($appointments); ?></div>
                <div>إجمالي المواعيد المؤكدة</div>
            </div>
        </div>

        <!-- قسم المواعيد -->
        <div class="appointments-section">
            <div class="section-title">
                <i class="fas fa-list"></i>
                مواعيدك المحجوزة
            </div>

            <button class="refresh-btn" onclick="location.reload()">
                <i class="fas fa-sync-alt"></i>
                تحديث
            </button>

            <?php if (!empty($appointments)): ?>
                
                <!-- عرض البطاقات (للشاشات الصغيرة) -->
                <div class="appointments-cards" style="display: none;">
                    <?php foreach ($appointments as $appointment): ?>
                        <div class="appointment-card">
                            <div class="appointment-header">
                                <div class="patient-name">
                                    <i class="fas fa-user"></i>
                                    <?php echo htmlspecialchars($appointment['name']); ?>
                                </div>
                                <div class="appointment-time">
                                    <?php echo $appointment['appointment_date']; ?> - 
                                    <?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?>
                                </div>
                            </div>
                            
                            <div class="appointment-details">
                                <div class="detail-item">
                                    <i class="fas fa-phone"></i>
                                    <?php echo htmlspecialchars($appointment['phone']); ?>
                                </div>
                                
                                <?php if ($appointment['email']): ?>
                                <div class="detail-item">
                                    <i class="fas fa-envelope"></i>
                                    <?php echo htmlspecialchars($appointment['email']); ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="detail-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span class="status">مؤكد</span>
                                </div>
                                
                                <?php if ($appointment['notes']): ?>
                                <div class="detail-item" style="grid-column: 1/-1;">
                                    <i class="fas fa-comment"></i>
                                    <strong>ملاحظات:</strong> <?php echo htmlspecialchars($appointment['notes']); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- الجدول (للشاشات الكبيرة) -->
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>رقم الحجز</th>
                            <th>اسم المريض</th>
                            <th>رقم الهاتف</th>
                            <th>البريد الإلكتروني</th>
                            <th>التاريخ</th>
                            <th>الوقت</th>
                            <th>الحالة</th>
                            <th>الملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?php echo $appointment['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($appointment['name']); ?></strong>
                                </td>
                                <td>
                                    <a href="tel:<?php echo $appointment['phone']; ?>">
                                        <i class="fas fa-phone" style="color: var(--primary-blue);"></i>
                                        <?php echo htmlspecialchars($appointment['phone']); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($appointment['email']): ?>
                                        <a href="mailto:<?php echo $appointment['email']; ?>">
                                            <i class="fas fa-envelope" style="color: var(--primary-blue);"></i>
                                            <?php echo htmlspecialchars($appointment['email']); ?>
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #ccc;">غير محدد</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo $appointment['appointment_date']; ?></strong>
                                </td>
                                <td>
                                    <strong><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></strong>
                                </td>
                                <td>
                                    <span class="status">مؤكد</span>
                                </td>
                                <td>
                                    <?php if ($appointment['notes']): ?>
                                        <span title="<?php echo htmlspecialchars($appointment['notes']); ?>">
                                            <i class="fas fa-comment" style="color: var(--primary-blue);"></i>
                                            <?php echo strlen($appointment['notes']) > 30 ? 
                                                substr(htmlspecialchars($appointment['notes']), 0, 30) . '...' : 
                                                htmlspecialchars($appointment['notes']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #ccc;">لا توجد</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else: ?>
                <div class="no-appointments">
                    <i class="fas fa-calendar-times"></i>
                    <h3>لا توجد مواعيد محجوزة</h3>
                    <p>لم يتم حجز أي مواعيد معك حتى الآن</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // تحديث الصفحة كل 30 ثانية للحصول على أحدث المواعيد
        setTimeout(function() {
            location.reload();
        }, 30000);

        // إظهار البطاقات على الشاشات الصغيرة
        function checkScreenSize() {
            if (window.innerWidth <= 768) {
                document.querySelector('.appointments-table').style.display = 'none';
                document.querySelector('.appointments-cards').style.display = 'block';
            } else {
                document.querySelector('.appointments-table').style.display = 'table';
                document.querySelector('.appointments-cards').style.display = 'none';
            }
        }

        checkScreenSize();
        window.addEventListener('resize', checkScreenSize);
    </script>
</body>
</html>

<?php
$conn->close();
?>