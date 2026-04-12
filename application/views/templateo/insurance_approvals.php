<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلبات التأمين</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root { --marsom-blue: #001f3f; --marsom-orange: #FF8C00; }
        body { font-family: 'Tajawal'; background: #f4f6f9; }
        
        .header-section { background: var(--marsom-blue); padding: 40px 0 80px; color: white; margin-bottom: -50px; }
        
        .ticket-card {
            background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: 0.3s;
            border-right: 5px solid #ddd;
        }
        .ticket-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .ticket-card.type-family { border-right-color: var(--marsom-orange); }
        .ticket-card.type-self { border-right-color: var(--marsom-blue); }

        .emp-avatar {
            width: 50px; height: 50px; background: #eef2f7; color: var(--marsom-blue);
            border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
        }
        
        .badge-type { padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; }
        .bg-fam { background: #fff3cd; color: #856404; }
        .bg-self { background: #cff4fc; color: #055160; }
        
        .btn-action { width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; transition: 0.2s; border: none; }
        .btn-approve { background: #e6f9ed; color: #28a745; }
        .btn-approve:hover { background: #28a745; color: white; }
        .btn-reject { background: #fbeaea; color: #dc3545; }
        .btn-reject:hover { background: #dc3545; color: white; }
        .btn-view { background: #f0f0f0; color: #333; }
        .btn-view:hover { background: #333; color: white; }
    </style>
</head>
<body>

<div class="header-section">
    <div class="container">
        <h2 class="fw-bold"><i class="fas fa-check-circle"></i> اعتماد التأمين الطبي</h2>
        <p class="opacity-75">إدارة ومراجعة طلبات التأمين المعلقة</p>
    </div>
</div>

<div class="container" style="max-width: 1100px;">
    <?php if(empty($requests)): ?>
        <div class="card border-0 shadow-sm p-5 text-center rounded-4">
            <div class="text-muted display-1 mb-3"><i class="fas fa-clipboard-check"></i></div>
            <h4>لا توجد طلبات معلقة</h4>
            <p>جميع الطلبات تمت معالجتها بنجاح</p>
        </div>
    <?php else: ?>
        <?php foreach($requests as $r): ?>
        <div class="ticket-card <?= $r['request_type'] == 'family' ? 'type-family' : 'type-self' ?>" id="req_<?= $r['id'] ?>">
            <div class="row align-items-center">
                
                <div class="col-md-4 d-flex align-items-center mb-3 mb-md-0">
                    <div class="emp-avatar ms-3"><i class="fas fa-user"></i></div>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark"><?= $r['subscriber_name'] ?></h6>
                        <small class="text-muted"><i class="fas fa-briefcase"></i> <?= $r['department'] ?> | ID: <?= $r['emp_code'] ?></small>
                    </div>
                </div>

                <div class="col-md-3 mb-3 mb-md-0">
                    <?php if($r['request_type'] == 'family'): ?>
                        <span class="badge-type bg-fam"><i class="fas fa-users"></i> عائلي</span>
                    <?php else: ?>
                        <span class="badge-type bg-self"><i class="fas fa-user"></i> فردي</span>
                    <?php endif; ?>
                    <div class="small text-muted mt-1"><i class="fas fa-calendar"></i> <?= date('Y-m-d', strtotime($r['created_at'])) ?></div>
                </div>

                <div class="col-md-3 mb-3 mb-md-0">
                    <?php if(!empty($r['reason'])): ?>
                        <small class="text-muted d-block text-truncate" style="max-width: 200px;">
                            <i class="fas fa-comment-alt"></i> <?= $r['reason'] ?>
                        </small>
                    <?php else: ?>
                        <small class="text-muted">-</small>
                    <?php endif; ?>
                </div>

                <div class="col-md-2 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= site_url('users1/insurance_details/'.$r['id']) ?>" class="btn-action btn-view" title="التفاصيل"><i class="fas fa-eye"></i></a>
                        <button class="btn-action btn-approve" onclick="act(<?= $r['id'] ?>, <?= $r['approval_level'] ?>, 'approve')" title="قبول"><i class="fas fa-check"></i></button>
                        <button class="btn-action btn-reject" onclick="act(<?= $r['id'] ?>, <?= $r['approval_level'] ?>, 'reject')" title="رفض"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function act(id, level, action) {
    Swal.fire({
        title: action==='approve'?'تأكيد القبول؟':'تأكيد الرفض؟', icon: 'question', showCancelButton: true, confirmButtonText: 'نعم'
    }).then((res) => {
        if(res.isConfirmed) {
            $.post('<?= site_url("users1/do_insurance_approval") ?>', 
            { req_id: id, level: level, action: action, '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>' }, 
            function() { $('#req_'+id).slideUp(); Swal.fire('تم', 'تم بنجاح', 'success'); }, 'json');
        }
    });
}
</script>
</body>
</html>s