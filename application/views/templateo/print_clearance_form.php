<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>نموذج إخلاء طرف - <?php echo $info['emp_name']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        @page { size: A4; margin: 10mm; }
        body { font-family: 'Tajawal', sans-serif; margin: 0; padding: 0; background: #fff; color: #000; -webkit-print-color-adjust: exact; }
        .container { max-width: 210mm; margin: auto; padding: 10px; }
        
        /* Header */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 3px solid #E67E22; padding-bottom: 10px; }
        .logo { width: 100px; }
        .title h1 { margin: 0; color: #E67E22; font-size: 22px; font-weight: 800; }
        
        /* Tables */
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 11px; page-break-inside: avoid; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; vertical-align: middle; }
        th { background-color: #e9e9e9; font-weight: bold; }
        
        /* Widths */
        .w-done { width: 5%; }
        .w-action { width: 35%; text-align: right !important; padding-right: 5px !important; }
        .w-check { width: 5%; }
        .w-covenant { width: 35%; text-align: right !important; padding-right: 5px !important; }

        /* Signature */
        .sig-row { display: flex; justify-content: space-between; margin-top: -16px; margin-bottom: 15px; font-size: 11px; font-weight: bold; padding: 5px; border: 1px solid #000; border-top: none; background: #fafafa; }
        
        .check-box { display: inline-block; width: 10px; height: 10px; border: 1px solid #000; margin: auto; }
        .checked { background-color: #000; } 

        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>

<?php if(!isset($is_email_mode) || $is_email_mode === false): ?>
<div class="no-print" style="text-align: center; padding: 15px; background: #f4f4f4; margin-bottom: 20px; border-bottom: 1px solid #ddd;">
    
    <button onclick="window.print()" style="padding: 10px 20px; background: #001f3f; color: white; border: none; cursor: pointer; border-radius: 5px; font-weight: bold;">
        <i class="fas fa-print"></i> طباعة النموذج
    </button>

    <button onclick="sendEmail()" id="btnEmail" style="padding: 10px 20px; background: #28a745; color: white; border: none; cursor: pointer; border-radius: 5px; margin-right: 10px; font-weight: bold;">
        📧 إرسال للموظف (Email)
    </button>

</div>
<?php endif; ?>

<div class="container">
    <div class="header">
        <div class="title">
            <h1>نموذج إخلاء طرف</h1>
            <span style="font-size:12px;">Clearance Form</span>
        </div>
        <img src="<?php echo base_url('assets/logo1.jpg'); ?>" class="logo">
    </div>

    <table>
        <thead>
            <tr>
                <th>تاريخ أخر يوم عمل</th>
                <th>المسمى الوظيفي</th>
                <th>الإدارة / القسم</th>
                <th>اسم الموظف</th>
                <th>الرقم الوظيفي</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $info['date_of_the_last_working']; ?></td>
                <td><?php echo $info['job_title']; ?></td>
                <td><?php echo $info['department_name']; ?></td>
                <td><?php echo $info['emp_name']; ?></td>
                <td><?php echo $info['emp_code']; ?></td>
            </tr>
        </tbody>
    </table>

    <?php
    // --- HELPER LOGIC ---
    $db_tasks = [];
    $dept_signatures = [];

    if(isset($departments)) {
        foreach($departments as $dept_id => $data) {
            if(isset($data['approver'])) {
                $dept_signatures[$dept_id] = ['name' => $data['approver'], 'date' => $data['date']];
            }
            if(isset($data['tasks'])) {
                foreach($data['tasks'] as $t) {
                    $clean_name = trim($t['parameter_name']);
                    $db_tasks[$dept_id][$clean_name] = $t['status'];
                }
            }
        }
    }

    function show_check($dept_id, $param_name, $tasks_array) {
        $status = isset($tasks_array[$dept_id][trim($param_name)]) ? $tasks_array[$dept_id][trim($param_name)] : '';
        $is_done = ($status === 'approved');
        echo '<div class="check-box ' . ($is_done ? 'checked' : '') . '"></div>';
    }

    function render_sig($dept_id, $title, $sigs) {
        $name = isset($sigs[$dept_id]['name']) ? $sigs[$dept_id]['name'] : '..................';
        $date = isset($sigs[$dept_id]['date']) ? date('Y-m-d', strtotime($sigs[$dept_id]['date'])) : '..................';
        $sign = isset($sigs[$dept_id]['name']) ? 'تم الاعتماد إلكترونياً' : '..................';
        echo '<div class="sig-row"><div style="width:30%">'.$title.'</div><div>الاسم: '.$name.'</div><div>التاريخ: '.$date.'</div><div>التوقيع: '.$sign.'</div></div>';
    }
    ?>

    <table>
        <thead><tr><th class="w-done">تم</th><th class="w-action">الاجراء</th><th class="w-check">استلام</th><th class="w-check">لايوجد</th><th class="w-check">يوجد</th><th class="w-covenant">العهد / الالتزامات</th></tr></thead>
        <tbody>
            <tr><td><?php show_check(45, 'تسليم أصل الاستقالة / الخطاب', $db_tasks); ?></td><td class="w-action">تسليم أصل الاستقالة / الخطاب</td><td></td><td></td><td></td><td class="w-covenant">هل لدى الموظف أي التزامات او عهد لديكم؟</td></tr>
            <tr><td></td><td class="w-action">أخرى: ...........................................</td><td></td><td></td><td></td><td class="w-covenant">أخرى: ...........................................</td></tr>
        </tbody>
    </table>
    <?php render_sig(45, 'المسؤول المباشر / مدير الإدارة', $dept_signatures); ?>

    <table>
        <thead><tr><th class="w-done">تم</th><th class="w-action">الاجراء</th><th class="w-check">استلام</th><th class="w-check">لايوجد</th><th class="w-check">يوجد</th><th class="w-covenant">العهد / الالتزامات</th></tr></thead>
        <tbody>
            <tr><td><?php show_check(44, 'إسناد السندات لمحامي أخر', $db_tasks); ?></td><td class="w-action">إسناد السندات لمحامي أخر</td><td><?php show_check(44, 'سندات لأمر، أحكام قضائية، صكوك، عقود', $db_tasks); ?></td><td><div class="check-box"></div></td><td><div class="check-box"></div></td><td class="w-covenant">سندات لأمر، أحكام قضائية، صكوك، عقود</td></tr>
            <tr><td><?php show_check(44, 'إشعار البنك وإدارة الموارد البشرية بفسخ الوكالة', $db_tasks); ?></td><td class="w-action">إشعار البنك وإدارة الموارد البشرية بفسخ الوكالة</td><td><?php show_check(44, 'وكالات شرعية.', $db_tasks); ?></td><td><div class="check-box"></div></td><td><div class="check-box"></div></td><td class="w-covenant">وكالات شرعية</td></tr>
            <tr><td><?php show_check(44, 'إشعار البنك بإلغاء التفويض', $db_tasks); ?></td><td class="w-action">إشعار البنك بإلغاء التفويض</td><td><?php show_check(44, 'خطابات تفويض من عملاء الشركة/المكتب', $db_tasks); ?></td><td><div class="check-box"></div></td><td><div class="check-box"></div></td><td class="w-covenant">خطابات تفويض من عملاء الشركة/المكتب</td></tr>
            <tr><td></td><td class="w-action"></td><td><?php show_check(44, 'شريحة الجوال الخاصة بالمنظمة.', $db_tasks); ?></td><td><div class="check-box"></div></td><td><div class="check-box"></div></td><td class="w-covenant">شريحة الجوال الخاصة بالمنظمة</td></tr>
            <tr><td></td><td class="w-action"></td><td><?php show_check(44, 'استلام مفاتيح الخزن', $db_tasks); ?></td><td><div class="check-box"></div></td><td><div class="check-box"></div></td><td class="w-covenant">استلام مفاتيح الخزن</td></tr>
        </tbody>
    </table>
    <?php render_sig(44, 'الإدارة القانونية قسم السندات', $dept_signatures); ?>

    <table>
        <thead><tr><th class="w-done">تم</th><th class="w-action">الاجراء</th><th class="w-check">استلام</th><th class="w-check">لايوجد</th><th class="w-check">يوجد</th><th class="w-covenant">العهد / الالتزامات</th></tr></thead>
        <tbody>
            <tr><td><?php show_check(9, 'إيقاف (اليوزر / البريد الالكتروني) الخاص بالموظف', $db_tasks); ?></td><td class="w-action">إيقاف (اليوزر / البريد الالكتروني) الخاص بالموظف</td><td><?php show_check(9, 'اجهزة الحاسب الآلي', $db_tasks); ?></td><td><div class="check-box"></div></td><td><div class="check-box"></div></td><td class="w-covenant">اجهزة الحاسب الآلي</td></tr>
            <tr><td><?php show_check(9, 'إيقاف التحويلة', $db_tasks); ?></td><td class="w-action">إيقاف التحويلة</td><td><?php show_check(9, 'ملحقات الحاسب الآلي (سماعات، تحويلة،)', $db_tasks); ?></td><td><div class="check-box"></div></td><td><div class="check-box"></div></td><td class="w-covenant">ملحقات الحاسب الآلي</td></tr>
            <tr><td><?php show_check(9, 'إيقاف يوزرات البرامج كافة.', $db_tasks); ?></td><td class="w-action">إيقاف يوزرات البرامج كافة</td><td><?php show_check(9, 'برامج مايكروسوفت', $db_tasks); ?></td><td><div class="check-box"></div></td><td><div class="check-box"></div></td><td class="w-covenant">برامج مايكروسوفت</td></tr>
        </tbody>
    </table>
    <?php render_sig(9, 'إدارة تقنية المعلومات', $dept_signatures); ?>

    <table>
        <thead><tr><th class="w-done">تم</th><th class="w-action">الاجراء</th><th class="w-check">استلام</th><th class="w-check">لايوجد</th><th class="w-check">يوجد</th><th class="w-covenant">العهد / الالتزامات</th></tr></thead>
        <tbody>
            <tr><td><?php show_check(37, 'تأكيد إيقاف التحويلة', $db_tasks); ?></td><td class="w-action">تأكيد إيقاف التحويلة</td><td><?php show_check(37, 'هل لدى الموظف أي التزامات او عهد لديكم؟', $db_tasks); ?></td><td><div class="check-box"></div></td><td><div class="check-box"></div></td><td class="w-covenant">هل لدى الموظف أي التزامات او عهد لديكم؟</td></tr>
        </tbody>
    </table>
    <?php render_sig(37, 'إدارة الجودة', $dept_signatures); ?>

    <table>
        <thead><tr><th class="w-done">تم</th><th class="w-action">الاجراء</th><th class="w-check">استلام</th><th class="w-check">لايوجد</th><th class="w-check">يوجد</th><th class="w-covenant">العهد / الالتزامات</th></tr></thead>
        <tbody>
            <tr><td><?php show_check(12, 'حذف الموظف من مسير الرواتب الشهرية', $db_tasks); ?></td><td class="w-action">حذف الموظف من مسير الرواتب الشهرية</td><td><?php show_check(12, 'هل لدى الموظف أي التزامات مادية؟', $db_tasks); ?></td><td><div class="check-box"></div></td><td><div class="check-box"></div></td><td class="w-covenant">هل لدى الموظف أي التزامات مادية؟</td></tr>
        </tbody>
    </table>
    <?php render_sig(12, 'الإدارة المالية', $dept_signatures); ?>

    <table>
        <thead><tr><th class="w-done">تم</th><th class="w-action">الاجراء</th><th class="w-check">استلام</th><th class="w-check">لايوجد</th><th class="w-check">يوجد</th><th class="w-covenant">العهد / الالتزامات</th></tr></thead>
        <tbody>
            <tr><td><?php show_check(7, 'إلغاء التأمين الطبي', $db_tasks); ?></td><td class="w-action">إلغاء التأمين الطبي</td><td><?php show_check(7, 'بطاقة العمل (الراجحي/ مرسوم / المكتب)', $db_tasks); ?></td><td><div class="check-box"></div></td><td><div class="check-box"></div></td><td class="w-covenant">بطاقة العمل (الراجحي/ مرسوم / المكتب)</td></tr>
            <tr><td><?php show_check(7, 'حذف الموظف من التأمينات الاجتماعية', $db_tasks); ?></td><td class="w-action">حذف الموظف من التأمينات الاجتماعية</td><td><?php show_check(7, 'بطاقة التأمين الطبي (الموظف وافراد عائلته)', $db_tasks); ?></td><td><div class="check-box"></div></td><td><div class="check-box"></div></td><td class="w-covenant">بطاقة التأمين الطبي</td></tr>
            <tr><td><?php show_check(7, 'احتساب مكافأة نهاية الخدمة', $db_tasks); ?></td><td class="w-action">احتساب مكافأة نهاية الخدمة</td><td><?php show_check(7, 'أصول الشهادات التعليمية', $db_tasks); ?></td><td><div class="check-box"></div></td><td><div class="check-box"></div></td><td class="w-covenant">أصول الشهادات التعليمية</td></tr>
            <tr><td><?php show_check(7, 'إصدار شهادة الخبرة', $db_tasks); ?></td><td class="w-action">إصدار شهادة الخبرة</td><td><?php show_check(7, 'أصول شهادات الخبرة', $db_tasks); ?></td><td><div class="check-box"></div></td><td><div class="check-box"></div></td><td class="w-covenant">أصول شهادات الخبرة</td></tr>
            <tr><td><?php show_check(7, 'حذف الموظف من مسير الرواتب الشهرية', $db_tasks); ?></td><td class="w-action">حذف الموظف من مسير الرواتب الشهرية</td><td><?php show_check(7, 'بطاقة موقف السيارة', $db_tasks); ?></td><td><div class="check-box"></div></td><td><div class="check-box"></div></td><td class="w-covenant">بطاقة موقف السيارة</td></tr>
            <tr><td><?php show_check(7, 'انهاء نقل الخدمات او الخروج النهائي (للوافدين)', $db_tasks); ?></td><td class="w-action">انهاء نقل الخدمات او الخروج النهائي (للوافدين)</td><td></td><td></td><td></td><td class="w-covenant"></td></tr>
        </tbody>
    </table>
    <?php render_sig(7, 'إدارة الموارد البشرية', $dept_signatures); ?>

</div>

<?php if(!isset($is_email_mode) || $is_email_mode === false): ?>
<script>
function sendEmail() {
    let btn = document.getElementById('btnEmail');
    let originalText = btn.innerText;
    
    // Prevent double clicking
    btn.disabled = true;
    btn.innerText = 'جاري الإرسال...';

    // AJAX Call
    $.post('<?= site_url("users1/send_clearance_email_ajax") ?>', 
    {
        resignation_id: '<?= $info['id'] ?>',
        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
    }, 
    function(response) {
        if(response.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'تم الإرسال بنجاح',
                text: response.message,
                confirmButtonColor: '#001f3f'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: response.message
            });
        }
        // Reset button
        btn.disabled = false;
        btn.innerText = originalText;
    }, 'json')
    .fail(function() {
        Swal.fire('خطأ', 'فشل الاتصال بالخادم', 'error');
        btn.disabled = false;
        btn.innerText = originalText;
    });
}
</script>
<?php endif; ?>

</body>
</html>