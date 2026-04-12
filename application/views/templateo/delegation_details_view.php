<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($title ?? 'تفاصيل التفويضات') ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f4f6f9; color: #333; }
        .main-container { max-width: 1000px; margin: 40px auto; padding: 0 15px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #e9ecef; padding-bottom: 15px; }
        
        /* Cards Styling */
        .card-custom { border: none; border-radius: 12px; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; transition: transform 0.2s; }
        .card-custom:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
        .card-header-custom { background-color: #fff; border-bottom: 1px solid #f0f0f0; padding: 15px 20px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center; }
        .card-body-custom { padding: 20px; }
        
        /* Info Boxes */
        .info-box { background-color: #f8f9fa; border-right: 4px solid #001f3f; padding: 15px; border-radius: 6px; margin-bottom: 15px; }
        .delegate-summary { background-color: #e8f4fd; border-right: 4px solid #0d6efd; padding: 20px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        
        /* Badges */
        .badge-approved { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .badge-rejected { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        .badge-pending { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .page-header { flex-direction: column; align-items: flex-start; gap: 15px; }
            .card-header-custom { flex-direction: column; align-items: flex-start; gap: 10px; }
        }
    </style>
</head>
<body>

<div class="main-container">
    
    <div class="page-header">
        <div>
            <h3 class="fw-bold text-dark m-0"><i class="fas fa-id-badge text-primary me-2"></i> سجل المهام المفوضة</h3>
            <p class="text-muted small m-0 mt-1">استعراض تفاصيل الطلبات التي تم تفويضها للموظف</p>
        </div>
        <div>
            <a href="<?= site_url('users1/delegation_report') ?>" class="btn btn-secondary rounded-pill px-4 shadow-sm">
                <i class="fas fa-arrow-right me-2"></i> رجوع للقائمة
            </a>
        </div>
    </div>

    <div class="delegate-summary">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="fw-bold text-dark mb-2">
                    <i class="fas fa-user-tie me-2 text-primary"></i> <?= html_escape($delegate_info['subscriber_name']) ?>
                </h5>
                <div class="d-flex flex-wrap gap-3 mt-3">
                    <span class="text-muted"><i class="fas fa-id-card me-1"></i> <strong>الرقم الوظيفي:</strong> <?= html_escape($delegate_info['employee_id']) ?></span>
                    <span class="text-muted"><i class="fas fa-briefcase me-1"></i> <strong>المسمى:</strong> <?= html_escape($delegate_info['profession']) ?></span>
                    <span class="text-muted"><i class="fas fa-sitemap me-1"></i> <strong>الإدارة:</strong> <?= html_escape($delegate_info['n1']) ?></span>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge bg-white text-primary border border-primary p-2 fs-6 rounded-pill shadow-sm">
                    <i class="fas fa-tasks me-1"></i> إجمالي الطلبات: <?= count($delegation_records) ?>
                </span>
            </div>
        </div>
    </div>

    <h5 class="fw-bold mb-4 text-secondary"><i class="fas fa-list-alt me-2"></i> قائمة الطلبات:</h5>

    <?php if(empty($delegation_records)): ?>
        <div class="text-center py-5">
            <div class="mb-3"><i class="far fa-folder-open fa-3x text-muted opacity-50"></i></div>
            <h5 class="text-muted">لا توجد طلبات مفوضة لهذا الموظف حالياً</h5>
        </div>
    <?php else: ?>
        
        <?php foreach($delegation_records as $row): ?>
            <div class="card-custom">
                <div class="card-header-custom">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <span class="fw-bold text-dark fs-5">#<?= $row['order_id'] ?></span>
                        <span class="d-none d-md-inline text-muted">|</span>
                        <span class="text-muted"><i class="far fa-calendar-alt ms-1"></i> <?= $row['request_date'] ?></span>
                        <span class="d-none d-md-inline text-muted">|</span>
                        <span class="badge bg-light text-dark border">
                            نوع الطلب: <?= html_escape($row['type'] ?? 'إجازة/مهمة') ?>
                        </span>
                    </div>

                    <div class="mt-2 mt-md-0">
                        <?php if($row['status'] == 2): ?>
                            <span class="badge badge-approved rounded-pill px-3 py-2">
                                <i class="fas fa-check-circle me-1"></i> معتمد
                            </span>
                        <?php elseif($row['status'] == 3): ?>
                            <span class="badge badge-rejected rounded-pill px-3 py-2">
                                <i class="fas fa-times-circle me-1"></i> مرفوض
                            </span>
                        <?php else: ?>
                            <span class="badge badge-pending rounded-pill px-3 py-2">
                                <i class="fas fa-clock me-1"></i> قيد المعالجة
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body-custom">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h6 class="fw-bold text-secondary mb-2">
                                <i class="fas fa-user me-1"></i> الموظف صاحب الطلب
                            </h6>
                            <div class="info-box">
                                <div class="fw-bold text-dark"><?= html_escape($row['requestor_name']) ?></div>
                                <div class="text-muted small mt-1">
                                    <i class="fas fa-briefcase me-1"></i> <?= html_escape($row['requestor_profession']) ?> 
                                    (<?= html_escape($row['requestor_id']) ?>)
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="fw-bold text-secondary mb-2">
                                <i class="fas fa-calendar-day me-1"></i> فترة التفويض
                            </h6>
                            <div class="info-box" style="border-right-color: #17a2b8;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-center">
                                        <small class="text-muted d-block">من تاريخ</small>
                                        <strong class="text-dark"><?= $row['vac_start'] ?></strong>
                                    </div>
                                    <i class="fas fa-long-arrow-alt-left text-info opacity-50 mx-2"></i>
                                    <div class="text-center">
                                        <small class="text-muted d-block">إلى تاريخ</small>
                                        <strong class="text-dark"><?= $row['vac_end'] ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 text-end">
                        <a href="<?= site_url('orders_emp/view/'.$row['order_id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="fas fa-external-link-alt me-1"></i> عرض أصل الطلب
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>