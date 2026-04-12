<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($title ?? 'تقرير الموظفين المفوضين') ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f4f6f9; color: #333; }
        .main-container { max-width: 1000px; margin: 40px auto; padding: 0 15px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #e9ecef; padding-bottom: 15px; }
        
        .card-custom { border: none; border-radius: 12px; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: transform 0.2s; height: 100%; }
        .card-custom:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        
        .icon-circle { width: 65px; height: 65px; background-color: #e8f4fd; color: #0d6efd; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 15px; }
        
        @media (max-width: 768px) {
            .page-header { flex-direction: column; align-items: flex-start; gap: 15px; }
        }
    </style>
</head>
<body>

<div class="main-container">
    
    <div class="page-header">
        <div>
            <h3 class="fw-bold text-dark m-0"><i class="fas fa-users-cog text-primary me-2"></i> تقرير الموظفين المفوضين</h3>
            <p class="text-muted small m-0 mt-1">قائمة بالموظفين الذين تم تفويض مهام أو طلبات إليهم من قبل زملائهم</p>
        </div>
        <div>
            <a href="<?= site_url('users1') ?>" class="btn btn-secondary rounded-pill px-4 shadow-sm">
                <i class="fas fa-home me-2"></i> الرئيسية
            </a>
        </div>
    </div>
    
    <div class="row">
        <?php if(!empty($delegates)): foreach($delegates as $del): ?>
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card-custom p-4">
                <div class="d-flex flex-column align-items-center text-center">
                    
                    <div class="icon-circle shadow-sm">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    
                    <h5 class="fw-bold text-dark mb-1">
                        <?= !empty($del['delegate_name']) ? html_escape($del['delegate_name']) : 'اسم الموظف غير متوفر' ?>
                    </h5>
                    
                    <p class="text-muted mb-3 small">
                        <i class="fas fa-id-card me-1"></i> <?= html_escape($del['delegation_employee_id']) ?> <br>
                        <?= html_escape($del['delegate_profession']) ?>
                    </p>
                    
                    <div class="w-100 bg-light border rounded-3 p-3 mb-4 text-start">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary"><i class="fas fa-phone me-1 text-success"></i> الجوال:</span>
                            <span class="fw-bold text-dark" dir="ltr"><?= html_escape($del['delegate_phone']) ?: '---' ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary"><i class="fas fa-tasks me-1 text-info"></i> عدد الطلبات:</span>
                            <span class="badge bg-primary rounded-pill px-2"><?= $del['delegation_count'] ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary"><i class="far fa-calendar-alt me-1 text-warning"></i> آخر تفويض:</span>
                            <span class="text-dark small fw-bold mt-1"><?= $del['last_delegation_date'] ?></span>
                        </div>
                    </div>
                    
                    <a href="<?= site_url('users1/delegation_details/'.$del['delegation_employee_id']) ?>" class="btn btn-outline-primary w-100 rounded-pill fw-bold">
                        <i class="fas fa-list-ul me-2"></i> عرض تفاصيل الطلبات
                    </a>
                    
                </div>
            </div>
        </div>
        <?php endforeach; else: ?>
        <div class="col-12">
            <div class="text-center py-5 bg-white border rounded-4 shadow-sm mt-3">
                <div class="mb-3"><i class="far fa-folder-open fa-3x text-muted opacity-50"></i></div>
                <h5 class="text-muted fw-bold">لا يوجد موظفين مفوضين حالياً</h5>
                <p class="text-muted small">لم يتم العثور على أي تفويضات مسجلة في النظام.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>