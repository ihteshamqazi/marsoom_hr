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
        :root { --marsom-blue: #001f3f; --marsom-orange: #FF8C00; --text-light: #fff; --text-dark: #343a40; }
        body { font-family: 'Tajawal', sans-serif; background: #f4f6f9; }
        .page-title { font-family: 'El Messiri', sans-serif; font-weight: 700; color: #001f3f; margin: 30px 0; }
        .table-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 25px; }
        .dataTables_wrapper .table thead th { background-color: #001f3f; color: #fff; text-align: center; }
        .dataTables_wrapper .table tbody td { text-align: center; vertical-align: middle; }
        .form-label { font-weight: bold; font-size: 0.9rem; color: #555; }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <h1 class="page-title text-center">تقرير طلبات الموظفين</h1>

    <div class="table-card">
        <div class="row g-3 align-items-end mb-4">
            <div class="col-md-2">
                <label class="form-label">الرقم الوظيفي</label>
                <input type="text" id="filter_employee_id" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">اسم الموظف</label>
                <input type="text" id="filter_name" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">نوع الطلب</label>
                <select id="filter_type" class="form-select">
                    <option value="">الكل</option>
                    <?php foreach($request_types as $code => $name): ?>
                        <option value="<?= $code ?>"><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">الحالة</label>
                <select id="filter_status" class="form-select">
                    <option value="">الكل</option>
                    <?php foreach($statuses as $code => $name): ?>
                        <option value="<?= $code ?>"><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button id="btn-search" class="btn btn-primary w-100"><i class="fa fa-search"></i> بحث</button>
                <button id="btn-clear" class="btn btn-secondary w-100"><i class="fa fa-times"></i> مسح</button>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">من تاريخ (البدء الفعلي)</label>
                <input type="date" id="filter_start_date" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">إلى تاريخ (البدء الفعلي)</label>
                <input type="date" id="filter_end_date" class="form-control">
            </div>
        </div>

        <div class="table-responsive">
            <table id="requests-table" class="table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>الرقم الوظيفي</th>
                        <th>اسم الموظف</th>
                        <th>المهنة</th>
                        <th>نوع الطلب</th>
                        
                        <th>التفاصيل / النوع</th>
                        <th>تاريخ البداية</th>
                        <th>تاريخ النهاية</th>
                        <th>المدة</th>
                        
                        <th>تاريخ الإنشاء</th>
                        <th>الحالة</th>
                        <th>عرض</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
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
                d.filter_employee_id = $('#filter_employee_id').val();
                d.filter_name = $('#filter_name').val();
                d.filter_type = $('#filter_type').val();
                d.filter_status = $('#filter_status').val();
                d.filter_start_date = $('#filter_start_date').val();
                d.filter_end_date = $('#filter_end_date').val();
            }
        },
        "columns": [
            { "data": "id" },                   // 0
            { "data": "emp_id" },               // 1
            { "data": "emp_name" },             // 2
            { "data": "profession" },           // 3
            { "data": "order_name" },           // 4
            { "data": "vac_main_type" },        // 5
            { "data": "effective_start_date" }, // 6
            { "data": "effective_end_date" },   // 7
            { "data": "details_info" },         // 8
            { "data": "date" },                 // 9
            { "data": "status" },               // 10
            { "data": "id", "orderable": false }// 11
        ],
        "columnDefs": [
            {
                "targets": 11,
                "render": function (data, type, row) {
                    var viewUrl = `<?php echo site_url('users1/view_request/'); ?>${data}`;
                    return `<a href="${viewUrl}" class="btn btn-sm btn-info text-white" target="_blank"><i class="fa fa-eye"></i></a>`;
                }
            }
        ],
        "language": { "url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json" },
        "layout": {
            topStart: {
                buttons: [
    {
        extend: 'excel',
        text: '<i class="fas fa-file-excel me-1"></i> تصدير Excel (الكل)',
        className: 'btn btn-success btn-sm',
        exportOptions: { 
            columns: ':not(:last-child)', // Exclude the "Action" column
            format: {
                body: function(data, row, column, node) {
                    // Strip HTML tags (like badges) from data for clean Excel
                    return $('<div>'+data+'</div>').text(); 
                }
            }
        },
        action: function (e, dt, button, config) {
            var self = this; // <--- CRITICAL FIX: Capture the button instance
            var originalLen = dt.page.len();

            // 1. Listen for the draw event (happens after data is loaded)
            dt.one('draw', function () {
                // 2. Trigger the actual Excel export using 'self'
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
                
                // 3. Reset the table back to original length (pagination)
                dt.page.len(originalLen).draw();
            });

            // 4. Set length to -1 (All) and trigger draw to fetch all data
            dt.page.len(-1).draw();
        }
    },
    { 
        extend: 'print', 
        text: '<i class="fas fa-print me-1"></i> طباعة', 
        className: 'btn btn-info btn-sm text-white',
        exportOptions: { columns: ':not(:last-child)' }
    }
]
            }
        }
    });

    $('#btn-search').on('click', function() { dt.draw(); });
    $('#btn-clear').on('click', function() {
        $('input, select').val('');
        dt.draw();
    });
});
</script>

</body>
</html>