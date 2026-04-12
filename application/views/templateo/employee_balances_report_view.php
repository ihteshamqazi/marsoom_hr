<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير أرصدة الإجازات</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;}
        body{font-family:'Tajawal',sans-serif;overflow-y:auto !important;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .main-container{padding:30px 15px;position:relative;z-index:1}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.6rem;color:var(--text-light);margin-bottom:24px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .page-title::after{content:'';position:absolute;width:160px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
        .table-card{background:rgba(255,255,255,.95);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.3);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .top-actions a:hover{background:rgba(255,255,255,.2);color:var(--marsom-orange)}
        .dataTables-example thead th{background-color:#001f3f !important;color:#fff;text-align:center;vertical-align:middle;border-bottom:2px solid #00152b}
        .dataTables-example tbody td{text-align:center;vertical-align:middle;font-size:14px;white-space:nowrap}
        .dt-buttons .btn { margin: 0 4px; }
        .dt-search { float: left; margin-left: 10px; }
    </style>
</head>
<body>

<div class="top-actions">
    <a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
</div>

<div class="main-container container-fluid">
    <div class="text-center">
        <h1 class="page-title">تقرير أرصدة الإجازات</h1>
    </div>

    <div class="card table-card">
        <div class="card-body">
             
            <hr>
            <div class="table-responsive">
                <table id="balances-table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>الرقم الوظيفي</th>
                            <th>اسم الموظف</th>
                            <th>نوع الإجازة</th>
                            <th>المخصص</th>
                            <th>المستهلك</th>
                            <th>المتبقي</th>
                            <th>السنة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($balances as $balance): ?>
                        <tr>
                            <td><?php echo html_escape($balance['employee_id']); ?></td>
                            <td><?php echo html_escape($balance['subscriber_name']); ?></td>
                            <td><?php echo html_escape($balance['leave_type_name']); ?></td>
                            <td><?php echo number_format((float)$balance['balance_allotted'], 2); ?></td>
                            <td><?php echo number_format((float)$balance['balance_consumed'], 2); ?></td>
                            <td><?php echo number_format((float)$balance['remaining_balance'], 2); ?></td>
                            <td><?php echo html_escape($balance['year']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#balances-table').DataTable({
        "responsive": true,
        "pageLength": 25,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json"
        },
        "layout": {
            "topStart": {
                "buttons": [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel me-1"></i> تصدير Excel للعرض الحالي',
                        className: 'btn btn-sm btn-success',
                        title: 'تقرير أرصدة الإجازات',
                        exportOptions: {
                            columns: ':visible' // Export only visible columns
                        }
                    },
                   
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print me-1"></i> طباعة',
                        className: 'btn btn-sm btn-info',
                        title: 'تقرير أرصدة الإجازات'
                    }
                ]
            }
        }
    });
});
</script>

</body>
</html>