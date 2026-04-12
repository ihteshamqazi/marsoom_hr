<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>قائمة الموظفين الجدد</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;}
        body{font-family:'Tajawal',sans-serif;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:#343a40;}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .main-container{padding:30px 15px;z-index:1}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.5rem;color:#fff;margin-bottom:2rem;text-align:center;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .table-card{background:rgba(255,255,255,.95);backdrop-filter:blur(8px);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .dataTables-example thead th{background-color:#001f3f !important;color:#fff;}
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;}
        .filter-section{background:rgba(0,31,63,0.05);padding:20px;border-radius:10px;margin-bottom:20px;}
    </style>
</head>
<body>

<div class="top-actions">
    <a href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
    <a href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="main-container container-fluid">
    <div class="text-center"><h1 class="page-title">قائمة الموظفين الجدد</h1></div>
    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-body">
                    
                    <!-- DEBUG: Show what dates we received -->
                    <div class="alert alert-warning">
                        <strong>Debug Info:</strong><br>
                        Start Date Received: <?php echo !empty($_GET['start_date']) ? $_GET['start_date'] : 'NONE'; ?><br>
                        End Date Received: <?php echo !empty($_GET['end_date']) ? $_GET['end_date'] : 'NONE'; ?><br>
                        Total Employees: <?php echo count($new_employees); ?>
                    </div>

                    <!-- Simple Filter Form -->
                    <div class="filter-section">
                        <form method="GET" action="<?php echo current_url(); ?>">
                            <div class="row align-items-end g-3">
                                <div class="col-md-4">
                                    <label for="start_date" class="form-label fw-bold">من تاريخ الانضمام</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="<?php echo $this->input->get('start_date'); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date" class="form-label fw-bold">إلى تاريخ الانضمام</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                           value="<?php echo $this->input->get('end_date'); ?>">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter me-2"></i>تصفية
                                    </button>
                                    <a href="<?php echo current_url(); ?>" class="btn btn-secondary w-100 mt-2">
                                        <i class="fas fa-redo me-2"></i>إعادة تعيين
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <hr>

                    <!-- DataTable -->
                    <table id="newEmployeesTable" class="table table-striped table-bordered dataTables-example" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>الرقم الوظيفي</th>
                                <th>اسم الموظف</th>
                                <th>الجنسية</th>
                                <th>تاريخ الانضمام</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($new_employees as $employee): ?>
                            <tr>
                                <td><?php echo html_escape($employee['id']); ?></td>
                                <td><?php echo html_escape($employee['employee_id']); ?></td>
                                <td><?php echo html_escape($employee['subscriber_name']); ?></td>
                                <td><?php echo html_escape($employee['nationality']); ?></td>
                                <td><?php echo html_escape($employee['join_date']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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
    $('#newEmployeesTable').DataTable({
        responsive: true,
        pageLength: 25,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json'
        },
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fa fa-file-excel me-1"></i> تصدير إكسل',
                className: 'btn btn-success'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print me-1"></i> طباعة',
                className: 'btn btn-info'
            }
        ]
    });
});
</script>

</body>
</html>