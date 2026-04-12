<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تفاصيل الطلب</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    
    <style>
        body { font-family: 'Tajawal'; background: #f8f9fa; padding-bottom: 100px; }
        .main-container { max-width: 1000px; margin: 40px auto; }
        
        .card-box { background: white; border-radius: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); padding: 30px; margin-bottom: 25px; }
        .section-title { font-weight: 800; color: #001f3f; margin-bottom: 20px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }

        /* Employee Header */
        .emp-header { display: flex; align-items: center; gap: 20px; }
        .emp-img { width: 70px; height: 70px; background: #001f3f; color: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 2rem; }
        
        /* Family Grid */
        .fam-card {
            background: #fff; border: 1px solid #eef2f7; border-radius: 12px; padding: 15px;
            transition: 0.3s; position: relative; overflow: hidden;
        }
        .fam-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-color: #FF8C00; }
        .fam-card::before { content:''; position: absolute; top:0; left:0; width: 4px; height: 100%; background: #FF8C00; }
        .rel-badge { position: absolute; top: 15px; left: 15px; font-size: 0.75rem; background: #eef2f7; padding: 2px 8px; border-radius: 4px; }

        /* Timeline */
        .timeline-item { position: relative; padding-right: 20px; margin-bottom: 20px; border-right: 2px solid #ddd; }
        .timeline-item::after { content:''; position: absolute; right: -6px; top: 5px; width: 10px; height: 10px; background: white; border: 2px solid #aaa; border-radius: 50%; }
        .timeline-item.done { border-color: #198754; }
        .timeline-item.done::after { background: #198754; border-color: #198754; }

        .action-bar { position: fixed; bottom: 0; left: 0; width: 100%; background: white; padding: 15px; box-shadow: 0 -5px 20px rgba(0,0,0,0.1); text-align: center; }
    </style>
</head>
<body>

<div class="main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0 text-primary">تفاصيل الطلب #<?= $req['id'] ?></h3>
        <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-pill px-4">عودة</a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card-box">
                <div class="emp-header">
                    <div class="emp-img"><i class="fas fa-user-tie"></i></div>
                    <div>
                        <h4 class="fw-bold m-0"><?= $req['subscriber_name'] ?></h4>
                        <div class="text-muted mt-1">
                            <span class="me-3"><i class="fas fa-id-card"></i> <?= $req['emp_code'] ?></span>
                            <span><i class="fas fa-building"></i> <?= $req['department'] ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4 pt-3 border-top">
                    <div class="col-md-6">
                        <small class="text-muted d-block">نوع الطلب</small>
                        <?php if($req['request_type']=='family'): ?>
                            <h5 class="fw-bold text-warning"><i class="fas fa-users"></i> عائلي</h5>
                        <?php else: ?>
                            <h5 class="fw-bold text-info"><i class="fas fa-user"></i> فردي</h5>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">تاريخ التقديم</small>
                        <h5 class="fw-bold"><?= date('Y-m-d', strtotime($req['created_at'])) ?></h5>
                    </div>
                    <div class="col-12 mt-3">
                        <small class="text-muted d-block">ملاحظات</small>
                        <p class="mb-0 bg-light p-2 rounded"><?= $req['reason'] ? $req['reason'] : 'لا يوجد' ?></p>
                    </div>
                </div>
            </div>

            <?php if($req['request_type'] == 'family'): ?>
            <div class="card-box">
                <h5 class="section-title"><i class="fas fa-users me-2"></i> أفراد العائلة (<?= count($family_members) ?>)</h5>
                <div class="row g-3">
                    <?php foreach($family_members as $fam): ?>
                    <div class="col-md-6">
                        <div class="fam-card">
                            <span class="rel-badge"><?= $fam['relationship'] ?></span>
                            <h6 class="fw-bold mt-1 mb-1"><?= $fam['full_name'] ?></h6>
                            <div class="small text-muted">
                                <span class="me-2"><i class="fas fa-id-badge"></i> <?= $fam['national_id'] ?></span>
                                <span><i class="fas fa-birthday-cake"></i> <?= $fam['age'] ?> سنة</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="card-box">
                <h5 class="section-title">حالة الطلب</h5>
                <?php if($req['status'] == 'Pending'): ?>
                    <div class="alert alert-warning text-center fw-bold">قيد الانتظار</div>
                <?php elseif($req['status'] == 'Approved'): ?>
                    <div class="alert alert-success text-center fw-bold">تم الاعتماد</div>
                <?php else: ?>
                    <div class="alert alert-danger text-center fw-bold">مرفوض</div>
                <?php endif; ?>

                <div class="mt-4">
                    <h6 class="fw-bold mb-3">سجل الموافقات</h6>
                    <div class="timeline-item done">
                        <strong>تم رفع الطلب</strong>
                        <div class="small text-muted"><?= $req['created_at'] ?></div>
                    </div>
                    <?php foreach($timeline as $log): 
                        $cls = $log['status'] == 'Approved' ? 'done' : '';
                    ?>
                    <div class="timeline-item <?= $cls ?>">
                        <strong><?= $log['approver_name'] ?></strong>
                        <div class="small <?= $log['status']=='Rejected'?'text-danger':'text-success' ?>"><?= $log['status'] ?></div>
                        <div class="small text-muted"><?= $log['action_date'] ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    $current_user = $this->session->userdata('username');
    if($req['status'] == 'Pending' && $req['current_approver'] == $current_user): 
        $level = 1; if($current_user == '2200') $level = 2; if($current_user == '2515') $level = 3;
?>
<div class="action-bar">
    <button class="btn btn-success px-5 fw-bold rounded-pill" onclick="process(<?= $req['id'] ?>, <?= $level ?>, 'approve')">
        <i class="fas fa-check me-2"></i> قبول الطلب
    </button>
    <button class="btn btn-danger px-5 fw-bold rounded-pill ms-2" onclick="process(<?= $req['id'] ?>, <?= $level ?>, 'reject')">
        <i class="fas fa-times me-2"></i> رفض الطلب
    </button>
</div>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function process(id, lvl, act) {
    Swal.fire({
        title: act==='approve'?'اعتماد؟':'رفض؟', icon:'question', showCancelButton:true, confirmButtonText:'نعم'
    }).then((r)=>{
        if(r.isConfirmed){
            $.post('<?= site_url("users1/do_insurance_approval") ?>', 
            { req_id: id, level: lvl, action: act, '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>' }, 
            function(){ Swal.fire('تم', 'تمت العملية', 'success').then(()=>window.location.href='<?= site_url("users1/insurance_approvals") ?>'); });
        }
    });
}
</script>
</body>
</html>