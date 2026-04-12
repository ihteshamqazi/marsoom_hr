<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>التقرير الشامل للموارد البشرية</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">

    <style>
        :root {
            --marsom-blue: #001f3f;
            --marsom-orange: #FF8C00;
            --text-light: #fff;
            --text-dark: #343a40;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, var(--marsom-blue) 0%, #34495e 50%, var(--marsom-orange) 100%);
            background-size: 400% 400%;
            animation: grad 20s ease infinite;
            min-height: 100vh;
        }

        @keyframes grad {
            0% { background-position: 0% 50% }
            50% { background-position: 100% 50% }
            100% { background-position: 0% 50% }
        }

        .main-container { padding: 30px 15px; }

        .page-title {
            font-family: 'El Messiri', sans-serif;
            font-weight: 700;
            font-size: 2.8rem;
            color: var(--text-light);
            margin-bottom: 32px;
            text-align: center;
            text-shadow: 0 3px 6px rgba(0, 0, 0, .4);
        }

        /* Glassmorphism Card Style */
        .glass-card {
            background: rgba(255, 255, 255, .95);
            backdrop-filter: blur(8px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .15);
            padding: 25px;
            margin-bottom: 20px;
            border: none;
        }

        /* Stat Cards Specifics */
        .stat-card {
            transition: transform 0.3s;
            position: relative;
            overflow: hidden;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon {
            position: absolute;
            top: -10px;
            left: -10px;
            font-size: 5rem;
            opacity: 0.1;
            color: var(--marsom-blue);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--marsom-blue);
            font-family: 'El Messiri', sans-serif;
        }
        .stat-label {
            color: #666;
            font-size: 1.1rem;
            font-weight: 500;
        }

        /* Tabs Styling */
        .nav-pills .nav-link {
            background-color: rgba(255, 255, 255, 0.2);
            color: var(--text-light);
            margin: 0 5px;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 50px;
            padding: 10px 25px;
            transition: all 0.3s;
        }
        .nav-pills .nav-link.active {
            background-color: var(--marsom-orange);
            color: white;
            border-color: var(--marsom-orange);
            box-shadow: 0 5px 15px rgba(255, 140, 0, 0.4);
        }
        .nav-pills .nav-link:hover:not(.active) {
            background-color: rgba(255, 255, 255, 0.4);
        }

        /* Table Styling */
        .table-responsive { border-radius: 10px; overflow: hidden; }
        .dataTables-example thead th, table.dataTable thead th {
            background-color: var(--marsom-blue) !important;
            color: #fff;
            text-align: center;
            vertical-align: middle;
            font-weight: 500;
        }
        .dataTables-example tbody td, table.dataTable tbody td {
            text-align: center;
            vertical-align: middle;
            font-weight: 500;
        }
        
        /* Custom Button Colors for DataTables */
        .dt-button.btn-success { background-color: #28a745 !important; border-color: #28a745 !important; }
        .dt-button.btn-info { background-color: var(--marsom-blue) !important; border-color: var(--marsom-blue) !important; color: white !important; }

        label.form-label { color: var(--marsom-blue); }
    </style>
</head>
<body>

<div class="main-container container-fluid">
    <div class="text-center">
        <h1 class="page-title"><i class="fa fa-chart-pie me-3"></i>التقرير الشامل للموارد البشرية</h1>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card stat-card">
                <i class="fa fa-user-clock stat-icon"></i>
                <div class="stat-number"><?= $stats['probation'] ?? 0 ?></div>
                <div class="stat-label">تحت التجربة</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card stat-card">
                <i class="fa fa-plane-departure stat-icon" style="color: var(--marsom-orange)"></i>
                <div class="stat-number" style="color: var(--marsom-orange)"><?= $stats['on_vacation'] ?? 0 ?></div>
                <div class="stat-label">في إجازة حالياً</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card stat-card">
                <i class="fa fa-file-circle-exclamation stat-icon" style="color: #dc3545"></i>
                <div class="stat-number" style="color: #dc3545"><?= $stats['pending'] ?? 0 ?></div>
                <div class="stat-label">طلبات معلقة</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card stat-card">
                <i class="fa fa-users stat-icon"></i>
                <div class="stat-number"><?= $stats['active'] ?? 0 ?></div>
                <div class="stat-label">إجمالي الموظفين</div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mb-4">
        <ul class="nav nav-pills" id="reportTabs">
            <li class="nav-item"><a class="nav-link active" href="#" onclick="switchReport('probation', this)">تحت التجربة</a></li>
            <li class="nav-item"><a class="nav-link" href="#" onclick="switchReport('resigned', this)">المستقيلين</a></li>
            <li class="nav-item"><a class="nav-link" href="#" onclick="switchReport('on_vacation', this)">في إجازة</a></li>
            <li class="nav-item"><a class="nav-link" href="#" onclick="switchReport('sick_leave', this)">إجازة مرضية</a></li>
            <li class="nav-item"><a class="nav-link" href="#" onclick="switchReport('pending_requests', this)">طلبات معلقة</a></li>
            <li class="nav-item"><a class="nav-link" href="#" onclick="switchReport('balances', this)">أرصدة الإجازات</a></li>
        </ul>
    </div>

    <div class="glass-card">
        <div class="card-body">
            
            <form id="filterForm">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="report_type" id="report_type_input" value="probation">

                <div class="row g-3 align-items-end mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">القسم / الإدارة</label>
                        <select class="form-select" name="department" onchange="reloadTable()">
                            <option value="">الكل</option>
                            <?php if(!empty($departments)): foreach($departments as $d): ?>
                                <option value="<?= $d ?>"><?= $d ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">الشركة</label>
                        <select class="form-select" name="company" onchange="reloadTable()">
                            <option value="">الكل</option>
                            <?php if(!empty($companies)): foreach($companies as $c): ?>
                                <option value="<?= $c ?>"><?= $c ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">التاريخ (من - إلى)</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="start_date" onchange="reloadTable()">
                            <input type="date" class="form-control" name="end_date" onchange="reloadTable()">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary w-100" style="background-color: var(--marsom-blue); border: none;" onclick="reloadTable()">
                            <i class="fa fa-filter me-1"></i> تصفية
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table id="comprehensiveTable" class="table table-striped table-bordered w-100">
                    <thead>
                        <tr>
                            <th>الرقم الوظيفي</th>
                            <th>الاسم</th>
                            <th>الإدارة</th>
                            <th>الوظيفة</th>
                            <th id="dynamic_th_1">تاريخ المباشرة</th>
                            <th id="dynamic_th_2">الحالة</th>
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
var dataTable;

$(document).ready(function() {
    switchReport('probation', $('.nav-link.active')); // Load default
});

function switchReport(type, element) {
    // Update Tabs UI
    $('.nav-link').removeClass('active');
    $(element).addClass('active');

    // Update Logic
    $('#report_type_input').val(type);
    updateHeaders(type);
    reloadTable();
}

function updateHeaders(type) {
    var h1 = "تفاصيل 1";
    var h2 = "تفاصيل 2";

    if(type === 'probation') { h1 = "فترة الخدمة (أيام)"; h2 = "حالة العقد"; }
    else if(type === 'resigned') { h1 = "آخر يوم عمل"; h2 = "سبب الاستقالة"; }
    else if(type === 'on_vacation' || type === 'sick_leave') { h1 = "تاريخ العودة"; h2 = "مدة الإجازة"; }
    else if(type === 'pending_requests') { h1 = "نوع الطلب"; h2 = "تاريخ الطلب"; }
    else if(type === 'balances') { h1 = "نوع الرصيد"; h2 = "المتبقي"; }

    $('#dynamic_th_1').text(h1);
    $('#dynamic_th_2').text(h2);
}

function reloadTable() {
    if ($.fn.DataTable.isDataTable('#comprehensiveTable')) {
        $('#comprehensiveTable').DataTable().destroy();
    }

    // Use DataTables 2.0 layout syntax matching your snippet
    dataTable = $('#comprehensiveTable').DataTable({
        "processing": true,
        "serverSide": false, // Using client-side processing for report smoothness
        "ajax": {
            "url": "<?= base_url('users1/hr_report_ajax') ?>",
            "type": "POST",
            "data": function (d) {
                var form = $('#filterForm').serializeArray();
                $.each(form, function(i, field){ d[field.name] = field.value; });
            },
            "dataSrc": function(json) {
                if(json.csrf_hash) $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val(json.csrf_hash);
                return json.data;
            }
        },
        "columns": [
            { "data": "emp_id" },
            { "data": "name", "render": function(data) { return '<strong>'+data+'</strong>'; } },
            { "data": "dept" },
            { "data": "job" },
            { "data": "col1" },
            { "data": "col2" }
        ],
        "language": { "url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json" },
        "layout": {
            topStart: {
                buttons: [
                    { extend: 'excel', text: '<i class="fas fa-file-excel me-1"></i> Excel', className: 'btn btn-success btn-sm' },
                    { extend: 'print', text: '<i class="fas fa-print me-1"></i> طباعة', className: 'btn btn-info btn-sm' }
                ]
            }
        },
        "pageLength": 25
    });
}
</script>

</body>
</html>