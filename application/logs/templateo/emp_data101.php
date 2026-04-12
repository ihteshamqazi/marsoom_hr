<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>بيانات الموظفين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;}
        body{font-family:'Tajawal',sans-serif;overflow-y:auto !important;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.8rem;color:var(--text-light);margin-bottom:32px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .page-title::after{content:'';position:absolute;width:100px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
        .table-card{background:rgba(255,255,255,.9);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.3);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .dataTables-example thead th{background-color:#001f3f !important;color:#fff;text-align:center;vertical-align:middle;border-bottom:2px solid #00152b}
        .dataTables-example tbody td{text-align:center;vertical-align:middle;font-size:14px;white-space:nowrap}
    </style>
</head>
<body>

<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-center flex-grow-1">
            <h1 class="page-title">بيانات الموظفين</h1>
        </div>
        <div>
             <a href="<?php echo site_url('users1/main_hr1'); ?>" class="btn btn-light"><i class="fas fa-arrow-right"></i> رجوع للرئيسية</a>
        </div>
    </div>

    <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
    <?php endif; ?>
    <?php if($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
    <?php endif; ?>

    <div class="card table-card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3"><label for="filter_employee_id" class="form-label fw-bold">الرقم الوظيفي</label><input type="text" id="filter_employee_id" class="form-control" placeholder="أدخل الرقم الوظيفي..."></div>
                <div class="col-md-3"><label for="filter_id_number" class="form-label fw-bold">رقم الهوية</label><input type="text" id="filter_id_number" class="form-control" placeholder="أدخل رقم الهوية..."></div>
                <div class="col-md-3"><label for="filter_name" class="form-label fw-bold">اسم الموظف</label><input type="text" id="filter_name" class="form-control" placeholder="أدخل جزء من الاسم..."></div>
                <div class="col-md-3 d-flex gap-2"><button id="btn-search" class="btn btn-primary w-100"><i class="fa fa-search"></i> بحث</button><button id="btn-clear" class="btn btn-outline-secondary w-100"><i class="fa fa-times"></i> مسح</button></div>
            </div>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-header d-flex justify-content-end gap-2 bg-transparent border-0">
             <a href="<?= site_url('users1/add_employee') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> إضافة موظف جديد</a>
             <a href="<?= site_url('users1/upload_employees_page') ?>" class="btn btn-success"><i class="fa fa-file-excel"></i> رفع من ملف Excel</a>
        </div>
        <div class="card-body">
            <input type="hidden" id="csrf-token" name="<?= $csrf_token_name ?>" value="<?= $csrf_hash ?>">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
                    <thead>
                        <tr>
                            <th>الرقم الوظيفي</th>
                            <th>رقم الهوية</th>
                            <th>اسم الموظف</th>
                            <th>الجنسية</th>
                            <th>الجنس</th>
                            <th>إجمالي الراتب</th>
                            <th>المسمى الوظيفي</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>

<script>
    function openExemptionPopup(empId) {
        const url = "<?php echo site_url('users1/view_emp'); ?>/" + encodeURIComponent(empId);
        window.open(url, '_blank');
    }
    
    // ✅ REPLACED: This function now handles CSRF tokens correctly
    function deleteEmployee(id) {
        if (confirm('هل أنت متأكد من حذف هذا الموظف؟ لا يمكن التراجع عن هذا الإجراء.')) {
            
            // Get the current CSRF token from the hidden input field
            const csrfTokenName = $('#csrf-token').attr('name');
            const csrfTokenHash = $('#csrf-token').val();
            
            var postData = { id: id };
            postData[csrfTokenName] = csrfTokenHash;

            $.ajax({
                url: "<?php echo site_url('users1/delete_employee'); ?>",
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Update the hidden input with the new token returned from the server
                        $('#csrf-token').val(response.csrf_hash);
                        alert(response.message);
                        $('.dataTables-example').DataTable().draw(false);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error, xhr.responseText);
                    alert('حدث خطأ في الاتصال بالخادم. يرجى تحديث الصفحة والمحاولة مرة أخرى.');
                }
            });
        }
    }

    $(document).ready(function () {
        var dt = $('.dataTables-example').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "pageLength": 25,
            "searching": false,
            "ajax": {
                "url": "<?php echo site_url('users1/fetch_employees'); ?>",
                "type": "POST",
                "data": function (d) {
                    d.filter_employee_id = $('#filter_employee_id').val();
                    d.filter_id_number = $('#filter_id_number').val();
                    d.filter_name = $('#filter_name').val();
                    d['<?php echo $csrf_token_name; ?>'] = $('#csrf-token').val(); // Send CSRF token with search
                },
                "dataSrc": function(json) {
                    // Update CSRF token after every server response
                    $('#csrf-token').val(json.csrf_hash);
                    return json.data;
                }
            },
            "columns": [
                { "data": "employee_id" },
                { "data": "id_number" },
                { "data": "subscriber_name" },
                { "data": "nationality" },
                { "data": "gender" },
                { "data": "total_salary" },
                { "data": "profession" },
                { "data": "actions", "orderable": false }
            ],
            "columnDefs": [
                {
                    "targets": 0,
                    "render": function (data, type, row, meta) {
                        return `<a href="#" onclick="openExemptionPopup('${data}')" class="fw-bold">${data}</a>`;
                    }
                },
                {
                    "targets": 7,
                    "render": function (data, type, row, meta) {
                        var editUrl = "<?php echo site_url('users1/edit_employee/'); ?>" + row.id;
                        return `
                            <div class="btn-group">
                                <a href="${editUrl}" class="btn btn-sm btn-info" title="تعديل"><i class="fa fa-pen"></i></a>
                                <button class="btn btn-sm btn-danger" onclick="deleteEmployee(${row.id})" title="حذف"><i class="fa fa-trash"></i></button>
                            </div>
                        `;
                    }
                }
            ],
            "language": { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json' }
        });

        $('#btn-search').on('click', function() { dt.draw(); });
        $('#btn-clear').on('click', function() {
            $('#filter_employee_id, #filter_id_number, #filter_name').val('');
            dt.draw();
        });
    });
</script>
</body>
</html>