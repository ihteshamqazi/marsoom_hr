<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> تصحيح بيانات الحضور    </title>

    <!-- تضمين خط Tajawal من Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <!-- تضمين Font Awesome لإيقونات رائعة -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery UI CSS (لتصميم Datepicker) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">

 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
       
    <!-- تضمين Font Awesome لإيقونات رائعة -->
    

    <!-- Bootstrap 5 CSS -->
     

    <!-- jQuery -->
     



    <style>
        /* الخط الأساسي للجسم وتأثير الخلفية */
        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%); /* تدرج لوني رمادي-أزرق فاتح وناعم */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center; /* توسيط عمودي */
            align-items: center; /* توسيط أفقي */
            padding: 20px;
            overflow-x: hidden; /* لمنع ظهور شريط التمرير الأفقي */
        }

        /* حاوية الصفحة الرئيسية */
        .container-fluid {
            max-width: 700px; /* تحديد عرض مناسب لشاشة الإدخال */
            background-color: #ffffff;
            border-radius: 25px; /* حواف أكثر استدارة */
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.18); /* ظل أعمق وأكثر انتشاراً */
            padding: 40px;
            animation: fadeInScale 0.9s ease-out forwards; /* أنيميشن دخول للحاوية */
            position: relative; /* لتحديد موقع العنوان بدقة */
        }

        /* عنوان الشاشة */
        .page-header {
            font-family: 'Tajawal', sans-serif;
            font-weight: 800;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.5rem; /* حجم أكبر للعنوان */
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.15);
            animation: fadeInDown 0.8s ease-out forwards; /* أنيميشن دخول للعنوان */
            position: relative;
        }
        .page-header::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 5px;
            background: linear-gradient(90deg, #6a93cb, #88b0eb); /* شريط زخرفي تحت العنوان */
            border-radius: 5px;
        }

        /* تصميم حقول الإدخال */
        .form-control, .form-select {
            border-radius: 10px; /* حواف مستديرة */
            border: 1px solid #cce0ee; /* حد أزرق فاتح */
            padding: 10px 15px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05); /* ظل داخلي خفيف */
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #6a93cb;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(106, 147, 203, 0.25); /* ظل تركيز أزرق فاتح */
        }

        /* تنسيق Labels */
        label {
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
            display: block; /* لجعلها تأخذ سطرًا كاملاً */
        }
        
        /* زر الحفظ */
        .btn-save {
            background: linear-gradient(90deg, #537bbd, #6a93cb); /* تدرج لوني أزرق فاتح وجذاب */
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
            transition: all 0.3s ease, transform 0.2s ease;
            width: 100%; /* زر يملأ العرض */
            margin-top: 20px;
        }

        .btn-save:hover {
            background: linear-gradient(90deg, #4168a2, #537bbd); /* تدرج أزرق أغمق عند التحليق */
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* رسوم متحركة عامة */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.97); }
            to { opacity: 1; transform: scale(1); }
        }

        /* تخصيص Datepicker لتناسب التصميم */
        .ui-datepicker {
            font-family: 'Tajawal', sans-serif;
            background-color: #ffffff;
            border: 1px solid #e0e6ed;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 15px;
            z-index: 1000 !important; /* لضمان ظهورها فوق العناصر الأخرى */
        }

        .ui-datepicker-header {
            background: linear-gradient(90deg, #537bbd, #6a93cb);
            color: white;
            border-radius: 8px 8px 0 0;
            padding: 10px 0;
            margin-bottom: 10px;
            position: relative;
        }

        .ui-datepicker-title {
            text-align: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .ui-datepicker-prev, .ui-datepicker-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            transition: background-color 0.2s ease;
        }
        .ui-datepicker-prev:hover, .ui-datepicker-next:hover {
            background-color: rgba(255, 255, 255, 0.4);
        }
        .ui-datepicker-prev { right: 10px; }
        .ui-datepicker-next { left: 10px; }

        .ui-datepicker-calendar th {
            color: #555;
            font-weight: 700;
            padding: 8px;
        }

        .ui-datepicker-calendar td {
            padding: 5px;
        }
        .ui-datepicker-calendar td span, .ui-datepicker-calendar td a {
            display: block;
            padding: 8px;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .ui-state-default { background-color: transparent; }
        .ui-state-highlight, .ui-state-active {
            background-color: #eef7ff;
            color: #537bbd;
        }
        .ui-state-hover {
            background-color: #e0f2f7;
            color: #2c3e50;
        }
        .ui-state-active, .ui-state-active:hover {
            background: linear-gradient(90deg, #537bbd, #6a93cb) !important;
            color: white !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        /* استجابة الشاشة الصغيرة */
        @media (max-width: 767px) {
            .container-fluid {
                padding: 25px;
            }
            .page-header {
                font-size: 2rem;
            }
            .btn-save {
                padding: 10px 20px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <h1 class="page-header"> تصحيح بيانات الحضور    </h1>
<?php echo validation_errors(); ?>
             <?php echo form_open_multipart('users1/exemption/'.$id.'/'.$id2); ?>
        <form id="leaveForm">
              
     

   

            
             
            <div class="mb-4">
                <label for="requestType">نوع الطلب  <i class="fas fa-clipboard-list text-danger"></i></label>
                <select name="type" class="form-select" id="requestType" required>
                    <option selected disabled value="">اختر نوع  الاجراء ...</option>
                   <?php if(in_array($this->session->userdata('username') ?? '', array('1835', '1001','2901','2774'))): ?>
<option value="1">اعفاء من البصمة المنفردة</option>
<?php endif; ?>
                    <option value="2">اعفاء من مخالفات التأخير والخروج المبكر</option>
                    <option value="3">الاعفاء من خصم الغياب</option>
<?php if(in_array($this->session->userdata('username') ?? '', array('1835', '1001','2901','2774'))): ?>
                    <option value="4">الاعفاء من جميع المخالفات</option>
                    <?php endif; ?>

                    <option value="5">ايقاف الراتب</option>
                   
                </select>
            </div>
            <button type="submit" class="btn btn-save">
                <i class="fas fa-save me-2"></i> حفظ بيانات الإجازة
            </button>
       <?php echo form_close(); ?>
    </div>

    <!-- jQuery -->
   
    <!-- jQuery UI JS (لتفعيل Datepicker) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
$(document).ready(function() {
    $('#employeeId').on('input', function() {
        var employeeId = $(this).val(); // الحصول على الرقم الوظيفي المدخل

        if(employeeId != '') {
            // إرسال طلب AJAX إلى الخادم
            $.ajax({
                url: '<?php echo site_url("users/get_employee_data"); ?>', // رابط الوحدة في CodeIgniter
                method: 'POST',
                data: { employee_id: employeeId }, // تمرير الرقم الوظيفي إلى الخادم
                dataType: 'json', // استلام البيانات بتنسيق JSON
                success: function(response) {
                    if(response.status == 'success') {
                        // عرض اسم الموظف في الحقل إذا تم العثور على الموظف
                        $('#employeeName').val(response.employee_name);
                    } else {
                       
                    }
                },
                error: function(xhr, status, error) {
                    // طباعة تفاصيل الخطأ في الـ Console لمساعدتنا في التشخيص
                    console.log("AJAX Error: " + error);
                    console.log("Status: " + status);
                    console.log("XHR Response: " + xhr.responseText);
                    alert('حدث خطأ أثناء استرجاع البيانات');
                }
            });
        } else {
            // إذا كان الحقل فارغاً
            $('#employeeName').val('');
        }
    });
});
</script>


    <script>
        $(document).ready(function() {
            // تهيئة jQuery UI Datepicker لحقول التاريخ
            $(".datepicker").datepicker({
                dateFormat: 'yy/mm/dd', // تنسيق التاريخ المطلوب
                changeMonth: true, // السماح بتغيير الشهر
                changeYear: true, // السماح بتغيير السنة
                yearRange: 'c-10:c+10', // نطاق السنوات (10 سنوات للوراء و 10 للأمام)
                isRTL: true, // دعم اتجاه اليمين لليسار للغة العربية
                showButtonPanel: true, // إظهار زر "اليوم" و "إغلاق"
                // تخصيص الأيقونات لأسهم التنقل في Datepicker
                prevText: '<i class="fas fa-chevron-right"></i>', // أيقونة السهم السابق
                nextText: '<i class="fas fa-chevron-left"></i>',  // أيقونة السهم التالي
                // ترجمة الأيام والشهور للعربية (إذا لم تكن مترجمة تلقائيا)
                dayNamesMin: ["ح", "ن", "ث", "ر", "خ", "ج", "س"],
                monthNames: ["يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو", "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر"]
            });

            // معالجة إرسال النموذج (للتجربة فقط، يمكنك استبدالها بمنطق حفظ البيانات الفعلي)
            $('#leaveForm').on('submit', function(e) {
                e.preventDefault(); // منع الإرسال الافتراضي للنموذج

                const employeeId = $('#employeeId').val();
                const employeeName = $('#employeeName').val();
                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();
                const requestType = $('#requestType').val();

                if (!employeeId || !employeeName || !startDate || !endDate || !requestType) {
                    alert('الرجاء تعبئة جميع الحقول المطلوبة!'); // يمكنك استبدال هذا بنظام رسائل أجمل
                    return;
                }

                const formData = {
                    employeeId: employeeId,
                    employeeName: employeeName,
                    startDate: startDate,
                    endDate: endDate,
                    requestType: requestType
                };

                console.log('بيانات الإجازة جاهزة للحفظ:', formData);
                alert('تم استلام بيانات الإجازة بنجاح (راجع وحدة التحكم للمعاينة).'); // رسالة تأكيد للمستخدم
                
                // هنا يمكنك إضافة كود AJAX لإرسال البيانات إلى الخادم (مثلاً PHP)
                // $.ajax({
                //     url: 'your_save_script.php', // استبدل هذا بمسار ملف PHP الخاص بك
                //     method: 'POST',
                //     data: formData,
                //     success: function(response) {
                //         console.log('الاستجابة من الخادم:', response);
                //         alert('تم حفظ البيانات بنجاح!');
                //         $('#leaveForm')[0].reset(); // إعادة تعيين النموذج
                //     },
                //     error: function(xhr, status, error) {
                //         console.error('حدث خطأ أثناء الحفظ:', error);
                //         alert('حدث خطأ أثناء حفظ البيانات. الرجاء المحاولة مرة أخرى.');
                //     }
                // });
            });
        });
    </script>

</body>
</html>