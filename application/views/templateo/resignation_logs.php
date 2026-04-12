<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سجل حضور الموظف المستقيل</title>
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
            font-size: 2.5rem;
            color: var(--text-light);
            margin-bottom: 25px;
            text-align: center;
            text-shadow: 0 3px 6px rgba(0,0,0,.4);
        }
        .table-card {
            background: rgba(255, 255, 255, .95);
            backdrop-filter: blur(8px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,.15);
            padding: 25px;
            margin-bottom: 20px;
        }
        /* Custom Table Styling to match reference */
        .table thead th {
            background-color: var(--marsom-blue) !important;
            color: #fff;
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
        }
        .table tbody td {
            text-align: center;
            vertical-align: middle;
            font-weight: 500;
        }
        .info-box {
            background: #f8f9fa;
            border-right: 5px solid var(--marsom-orange);
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .badge-in { background-color: #28a745; font-size: 0.9em; padding: 8px 12px; }
        .badge-out { background-color: #dc3545; font-size: 0.9em; padding: 8px 12px; }
    </style>
</head>
<body>

<div class="main-container container-fluid">
    <div class="text-center">
        <h1 class="page-title">تقرير بصمات الموظف المستقيل</h1>
    </div>

    <div class="card table-card">
        <div class="card-body">
            
            <div class="row mb-4">
                <div class="col-12">
                    <div class="info-box d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h5 class="m-0 fw-bold text-dark">
                                <i class="fas fa-calendar-alt text-primary me-2"></i> 
                                الفترة: 
                                <span class="mx-2 text-muted">من</span> <b dir="ltr"><?= $period_start ?></b>
                                <span class="mx-2 text-muted">إلى</span> <b dir="ltr"><?= $period_end ?></b>
                            </h5>
                        </div>
                        <div>
                             <?php if(!empty($logs)): ?>
                                <span class="badge bg-secondary">عدد السجلات: <?= count($logs) ?></span>
                             <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="attendance-table" class="table table-striped table-bordered table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>الوقت</th>
                            <th>الرقم الوظيفي</th>
                            <th>الاسم</th>
                            <th>الحالة</th>
                            <th>الموقع / الجهاز</th>
                            <th>رقم الجهاز (SN)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($logs)): ?>
                            <?php foreach ($logs as $log): ?>
                                <?php 
                                    $dt = new DateTime($log['punch_time']);
                                    $date_only = $dt->format('Y-m-d');
                                    $time_only = $dt->format('H:i:s');
                                    
                                    // Determine Status Badge
                                    $state_badge = '<span class="badge bg-secondary">Unknown</span>';
                                    $state_text = $log['punch_state'];
                                    
                                    if ($state_text == '0' || stripos($state_text, 'In') !== false || stripos($state_text, 'Check In') !== false) {
                                        $state_badge = '<span class="badge badge-in"><i class="fas fa-sign-in-alt me-1"></i> دخول</span>';
                                    } elseif ($state_text == '1' || stripos($state_text, 'Out') !== false || stripos($state_text, 'Check Out') !== false) {
                                        $state_badge = '<span class="badge badge-out"><i class="fas fa-sign-out-alt me-1"></i> خروج</span>';
                                    } else {
                                        $state_badge = '<span class="badge bg-info text-dark">'.$state_text.'</span>';
                                    }
                                ?>
                                <tr>
                                    <td dir="ltr"><?= $date_only ?></td>
                                    <td dir="ltr" class="fw-bold text-primary"><?= $time_only ?></td>
                                    <td><?= $log['emp_code'] ?></td>
                                    <td><?= $log['first_name'] . ' ' . $log['last_name'] ?></td>
                                    <td><?= $state_badge ?></td>
                                    <td><?= !empty($log['terminal_alias']) ? $log['terminal_alias'] : $log['area_alias'] ?></td>
                                    <td dir="ltr" class="text-muted small"><?= $log['terminal_sn'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#attendance-table').DataTable({
        "responsive": true,
        "pageLength": 25,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json"
        },
        "dom": "<'row'<'col-md-6'B><'col-md-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-md-5'i><'col-md-7'p>>",
        "buttons": [
            { 
                extend: 'excel', 
                text: '<i class="fas fa-file-excel me-1"></i> Excel', 
                className: 'btn btn-success btn-sm',
                title: 'سجل حضور الموظف المستقيل'
            },
            { 
                extend: 'print', 
                text: '<i class="fas fa-print me-1"></i> طباعة', 
                className: 'btn btn-info btn-sm text-white',
                title: 'سجل حضور الموظف المستقيل'
            }
        ],
        "order": [[0, 'asc'], [1, 'asc']] // Sort by Date then Time
    });
});
</script>

</body>
</html>