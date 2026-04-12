<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>القضايا العمالية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        body { font-family: 'Tajawal'; background: #f5f7fa; }
        .header-bar { background: linear-gradient(135deg, #2c3e50, #4a6491); color: white; padding: 25px 0; margin-bottom: 30px; }
        .card { border-radius: 10px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        .table th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; }
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; }
        .btn-action { padding: 4px 12px; font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="header-bar">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3><i class="fas fa-balance-scale me-2"></i> إدارة القضايا العمالية</h3>
                <p class="mb-0 text-light">عرض ومتابعة جميع القضايا والتظلمات</p>
            </div>
            <a href="<?= site_url('users1/main_hr1') ?>" class="btn btn-outline-light">
                <i class="fas fa-arrow-right me-1"></i> العودة
            </a>
        </div>
    </div>
</div>

<div class="container">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i> سجل القضايا</h5>
            <a href="<?= site_url('users1/create_labor_case') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle me-1"></i> تقديم قضية جديدة
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="casesTable" class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th width="120">تاريخ الإنشاء</th>
                            <th>الموظف</th>
                            <th>نوع القضية</th>
                            <th width="150">الطرف الآخر</th>
                            <th width="120">الحالة</th>
                            <th width="100">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cases as $case): 
                            $status_class = '';
                            switch($case['status']) {
                                case 'Pending': $status_class = 'bg-warning text-dark'; break;
                                case 'In Review': $status_class = 'bg-info text-white'; break;
                                case 'Approved': $status_class = 'bg-success text-white'; break;
                                case 'Rejected': $status_class = 'bg-danger text-white'; break;
                                case 'Closed': $status_class = 'bg-secondary text-white'; break;
                                default: $status_class = 'bg-light text-dark';
                            }
                        ?>
                        <tr>
                            <td><strong>#<?= $case['id'] ?></strong></td>
                            <td><?= date('Y-m-d', strtotime($case['created_at'])) ?></td>
                            <td><?= htmlspecialchars($case['subscriber_name']) ?></td>
                            <td><?= htmlspecialchars($case['case_type']) ?></td>
                            <td><?= htmlspecialchars($case['against_whom'] ?? 'الشركة') ?></td>
                            <td>
                                <span class="status-badge d-inline-block <?= $status_class ?>">
                                    <?= $case['status'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= site_url('users1/labor_case_details/' . $case['id']) ?>" 
                                       class="btn btn-outline-primary btn-action" title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if($case['emp_id'] == $this->session->userdata('user_id') && $case['status'] == 'Pending'): ?>
                                    <button class="btn btn-outline-warning btn-action" title="تعديل" 
                                            onclick="editCase(<?= $case['id'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-action" title="حذف"
                                            onclick="deleteCase(<?= $case['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-center bg-light">
                <div class="card-body">
                    <h1 class="text-primary"><?= count(array_filter($cases, fn($c) => $c['status'] == 'Pending')) ?></h1>
                    <p class="mb-0">قيد الانتظار</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-light">
                <div class="card-body">
                    <h1 class="text-info"><?= count(array_filter($cases, fn($c) => $c['status'] == 'In Review')) ?></h1>
                    <p class="mb-0">قيد المراجعة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-light">
                <div class="card-body">
                    <h1 class="text-success"><?= count(array_filter($cases, fn($c) => $c['status'] == 'Approved')) ?></h1>
                    <p class="mb-0">معتمدة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-light">
                <div class="card-body">
                    <h1 class="text-danger"><?= count(array_filter($cases, fn($c) => $c['status'] == 'Rejected')) ?></h1>
                    <p class="mb-0">مرفوضة</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف هذه القضية؟ هذا الإجراء لا يمكن التراجع عنه.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">حذف</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#casesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'
        },
        order: [[0, 'desc']],
        pageLength: 25
    });
    
    let caseIdToDelete = null;
    
    window.deleteCase = function(id) {
        caseIdToDelete = id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    };
    
    $('#confirmDelete').click(function() {
        if(caseIdToDelete) {
            $.post('<?= site_url("users1/delete_labor_case") ?>', {
                id: caseIdToDelete,
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            }, function(response) {
                if(response.success) {
                    Swal.fire('تم!', 'تم حذف القضية بنجاح', 'success').then(() => location.reload());
                } else {
                    Swal.fire('خطأ!', response.message || 'حدث خطأ أثناء الحذف', 'error');
                }
            }, 'json');
        }
        $('#deleteModal').modal('hide');
    });
    
    window.editCase = function(id) {
        window.location.href = '<?= site_url("users1/edit_labor_case/") ?>' + id;
    };
});
</script>
</body>
</html>