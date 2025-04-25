<?php  
// إعدادات اللغة والتاريخ
setlocale(LC_ALL, 'ar_AE.utf8');
date_default_timezone_set('Asia/Riyadh');

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    try{
        // إعدادات اتصال قاعدة البيانات (يفضل نقلها لملف إعدادات منفصل)
    $host = "sql210.infinityfree.com";
        $user = "if0_38758997";
        $pass = "vjuor32VXQB";
        $dbname = "if0_38758997_Res";
        
        $conn = new mysqli($host, $user, $pass, $dbname);
        $conn->set_charset("utf8mb4");

        if($conn->connect_error){
            echo json_encode(['nam'=>"فشل الاتصال بقاعدة البيانات: " . $conn->connect_error,'style'=>"shoop"]);
            exit;
        }

        // قائمة الحقول المطلوبة مع أسماءها المعروضة
        $required_fields = [
            'id' => 'المعرف',
            'Name' => 'الاسم',
            'age' => 'العمر',
            'phone' => 'الهاتف',
            'room' => 'رقم الغرفة',
            'monay' => 'المبلغ المدفوع',
            'Since' => 'تاريخ البدء'
        ];

        // التحقق من الحقول المطلوبة
        foreach($required_fields as $field => $display_name){
            if(!isset($_POST[$field]) || empty(trim($_POST[$field]))){
                echo json_encode(['nam'=>"حقل {$display_name} مطلوب",'style'=>"shoop"]);
                exit;
            }
        }

        // تنظيف وفلترة البيانات
        $id = intval($_POST["id"]);
        $name = $conn->real_escape_string(trim($_POST["Name"]));
        $age = intval($_POST["age"]);
        $phone = preg_replace('/[^0-9+]/', '', $_POST["phone"]);
        $room = intval($_POST["room"]);
        $monay = intval($_POST["monay"]);
        $since = $conn->real_escape_string(trim($_POST["Since"]));

        // التحقق من صحة البيانات
        if($age < 10 || $age > 100){
            echo json_encode(['nam'=>"العمر يجب أن يكون بين 10 و 100 سنة",'style'=>"shoop"]);
            exit;
        }

        if(strlen($phone) < 10 || strlen($phone) > 15){
            echo json_encode(['nam'=>"رقم الهاتف يجب أن يكون بين 10 و 15 رقماً",'style'=>"shoop"]);
            exit;
        }

        if($room < 1 || $room > 999){
            echo json_encode(['nam'=>"رقم الغرفة غير صحيح",'style'=>"shoop"]);
            exit;
        }

        // التحقق من صحة التاريخ
        if(!strtotime($since)){
            echo json_encode(['nam'=>"تاريخ البدء غير صحيح",'style'=>"shoop"]);
            exit;
        }

        // التحقق من وجود السجل
        $check = $conn->prepare("SELECT Id FROM Students2 WHERE Id=?");
        $check->bind_param("i", $id);
        $check->execute();
        $check->store_result();

        if($check->num_rows == 0){
            echo json_encode(['nam'=>"لا يوجد مستأجر بهذا المعرف",'style'=>"shoop"]);
            exit;
        }
        $check->close();

        // التحقق من عدم تكرار الاسم (باستثناء السجل الحالي)
        $name_check = $conn->prepare("SELECT Id FROM Students2 WHERE Name=? AND Id != ?");
        $name_check->bind_param("si", $name, $id);
        $name_check->execute();
        $name_check->store_result();

        if($name_check->num_rows > 0){
            echo json_encode(['nam'=>"هذا الاسم مسجل بالفعل لمستأجر آخر",'style'=>"shoop"]);
            exit;
        }
        $name_check->close();

        // تنفيذ عملية التحديث
        $update = $conn->prepare("UPDATE Students2 SET 
                                Name=?, 
                                Age=?, 
                                Phone=?, 
                                Room=?, 
                                Monay_plaint=?, 
                                Since=?, 
                                UpdatedAt=NOW() 
                                WHERE Id=?");
        
        $update->bind_param("sissisi", $name, $age, $phone, $room, $monay, $since, $id);
        
        if($update->execute()){
            if($update->affected_rows > 0){
                echo json_encode(['nam'=>"تم تحديث بيانات المستأجر بنجاح",'style'=>"shoop1"]);
            } else {
                echo json_encode(['nam'=>"لم يتم تغيير أي بيانات (قد تكون البيانات نفسها)",'style'=>"shoop"]);
            }
        } else {
            echo json_encode(['nam'=>"فشل في تحديث البيانات: " . $conn->error,'style'=>"shoop"]);
        }

        $update->close();
        $conn->close();
        exit;

    } catch(Exception $e){
        error_log("Error in update.php: " . $e->getMessage());
        echo json_encode(['nam'=>"حدث خطأ غير متوقع. يرجى المحاولة لاحقاً",'style'=>"shoop"]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام السكن - تعديل بيانات مستأجر</title>
    <style>
        :root {
            --primary-color: #5ad75a;
            --secondary-color: #767063;
            --accent-color: #7374a5;
            --error-color: #ff4444;
            --success-color: #42fc42;
            --text-color: #333;
            --light-gray: #f5f5f5;
            --medium-gray: #9b9b9b;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: var(--light-gray);
            color: var(--text-color);
            line-height: 1.6;
        }

        header {
            text-align: center;
            background: var(--primary-color);
            padding: 1rem 0;
            box-shadow: var(--shadow);
        }

        h1 {
            color: white;
            font-size: 2rem;
        }

        nav ul {
            display: flex;
            justify-content: center;
            background: var(--secondary-color);
            list-style: none;
            padding: 1rem 0;
        }

        nav ul li {
            margin: 0 1.5rem;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 4px;
        }

        nav ul li a:hover {
            background-color: rgba(255,255,255,0.2);
        }

        .page-title {
            text-align: center;
            margin: 2rem 0;
            font-size: 1.8rem;
            color: var(--secondary-color);
        }

        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: var(--secondary-color);
        }

        input, select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid var(--medium-gray);
            border-radius: 4px;
            font-size: 1rem;
            transition: border 0.3s ease;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px rgba(115, 116, 165, 0.2);
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .message {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
        }

        .error {
            background-color: #ffdddd;
            color: var(--error-color);
        }

        .success {
            background-color: #ddffdd;
            color: var(--success-color);
        }

        .submit-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem;
            font-size: 1.1rem;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .submit-btn:hover {
            background-color: #4bc04b;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            nav ul {
                flex-direction: column;
                align-items: center;
            }
            
            nav ul li {
                margin: 0.5rem 0;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>نظام إدارة السكن الطلابي</h1>
</header>

<nav>
    <ul>
        <li><a href="index2.html">الرئيسية</a></li>
        <li><a href="add.php">إضافة مستأجر جديد</a></li>
        <li><a href="update.php">تعديل بيانات مستأجر</a></li>
        <li><a href="add.php">بحث عن مستأجر</a></li>
        <li><a href="select.php">التقارير</a></li>
    </ul>
</nav>

<main class="form-container">
    <h2 class="page-title">تعديل بيانات مستأجر</h2>
 add
    <form method="POST" class="form-grid" id="updateForm">
        <div class="form-group full-width">
            <label for="id">رقم المعرف (ID) للمستأجر</label>
            <input type="number" name="id" id="id" required min="1" placeholder="أدخل المعرف الخاص بالمستأجر">
        </div>
        
        <div class="form-group full-width">
            <p>البيانات الجديدة</p>
        </div>
        
        <div class="form-group">
            <label for="name">الاسم الكامل</label>
            <input type="text" name="Name" id="name" required placeholder="الاسم الثلاثي">
        </div>
        
        <div class="form-group">
            <label for="age">العمر</label>
            <input type="number" name="age" id="age" required min="10" max="100" placeholder="بين 10 و 100">
        </div>
        
        <div class="form-group">
            <label for="phone">رقم الهاتف</label>
            <input type="tel" name="phone" id="phone" required placeholder="مثال: 9665012367">
        </div>
        
        <div class="form-group">
            <label for="room">رقم الغرفة</label>
            <input type="number" name="room" id="room" required min="1" max="999" placeholder="من 1 إلى 999">
        </div>
        
        <div class="form-group"
            <label for="monay">المبلغ المدفوع (ريال)</label>
            <input type="number" name="monay" id="monay" required min="0" placeholder="المبلغ ">
        </div>
        
        <div class="form-group">
            <label for="since">تاريخ البدء</label>
            <input type="date" name="Since" id="since" required>
        </div>
        
        <div class="form-group full-width" id="messageContainer">
            <!-- ستظهر رسائل النتيجة هنا -->
        </div>
        
        <div class="form-group full-width">
            <button type="submit" class="submit-btn">حفظ التعديلات</button>
        </div>
    </form>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('updateForm');
        const messageContainer = document.getElementById('messageContainer');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // إظهار رسالة تحميل
            messageContainer.innerHTML = '<div class="message">جاري معالجة البيانات...</div>';
            
            const formData = new FormData(form);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.style === "shoop") {
                    messageContainer.innerHTML = `<div class="message error">${data.nam}</div>`;
                } else if(data.style === "shoop1") {
                    messageContainer.innerHTML = `<div class="message success">${data.nam}</div>`;
                    // form.reset(); // يمكن تفعيله إذا أردت مسح النموذج بعد التحديث
                }
            })
            .catch(error => {
                messageContainer.innerHTML = `<div class="message error">حدث خطأ في الاتصال بالخادم</div>`;
                console.error('Error:', error);
            });
        });
        
        // تعيين تاريخ اليوم كقيمة افتراضية لحقل التاريخ
        document.getElementById('since').valueAsDate = new Date();
    });
</script>

</body>
</html>