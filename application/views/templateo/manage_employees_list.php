<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة وتعديل الموظفين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;}
        body{font-family:'Tajawal',sans-serif;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.5rem;color:#fff;text-align:center;margin-bottom:30px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .table-card{background:rgba(255,255,255,.95);backdrop-filter:blur(8px);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .dataTables_filter input { border-radius: 8px; border: 2px solid var(--marsom-blue); padding: 5px 15px; }
        .btn-edit-icon { background-color: var(--marsom-orange); color: white; border-radius: 6px; padding: 6px 12px; transition: 0.3s; border: none;}
        .btn-edit-icon:hover { background-color: #e67e22; color: white; transform: scale(1.05); }
        thead th { background-color: var(--marsom-blue) !important; color: white !important; text-align: center; }
        tbody td { text-align: center; vertical-align: middle; font-weight: 500;}
    </style>
</head>
<body>

<div class="container my-5">
    <h1 class="page-title">قائمة الموظفين للتعديل</h1>

    <div class="card table-card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="employeesTable" class="table table-striped table-hover table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>الرقم الوظيفي</th>
                            <th>اسم الموظف</th>
                            <th>رقم الهوية / الإقامة</th>
                            <th>المسمى الوظيفي</th>
                            <th>الحالة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($employees)): foreach($employees as $emp): ?>
                        <tr>
                            <td class="text-primary fw-bold"><?= html_escape($emp['employee_id']) ?></td>
                            <td><?= html_escape($emp['subscriber_name']) ?></td>
                            <td><?= html_escape($emp['id_number']) ?></td>
                            <td><?= html_escape($emp['profession']) ?></td>
                            <td>
                                <?php if($emp['status'] == 'active'): ?>
                                    <span class="badge bg-success">نشط</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= html_escape($emp['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= site_url('users1/modify_staff_record/'.$emp['id']) ?>" class="btn btn-edit-icon" title="تعديل بيانات الموظف">
                                    <i class="fas fa-edit"></i> تعديل
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
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

<script>
    $(document).ready(function() {
        $('#employeesTable').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json"
            },
            "pageLength": 25,
            "responsive": true,
            "order": [[ 1, "asc" ]] // ترتيب أبجدي حسب الاسم
        });
    });
</script>

</body>
</html>