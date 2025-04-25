<?php
$host = "sql210.infinityfree.com";
$user = "if0_38758997";
$pass = "vjuor32VXQB";
$dbname = "if0_38758997_Res";

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) {
        throw new Exception("فشل الاتصال بقاعدة البيانات");
    }

    $result = $conn->query("SELECT * FROM Students2");
    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    $conn->close();
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام الإقامة - قائمة المستأجرين</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }

        body {
            background: #f5f6fa;
        }

        header {
            background: #2c3e50;
            color: white;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .nav-menu {
            background: #34495e;
            padding: 1rem;
        }

        .nav-menu ul {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
            list-style: none;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            background: #3b5998;
            transition: background 0.3s;
        }

        .nav-menu a:hover {
            background: #2e4a84;
        }

        .filters {
            margin: 1rem auto;
            width: 95%;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            justify-content: center;
        }

        .filter-btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 4px;
            background: #3b5998;
            color: white;
            cursor: pointer;
            transition: opacity 0.3s;
        }

        .filter-btn:hover {
            opacity: 0.9;
        }

        .table-container {
            width: 95%;
            margin: 2rem auto;
            overflow-x: auto;
            border-radius: 8px;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .data-table th,
        .data-table td {
            padding: 1rem;
            text-align: center;
            border-bottom: 1px solid #ecf0f1;
        }

        .data-table th {
            background: #3b5998;
            color: white;
            font-weight: bold;
        }

        .data-table tr:hover {
            background: #f8f9fa;
        }

        .data-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .error-message {
            padding: 2rem;
            text-align: center;
            color: #e74c3c;
            font-weight: bold;
        }

        @media (max-width: 600px) {
            header h1 {
                font-size: 1.2rem;
            }

            .filter-btn {
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
            }

            .nav-menu a {
                font-size: 0.9rem;
                padding: 0.4rem 0.8rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>نظام إدارة الإقامات</h1>
    </header>

    <nav class="nav-menu">
        <ul>
            <li><a href="index2.html"taret="_blank">الرئيسية</a></li>
            <li><a href="add.php"taret="_blank">إضافة مستأجر</a></li>
            <li><a href="update.php"taret="_blank">تعديل بيانات</a></li>
        </ul>
    </nav>

    <div class="filters">
        <button class="filter-btn">جميع المستأجرين</button>
        <button class="filter-btn">منتهي المدة</button>
        <button class="filter-btn">قريب الانتهاء</button>
    </div>

    <?php if(isset($error)): ?>
        <div class="error-message">
            <?php echo $error; ?>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>الاسم</th>
                        <th>العمر</th>
                        <th>الهاتف</th>
                        <th>الغرفة</th>
                        <th>الدفعات</th>
                        <th>تاريخ البدء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($rows)): ?>
                        <?php foreach($rows as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Id']); ?></td>
                                <td><?php echo htmlspecialchars($row['Name']); ?></td>
                                <td><?php echo htmlspecialchars($row['Age']); ?></td>
                                <td><?php echo htmlspecialchars($row['Phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['Room']); ?></td>
                                <td><?php echo number_format($row['Monay_plaint'], 2); ?> ر.ي</td>
                                <td><?php echo date('Y-m-d', strtotime($row['Since'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">لا توجد بيانات متاحة</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</body>
</html>