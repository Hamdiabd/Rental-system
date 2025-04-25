<?php
session_start();
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

const MAX_AMOUNT = 1000000; // الحد الأقصى للمبلغ

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // التحقق من CSRF Token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('طلب غير مصرح به');
        }

        // التحقق من المدخلات
        $requiredFields = ['id', 'amount'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception('جميع الحقول مطلوبة');
            }
        }

        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $amount = filter_var($_POST['amount'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => MAX_AMOUNT]]);

        if (!$id || !$amount){
            throw new Exception('قيم غير صالحة');
        }

        // الاتصال بقاعدة البيانات
 $host = "sql210.infinityfree.com";
        $user = "if0_38758997";
        $pass = "vjuor32VXQB";
        $dbname = "if0_38758997_Res";
        
        $conn = new mysqli($host, $user, $pass, $dbname);
        if ($conn->connect_error) {
            throw new Exception('فشل الاتصال بالخادم');
        }

        // التحقق من وجود المستخدم أولاً
        $check = $conn->prepare("SELECT Id FROM Students2 WHERE Id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        
        if (!$check->get_result()->num_rows) {
            throw new Exception('المستأجر غير موجود');
        }

        // تنفيذ العملية
        $update = $conn->prepare("UPDATE Students2 SET Monay_plaint = Monay_plaint + ? WHERE Id = ?");
        $update->bind_param("ii", $amount, $id);
        
        if (!$update->execute()) {
            throw new Exception('فشل في عملية الدفع');
        }

        $response = [
            'status' => 'success',
            'message' => 'تمت العملية بنجاح',
            'new_balance' => $conn->query("SELECT Monay_plaint FROM Students2 WHERE Id = $id")->fetch_assoc()['Monay_plaint']
        ];

        $update->close();
        $conn->close();

    } catch (Exception $e) {
        http_response_code(400);
        $response = [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// توليد CSRF Token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دفع مبلغ</title>
    <style>
        :root {
            --success: #28a745;
            --error: #dc3545;
            --primary: #007bff;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: #f8f9fa;
            padding: 20px;
        }

        form {
            background: white;
            max-width: 500px;
            margin: 2rem auto;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #495057;
        }

        input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ced4da;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
        }

        button {
            background: var(--primary);
            color: white;
            padding: 1rem;
            width: 100%;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: opacity 0.3s;
        }

        button:hover {
            opacity: 0.9;
        }

        #result {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 5px;
            text-align: center;
            display: none;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }

        .loading {
            position: relative;
            pointer-events: none;
            opacity: 0.7;
        }

        .loading::after {
            content: "";
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            border: 3px solid #fff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <form method="POST" class="payment-form">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <div class="form-group">
            <label for="id">رقم المستأجر</label>
            <input type="number" name="id" id="id" required min="1">
        </div>

        <div class="form-group">
            <label for="amount">المبلغ المدفوع (ريال)</label>
            <input type="number" name="amount" id="amount" required min="1" max="1000000">
        </div>

        <button type="submit">إتمام الدفع 💵</button>
        <div id="result"></div>
    </form>

    <script>
        const form = document.querySelector('.payment-form');
        const result = document.getElementById('result');
        const button = form.querySelector('button');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            button.classList.add('loading');
            const formData = new FormData(form);

            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                
                result.style.display = 'block';
                result.className = data.status === 'success' ? 'success' : 'error';
                result.innerHTML = data.message;

                if (data.status === 'success' && data.new_balance) {
                    result.innerHTML += `<br>الرصيد الجديد: ${data.new_balance} ر.ي`;
                }

            } catch (error) {
                result.style.display = 'block';
                result.className = 'error';
                result.textContent = 'حدث خطأ في الاتصال';
            } finally {
                button.classList.remove('loading');
                form.reset();
            }
        });
    </script>
</body>
</html>