<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إحصائيات الإجازات</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root { --marsom-blue: #001f3f; --marsom-orange: #FF8C00; }
        body { font-family: 'Tajawal', sans-serif; background-color: #f4f6f9; min-height: 100vh; }

        .page-header {
            background: linear-gradient(135deg, var(--marsom-blue), #1e3c72);
            color: white; padding: 2rem 0 4rem; margin-bottom: -3rem;
            border-radius: 0 0 2rem 2rem; box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .page-title { font-family: 'El Messiri', sans-serif; font-weight: 700; }
        .top-btn { background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; padding: 8px 15px; text-decoration: none; transition: 0.3s; }
        .top-btn:hover { background: white; color: var(--marsom-blue); }

        .stat-card {
            background: white; border-radius: 16px; border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); transition: transform 0.2s;
            overflow: hidden; position: relative;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        
        .card-stripe { height: 6px; width: 100%; position: absolute; top: 0; }
        .bg-success-soft { background-color: #d1e7dd; color: #0f5132; }
        .bg-danger-soft { background-color: #f8d7da; color: #842029; }
        
        .progress { height: 12px; border-radius: 6px; background-color: #e9ecef; margin-top: 15px; }
        .stat-val { font-size: 1.8rem; font-weight: 800; line-height: 1; }
        .stat-label { font-size: 0.8rem; color: #6c757d; }
    </style>
</head>
<body>

<div class="page-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">📊 طاقة استيعاب الإجازات</h1>
                <p class="mb-0 opacity-75">متابعة نسب الإشغال للفرق التابعة لك</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= site_url('users1/main_hr1') ?>" class="top-btn"><i class="fas fa-home"></i> الرئيسية</a>
            </div>
        </div>
    </div>
</div>

<div class="container" style="margin-top: 2rem;">
    
    <?php if(empty($dept_stats)): ?>
        <div class="alert alert-warning text-center shadow-sm rounded-4 p-4">
            <i class="fas fa-users-slash fa-2x mb-3"></i>
            <h5>لا يوجد موظفين تابعين لك حالياً.</h5>
        </div>
    <?php else: ?>
        <div class="alert alert-info shadow-sm rounded-3 border-0 mb-4">
            <i class="fas fa-info-circle me-2"></i> السياسة الحالية تسمح بخروج <strong>25%</strong> كحد أقصى من موظفي كل قسم في نفس الوقت.
        </div>

        <div class="row g-4">
            <?php foreach ($dept_stats as $stat): 
                $bar_color = ($stat['status_color'] == 'danger') ? 'bg-danger' : (($stat['status_color'] == 'warning') ? 'bg-warning' : 'bg-success');
                $border_class = 'border-' . $stat['status_color']; // Bootstrap border utility
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="stat-card">
                    <div class="card-stripe <?= $bar_color ?>"></div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="fw-bold mb-0 text-dark"><?= $stat['department'] ?></h5>
                            <?php if($stat['remaining_slots'] == 0): ?>
                                <span class="badge bg-danger rounded-pill">مكتمل</span>
                            <?php else: ?>
                                <span class="badge bg-success rounded-pill">متاح (<?= $stat['remaining_slots'] ?>)</span>
                            <?php endif; ?>
                        </div>

                        <div class="row text-center mb-3">
                            <div class="col-4 border-end">
                                <div class="stat-val text-<?= $stat['status_color'] ?>"><?= $stat['on_leave'] ?></div>
                                <div class="stat-label">في إجازة</div>
                            </div>
                            <div class="col-4 border-end">
                                <div class="stat-val text-dark"><?= $stat['total_employees'] ?></div>
                                <div class="stat-label">الإجمالي</div>
                            </div>
                            <div class="col-4">
                                <div class="stat-val text-secondary"><?= $stat['max_allowed'] ?></div>
                                <div class="stat-label">الحد الأقصى</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between small fw-bold text-muted mb-1">
                            <span>نسبة الإشغال</span>
                            <span><?= $stat['usage_percent'] ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar <?= $bar_color ?>" role="progressbar" 
                                 style="width: <?= min(100, $stat['usage_percent']) ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>