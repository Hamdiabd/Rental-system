<?php  
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $required_fields = ['Name',"age","phone","room","monay","Since"];
    foreach($required_fields as $field){
        if(!isset($_POST[$field]) || empty($_POST[$field])){
   echo json_encode(['nam'=>"يجب ادخال الحقل الفارغ",'style'=>"shoop"]);
exit;
        }
    }

    try {
 $host = "sql210.infinityfree.com";
        $user = "if0_38758997";
        $pass = "vjuor32VXQB";
        $dbname = "if0_38758997_Res";

        $conn = new mysqli($host, $user, $pass, $dbname);
        if ($conn->connect_error) {
            throw new Exception("فشل الاتصال: " . $conn->connect_error);
        }

        $conn->set_charset("utf8mb4");

        $name = $_POST["Name"];
 $check = $conn->prepare("SELECT * FROM Students2 WHERE Name = ?");
        $check->bind_param("s", $name);
        $check->execute();
        $result = $check->get_result();
        if ($result->num_rows > 0) 
        {
echo json_encode(['nam'=>"الاسم موجود مسبقًا",'style'=>"shoop"]);exit;
        }
        else{
        $va2 = $name;
        $va3 = intval($_POST["age"]);
        $va4 = $_POST["phone"];
        $va5 = intval($_POST["room"]);
        $va6 = $_POST["monay"];
        $va7 = $_POST["Since"];

        $adds = $conn->prepare("INSERT INTO Students2 (Name, Age, Phone, Room, Monay_plaint, Since) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$adds) {
            throw new Exception("خطأ في التحضير: " . $conn->error);
        }

   $adds->bind_param("sissis", $va2, $va3, $va4, $va5, $va6, $va7);
        if (!$adds->execute()) {
            throw new Exception("فشل التنفيذ: " . $adds->error);
        }

        $adds->close();
        $conn->close();
        
   echo json_encode(['nam'=>" تم الادخال بنجاح!",'style'=>"shoop1"]);
   exit;
        }
    } catch(Exception $e){
        echo "حدث خطأ: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>

    <html lang="ar">
        <head>
            <meta charset="utf-8">

            <title>
                resci
            </title>

              <style>

                   * {
            box-sizing: border-box;
            margin: 0;
            font-family: Arial, sans-serif;
        }
         

                header{
         text-align: center;
     flex-direction: column;
         display: flex;
             background: #10E344D6;

                   justify-content: space-evenly;

                }

          ul{      
            font-size: 20px;  
     text-transform: capitalize;
          color: white;
            list-style-type: none;
                    justify-content: space-evenly;
                    display: flex;
         background: #000000D4;
                    padding: 10px;
               }
               h1{
                padding: 16px;
               }
               a{              
                text-decoration:none;
             color: white;
               }
    
        form{
         border-radius:20px;
        display: grid;
    max-width: 700px;
    margin: auto;
     direction: rtl;                 
    padding: 20px;
background-color:rgba(125, 145, 161, 0.22);
gap:20px;
  grid-template-columns:160px 450px;
                   }
                label{
                    
      background-color: #38941EB0;
       color: white;
      padding: 10px;
      font-size: 20px; 
       font-weight: bold;
                   }
                   input{
         
                   padding: 10px;
                   font-size: 16px;
                 border-radius: 10px;
                  border: 1px solid #7374a9;
                   }
      .add{
         background: #42fc42;
         grid-column: 1/3;
         padding: 10px;
        border-radius: 20px;
        font: bold 20px cursive; 
               }
               
               .mianadd
               {
                padding: 20px;
  background-color: rgba(155, 147, 198, 0.51);
 text-align: center;
   font: bold italic 20px cursor;
               }    
          @media(max-width:700px) {
              
     form{
         width:400px;
         margin:auto;
        display:grid;
        
     grid-template-columns:120px 250px ;
                       font-size:10px;
                       gap:10px;
                   }
                   
               }
               .shoop,.mm{
                 padding:5px;
                 background:green;
               }
               .respite{
                 padding:5px;
                 background:red;
               }
            </style>
        </head>
        <body> 
          <header>
                <h1>system redidrnce</h1>

            </header>
            <div>
                <ul>
           <li><a href="index2.html"target="_blank">ٍالرئيسية</a></li>

          <li><a href="add.php"target="_blank">اضافة </a> </li>
           <li><a href="update.php"target="_blank">تعديل</a></li>                  
                </ul>
            </div>
<div class="mianadd">Enter Student</div>
<form action=""method="POST"class="fromatic">
              <label for="Name">الاسم</label>
                 <input type="text"name="Name"id="Name"required>              
                 <label for="age">العمر</label>
                 <input type="number"name="age"id="age"required>
                 <label for="phone">التلفون</label>
                <input type="number"name="phone"id="phone"required>
                 <label for="room">الغرفة</label>
                 <input type="number"name="room"id="room"required>
                 <label for="monay">المبلغ المدفوع</label>
                <input type="number"name="monay"id="monay"required>
                 <label for="Since">المدة</label>
                <input type="date"name="Since"id="Since"required>
                
               <div id="mm"></div>
              
         <button type="submit"name="send"class="add">Send</button>
            </form>
            
        <script>
    document.querySelector('.fromatic').addEventListener('submit', function(e){
       e.preventDefault();
    const form = this;
    const formData = new FormData(form);

    fetch('', {method:'POST',body: formData })
    .then(response => response.json())
    .then(data => {
      const id=document.getElementById ("mm");
      if(data.style==="shoop")
      {
 id.innerHTML=`<span style="background:red;padding:5px;margin:auto;">${data.nam}</span>`;
      }
     if(data.style==="shoop1")
      {
   id.innerHTML=`<span style="background:green;padding:5px;margin: auto;">${data.nam}</span>`;
      }
      
         form.reset();

    });
    });
    </script>
        </body>
        
    </html>
    
 