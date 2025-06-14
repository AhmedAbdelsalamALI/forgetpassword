<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام شات طبي</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #00bcd4,rgb(22, 113, 189));
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            direction: rtl;
        }

        .container {
            background-color: white;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            text-align: center;
            width: 100%;
            max-width: 450px;
        }

        /* تنسيق عنوان WallCare */
        .brand {
            font-size: 28px;
            font-weight: bold;
            color:rgb(18, 56, 181);
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        h2 {
            font-size: 22px;
            margin-bottom: 30px;
            color: #333;
        }

        .buttons-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        button {
            padding: 14px 30px;
            font-size: 18px;
            background-color:rgb(17, 98, 145);
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        button:hover {
            background-color:rgb(21, 88, 169);
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        a {
            text-decoration: none;
        }

        button:focus {
            outline: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="brand">WallCare</div>
        <h2>مرحبًا بك في نظام الشات بين المريض والدكتور</h2>
        <div class="buttons-container">
          
            <a href="login.php"><button>تسجيل الدخول</button></a>
        </div>
    </div>
</body>
</html>
