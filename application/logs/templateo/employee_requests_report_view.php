<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير طلبات الموظفين</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;}
        body{font-family:'Tajawal',sans-serif;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .main-container{padding:30px 15px;}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.8rem;color:var(--text-light);margin-bottom:32px;text-align:center;text-shadow:0 3px 6px rgba(0,0,0,.4);}
        .table-card{background:rgba(255,255,255,.95);backdrop-filter:blur(8px);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px;margin-bottom:20px;}
        .dataTables-example thead th{background-color:#001f3f !important;color:#fff;text-align:center;vertical-align:middle;}
        .dataTables-example tbody td{text-align:center;vertical-align:middle;}
    </style>
</head>
<body>

<div class="main-container container-fluid">
    <div class="text-center"><h1 class="page-title">تقرير طلبات الموظفين</h1></div>

    <div class="card table-card">
        <div class="card-body">
            <div class="row g-3 align-items-end mb-4">
                <div class="col-md-2"><label for="filter_employee_id" class="form-label fw-bold">الرقم الوظيفي</label><input type="text" id="filter_employee_id" class="form-control"></div>
                <div class="col-md-3"><label for="filter_name" class="form-label fw-bold">اسم الموظف</label><input type="text" id="filter_name" class="form-control"></div>
                <div class="col-md-2">
                    <label for="filter_type" class="form-label fw-bold">نوع الطلب</label>
                    <select id="filter_type" class="form-select">
                        <option value="">الكل</option>
                        <?php foreach($request_types as $code => $name): ?>
                            <option value="<?= $code ?>"><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filter_status" class="form-label fw-bold">الحالة</label>
                    <select id="filter_status" class="form-select">
                        <option value="">الكل</option>
                        <?php foreach($statuses as $code => $name): ?>
                            <option value="<?= $code ?>"><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button id="btn-search" class="btn btn-primary w-100"><i class="fa fa-search me-1"></i> بحث</button>
                    <button id="btn-clear" class="btn btn-outline-secondary w-100"><i class="fa fa-times me-1"></i> مسح</button>
                </div>
                <div class="col-md-3"><label for="filter_start_date" class="form-label fw-bold">من تاريخ</label><input type="date" id="filter_start_date" class="form-control"></div>
                <div class="col-md-3"><label for="filter_end_date" class="form-label fw-bold">إلى تاريخ</label><input type="date" id="filter_end_date" class="form-control"></div>
            </div>

            <div class="table-responsive">
                <table id="requests-table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th><th>الرقم الوظيفي</th><th>اسم الموظف</th><th>نوع الطلب</th>
                            <th>تاريخ الطلب</th><th>الحالة</th><th>عرض التفاصيل</th>
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
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    var dt = $('#requests-table').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "pageLength": 25,
        "searching": false,
        "ajax": {
            "url": "<?php echo site_url('users1/fetch_all_requests'); ?>",
            "type": "POST",
            "data": function (d) {
                // ✅ Send all filter data to the server
                d.filter_employee_id = $('#filter_employee_id').val();
                d.filter_name = $('#filter_name').val();
                d.filter_type = $('#filter_type').val();
                d.filter_status = $('#filter_status').val();
                d.filter_start_date = $('#filter_start_date').val();
                d.filter_end_date = $('#filter_end_date').val();
            }
        },
        "columns": [
            { "data": "id" }, { "data": "emp_id" }, { "data": "emp_name" },
            { "data": "order_name" }, { "data": "date" }, { "data": "status" },
            { "data": "id", "orderable": false }
        ],
        "columnDefs": [{
            "targets": 6,
            "render": function (data, type, row, meta) {
                var viewUrl = `<?php echo site_url('users1/view_request/'); ?>${data}`;
                return `<a href="${viewUrl}" class="btn btn-sm btn-info" target="_blank">عرض</a>`;
            }
        }],
        "language": { "url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json" },
        layout: {
            topStart: {
                buttons: [
                    { extend: 'excel', text: '<i class="fas fa-file-excel me-1"></i> Excel', className: 'btn-success' },
                    { extend: 'print', text: '<i class="fas fa-print me-1"></i> Print / PDF', className: 'btn-info' }
                ]
            }
        }
    });

    $('#btn-search').on('click', function() { dt.draw(); });
    $('#btn-clear').on('click', function() {
        $('#filter_employee_id, #filter_name, #filter_start_date, #filter_end_date').val('');
        $('#filter_type, #filter_status').val('');
        dt.draw();
    });
});
</script>

</body>
</html>