<?php  
$nam = "";
$style = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST["id"]) || empty($_POST["id"])) {
        $nam = "خطأ: يجب إدخال المعرف.";
        $style = "danger";
    } else {
        try {
            $conn = new mysqli("sql210.infinityfree.com", "if0_38758997", "vjuor32VXQB", "if0_38758997_Res");
            if ($conn->connect_error) {
                throw new Exception("فشل الاتصال: " . $conn->connect_error);
            }

            $Id = intval($_POST["id"]);
            $stmt = $conn->prepare("DELETE FROM Students2 WHERE Id = ?");
            $stmt->bind_param("i", $Id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $nam = "تم حذف المستأجر بنجاح.";
                $style = "success";
            } else {
                $nam = "المستأجر غير موجود.";
                $style = "warning";
            }

            $stmt->close();
            $conn->close();
        } catch (Exception $e) {
            $nam = "حدث خطأ: " . $e->getMessage();
            $style = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>نظام الإقامة</title>
    <style>
        :root {
            --primary: #2ecc71;
            --danger: #e74c3c;
            --warning: #f39c12;
            --success: #27ae60;
            --dark: #2c3e50;
            --light: #f8f9fa;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: var(--light);
        }

        header {
            background-color: var(--primary);
            color: white;
            padding: 20px;
            text-align: center;
        }

        ul {
            background: var(--dark);
            display: flex;
            justify-content: center;
            list-style: none;
        }

        ul li {
            margin: 0 15px;
        }

        ul a {
            color: white;
            text-decoration: none;
            padding: 12px;
            display: block;
        }

        ul a:hover {
            background-color: var(--primary);
            border-radius: 5px;
        }

        .mianadd {
            padding: 20px;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
        }

        form {
            display: grid;
            gap: 10px;
            grid-template-columns: 120px 1fr;
            max-width: 600px;
            margin: auto;
            background-color: rgba(0, 0, 0, 0.05);
            padding: 20px;
            border-radius: 10px;
        }

        label {
            background-color: #7374a5;
            color: white;
            padding: 12px;
            border-radius: 5px;
            font-weight: bold;
        }

        input {
            padding: 12px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .add {
            grid-column: 1 / -1;
            padding: 15px;
            background-color: var(--primary);
            color: white;
            font-weight: bold;
            font-size: 18px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }

        .message {
            grid-column: 1 / -1;
            padding: 12px;
            text-align: center;
            font-weight: bold;
            border-radius: 8px;
        }

        .success { background-color: var(--success); color: white; }
        .danger { background-color: var(--danger); color: white; }
        .warning { background-color: var(--warning); color: black; }
    </style>
</head>
<body>
    <header>
        <h1>نظام الإقامة</h1>
    </header>

    <ul>
        <li><a href="index2.html">الرئيسية</a></li>
        <li><a href="add.php" target="_blank">إضافة</a></li>
        <li><a href="update.php" target="_blank">تعديل</a></li>
    </ul>

    <div class="mianadd">حذف مستأجر</div>

    <form action="" method="POST" class="fromatic">
        <label for="id">المعرف</label>
        <input type="number" name="id" id="id" required>

        <?php if($nam): ?>
            <div class="message <?php echo $style; ?>"><?php echo $nam; ?></div>
        <?php endif; ?>

        <button type="submit" name="send" class="add">حذف</button>
    </form>

    <script>
    document.querySelector('.fromatic').addEventListener('submit', function(e) {
        // يمكن إزالة هذا السطر لو ما تستخدم AJAX
        // e.preventDefault();
        // reset input value بعد الإرسال
        setTimeout(() => {
            document.getElementById('id').value = "";
        }, 500);
    });
    </script>
</body>
</html>