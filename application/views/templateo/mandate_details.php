<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تفاصيل الانتداب #<?= $req['id'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
        :root { --primary: #001f3f; --accent: #FF8C00; --bg-light: #f4f6f9; --success: #198754; --danger: #dc3545; --text-dark: #2c3e50; }
        body { font-family: 'Tajawal', sans-serif; background: var(--bg-light); color: var(--text-dark); padding-bottom: 80px; }
        .hero-header { background: linear-gradient(135deg, var(--primary) 0%, #1e3c72 100%); color: white; padding: 40px 0 80px; margin-bottom: -50px; }
        .status-badge-lg { font-size: 1rem; padding: 8px 20px; border-radius: 50px; border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.15); backdrop-filter: blur(5px); }
        .glass-card { background: white; border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 25px; overflow: hidden; }
        .card-header-custom { padding: 20px 25px; border-bottom: 1px solid #f0f0f0; font-weight: 700; color: var(--primary); background: #fff; display: flex; align-items: center; gap: 10px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); text-align: center; height: 100%; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { font-size: 1.5rem; color: var(--accent); margin-bottom: 10px; }
        .stat-value { font-size: 1.4rem; font-weight: 800; color: var(--primary); }
        .stat-label { font-size: 0.9rem; color: #7f8c8d; }
        .tracking-list { padding: 0; list-style: none; position: relative; }
        .tracking-list::before { content: ''; position: absolute; top: 0; bottom: 0; right: 20px; width: 2px; background: #e9ecef; }
        .tracking-item { position: relative; padding: 0 50px 30px 0; }
        .tracking-icon { position: absolute; right: 6px; top: 0; width: 30px; height: 30px; border-radius: 50%; background: #fff; border: 2px solid #e9ecef; display: flex; align-items: center; justify-content: center; z-index: 2; font-size: 0.8rem; color: #ccc; }
        .tracking-item.is-completed .tracking-icon { background: var(--success); border-color: var(--success); color: white; }
        .tracking-item.is-active .tracking-icon { background: var(--accent); border-color: var(--accent); color: white; box-shadow: 0 0 0 4px rgba(255, 140, 0, 0.2); animation: pulse 2s infinite; }
        .tracking-item.is-rejected .tracking-icon { background: var(--danger); border-color: var(--danger); color: white; }
        .tracking-title { font-weight: 700; color: var(--primary); margin-bottom: 2px; }
        .tracking-role { font-size: 0.85rem; color: #666; background: #f8f9fa; padding: 2px 8px; border-radius: 4px; }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(255, 140, 0, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(255, 140, 0, 0); } 100% { box-shadow: 0 0 0 0 rgba(255, 140, 0, 0); } }
        .file-box { display: flex; align-items: center; padding: 12px; border: 1px solid #eee; border-radius: 8px; margin-bottom: 10px; text-decoration: none; color: inherit; transition: 0.2s; }
        .file-box:hover { background: #f9f9f9; border-color: var(--accent); }
        .file-icon { width: 40px; height: 40px; border-radius: 8px; background: #eef2f7; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: var(--primary); margin-left: 12px; }
        .action-bar { position: fixed; bottom: 0; left: 0; right: 0; background: white; padding: 15px; box-shadow: 0 -5px 20px rgba(0,0,0,0.1); z-index: 1000; display: flex; justify-content: center; gap: 15px; }
        .finance-table td { padding: 12px 15px; border-bottom: 1px solid #f0f0f0; }
        .total-row { background: #fdfdfd; font-size: 1.1rem; }
        
        /* Admin Modal Styles */
        .admin-leg-row { background: #f8f9fa; padding: 5px; border-radius: 6px; border: 1px solid #e9ecef; }
        
        @media print { .hero-header { padding: 20px; margin-bottom: 0; background: #fff !important; color: #000 !important; } .action-bar, .btn-back, .btn-warning { display: none !important; } }
    </style>
</head>
<body>

<?php 
    // HR Admins including new IDs
    $hr_admins = ['2230', '2515', '2774', '2784', '1835', '2901', '1693', '2909'];
    $my_id = $this->session->userdata('username');
    $is_admin = in_array($my_id, $hr_admins);
?>

<div class="hero-header">
    <div class="container text-center">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <a href="<?= site_url('users1/mandate_approvals') ?>" class="btn btn-outline-light btn-sm btn-back rounded-pill px-3">
                <i class="fas fa-arrow-right me-2"></i> عودة للقائمة
            </a>
            
            <?php if($is_admin): ?>
            <button type="button" class="btn btn-warning rounded-pill px-4 ms-2" data-bs-toggle="modal" data-bs-target="#adminEditModal">
                <i class="fas fa-edit me-2"></i> HR Override
            </button>
            <?php endif; ?>

            <span class="status-badge-lg">
                <?php if($req['status'] == 'Approved'): ?>
                    <i class="fas fa-check-circle me-1"></i> معتمد
                <?php elseif($req['status'] == 'Rejected'): ?>
                    <i class="fas fa-times-circle me-1"></i> مرفوض
                <?php else: ?>
                    <i class="fas fa-clock me-1"></i> قيد الإجراء
                <?php endif; ?>
            </span>
        </div>
        
        <a href="<?= site_url('users1/print_mandate/'.$req['id']) ?>" target="_blank" class="btn btn-light rounded-pill px-4">
            <i class="fas fa-print me-2"></i> طباعة النموذج
        </a>
        
        <h2 class="fw-bold mb-1 mt-3"><?= $req['subscriber_name'] ?></h2>
        <p class="opacity-75 mb-4"><?= $req['emp_department'] ?? $req['department'] ?> | ID: <?= $req['employee_id'] ?></p>
        
        <div class="badge bg-white text-dark px-3 py-2 rounded-pill shadow-sm">
            رقم الطلب #<?= $req['id'] ?> &bull; <?= $req['request_date'] ?>
        </div>
    </div>
</div>

<div class="container" style="max-width: 1200px;">
    
    <?php if($req['status'] == 'Rejected' && !empty($req['rejection_reason'])): ?>
        <div class="alert alert-danger border-danger mt-4 mb-4 shadow-sm">
            <div class="d-flex align-items-center mb-1">
                <i class="fas fa-exclamation-circle fs-5 me-2"></i>
                <h6 class="fw-bold mb-0">تم رفض الطلب</h6>
            </div>
            <hr class="my-2">
            <small class="text-muted">السبب:</small>
            <p class="mb-0 fw-bold"><?= htmlspecialchars($req['rejection_reason']) ?></p>
        </div>
    <?php endif; ?>

    <div class="row g-3 stats-row justify-content-center">
        <div class="col-md-3 col-6"><div class="stat-card"><div class="stat-icon"><i class="fas fa-calendar-alt"></i></div><div class="stat-value"><?= $req['start_date'] ?></div><div class="stat-label">تاريخ البداية</div></div></div>
        <div class="col-md-3 col-6"><div class="stat-card"><div class="stat-icon"><i class="fas fa-hourglass-half"></i></div><div class="stat-value"><?= $req['duration_days'] ?> أيام</div><div class="stat-label">مدة الانتداب</div></div></div>
        <div class="col-md-3 col-6"><div class="stat-card"><div class="stat-icon"><i class="fas fa-route"></i></div><div class="stat-value"><?= $req['road_total_km'] ?> كم</div><div class="stat-label">المسافة الكلية</div></div></div>
        <div class="col-md-3 col-6"><div class="stat-card border-bottom border-4 border-warning"><div class="stat-icon text-success"><i class="fas fa-money-bill-wave"></i></div><div class="stat-value text-success"><?= number_format($req['total_amount']) ?></div><div class="stat-label">الإجمالي (SAR)</div></div></div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="glass-card">
                <div class="card-header-custom"><i class="fas fa-map-marked-alt text-warning"></i> خط السير</div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="vertical-align: middle;">
                        <thead class="table-light"><tr><th>من</th><th>إلى</th><th>المسافة</th><th>الوسيلة</th></tr></thead>
                        <tbody>
                             <?php if(!empty($destinations)): foreach($destinations as $d): ?>
                            <tr>
                                <td class="fw-bold"><?= $d['from_city'] ?></td>
                                <td class="fw-bold"><?= $d['to_city'] ?></td>
                                <td><?= $d['distance_km'] ?> km</td>
                                <td>
                                    <?php if(($d['leg_mode'] ?? 'road') == 'air'): ?><span class="badge bg-primary bg-opacity-10 text-primary"><i class="fas fa-plane"></i> طيران</span>
                                    <?php else: ?><span class="badge bg-warning bg-opacity-10 text-dark"><i class="fas fa-car"></i> سيارة</span><?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; else: ?><tr><td colspan="4" class="text-center py-3">الوجهات غير محددة</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="glass-card">
                <div class="card-header-custom"><i class="fas fa-file-invoice-dollar text-success"></i> التفاصيل المالية</div>
                <div class="p-0">
                    <table class="table finance-table mb-0 w-100">
                        <tbody>
                            <tr><td width="60%" class="text-muted">بدل الانتداب اليومي<br><small class="text-muted">يتم حسابه بناءً على عدد الأيام (<?= $req['duration_days'] ?>) والمسافة</small></td><td class="fw-bold text-end"><?= number_format($req['allowance_amount'], 2) ?> SAR</td></tr>
                            <?php if($req['road_fuel_amount'] > 0): ?><tr><td class="text-muted">تعويض الوقود (Fuel)<br><small class="text-muted">للمسافات المقطوعة بالسيارة (70 ريال / 100 كم)</small></td><td class="fw-bold text-end text-primary"><?= number_format($req['road_fuel_amount'], 2) ?> SAR</td></tr><?php endif; ?>
                            <?php if($req['ticket_amount'] > 0): ?><tr><td class="text-muted">تذاكر الطيران (Air Tickets)</td><td class="fw-bold text-end text-danger"><?= number_format($req['ticket_amount'], 2) ?> SAR</td></tr><?php endif; ?>
                            <tr class="total-row"><td class="text-primary fw-bold">المبلغ الإجمالي المستحق</td><td class="text-success fw-bolder fs-5 text-end"><?= number_format($req['total_amount'], 2) ?> SAR</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="glass-card">
                <div class="card-header-custom"><i class="fas fa-bullseye text-danger"></i> الغرض والأهداف</div>
                <div class="p-4">
                    <div class="mb-3"><label class="small text-muted fw-bold">شرح المهمة:</label><p class="lead fs-6"><?= $req['reason'] ?></p></div>
                    <hr class="opacity-10">
                    <label class="small text-muted fw-bold mb-2">قائمة الأهداف:</label>
                    <?php if(empty($goals)): ?><p class="text-muted">لا يوجد أهداف.</p><?php else: ?>
                        <?php foreach($goals as $g): ?>
                        <div class="d-flex align-items-center mb-2 p-2 rounded border bg-light">
                            <input class="form-check-input goal-checkbox me-2 ms-2" type="checkbox" value="<?= $g['id'] ?>" <?= $g['is_achieved'] ? 'checked' : '' ?>>
                            <span class="<?= $g['is_achieved'] ? 'text-decoration-line-through text-muted' : '' ?>"><?= $g['goal_text'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="glass-card">
                <div class="card-header-custom"><span><i class="fas fa-history text-primary"></i> مسار الموافقات (Workflow)</span></div>
                <div class="p-4">
                    <ul class="tracking-list">
                        <li class="tracking-item is-completed">
                            <div class="tracking-icon"><i class="fas fa-file-upload"></i></div>
                            <div class="tracking-content">
                                <div class="tracking-title">تم تقديم الطلب</div>
                                <div class="tracking-role"><?= $req['subscriber_name'] ?></div>
                                <span class="tracking-date"><?= $req['request_date'] ?></span>
                            </div>
                        </li>

                        <?php 
                        $has_manager = false; $has_hr = false; $has_finance = false; $rejected = ($req['status'] == 'Rejected');
                        if (!empty($timeline)) {
                            foreach($timeline as $log) {
                                // Updated IDs logic
                                if (in_array($log['approver_id'], ['2784', '2774', '2230'])) $has_hr = true;
                                elseif (in_array($log['approver_id'], ['1693', '2909'])) $has_finance = true;
                                else $has_manager = true;

                                $cls = ($log['status']=='Approved') ? 'is-completed' : (($log['status']=='Rejected') ? 'is-rejected' : 'is-active');
                                $ico = ($log['status']=='Approved') ? 'fa-check' : (($log['status']=='Rejected') ? 'fa-times' : 'fa-clock');
                                
                                $role = 'Direct Manager';
                                if(in_array($log['approver_id'], ['2784', '2774', '2230'])) $role = 'HR Specialist';
                                if(in_array($log['approver_id'], ['1693', '2909'])) $role = 'Finance Manager';
                                ?>
                                <li class="tracking-item <?= $cls ?>">
                                    <div class="tracking-icon"><i class="fas <?= $ico ?>"></i></div>
                                    <div class="tracking-content">
                                        <div class="tracking-title"><?= $log['approver_name'] ?: 'Approver' ?></div>
                                        <div class="tracking-role badge bg-light text-dark border"><?= $role ?></div>
                                        <div class="small mt-1 text-<?= ($log['status']=='Rejected')?'danger':(($log['status']=='Approved')?'success':'warning') ?>"><?= $log['status'] ?></div>
                                        <?php if($log['action_date']): ?><span class="tracking-date"><?= $log['action_date'] ?></span><?php endif; ?>
                                    </div>
                                </li>
                                <?php
                            }
                        }
                        
                        // FUTURE STEPS VISUALIZATION
                        if (!$has_manager && !$rejected) { ?>
                            <li class="tracking-item is-active">
                                <div class="tracking-icon"><i class="fas fa-user-tie"></i></div>
                                <div class="tracking-content"><div class="tracking-title"><?= $req['current_approver_name'] ?: 'Direct Manager' ?></div><div class="tracking-role">Direct Manager</div><span class="text-warning small fw-bold">بانتظار الموافقة...</span></div>
                            </li>
                        <?php }
                        if (!$has_hr && !$rejected) { ?>
                            <li class="tracking-item">
                                <div class="tracking-icon" style="border-style:dashed"><i class="fas fa-users-cog text-muted"></i></div>
                                <div class="tracking-content opacity-50"><div class="tracking-title"><?= isset($hr_name)?$hr_name:'HR Specialist' ?></div><div class="tracking-role">HR Specialist</div><span class="small text-muted">الخطوة القادمة</span></div>
                            </li>
                        <?php }
                        if (!$has_finance && !$rejected) { ?>
                            <li class="tracking-item">
                                <div class="tracking-icon" style="border-style:dashed"><i class="fas fa-coins text-muted"></i></div>
                                <div class="tracking-content opacity-50"><div class="tracking-title"><?= isset($fin_name)?$fin_name:'Finance Manager' ?></div><div class="tracking-role">Finance Manager</div><span class="small text-muted">الخطوة الأخيرة</span></div>
                            </li>
                        <?php } ?>

                        <li class="tracking-item <?= ($req['status'] == 'Approved') ? 'is-completed' : '' ?>">
                            <div class="tracking-icon"><i class="fas fa-flag-checkered"></i></div>
                            <div class="tracking-content <?= ($req['status'] != 'Approved') ? 'opacity-50' : '' ?>"><div class="tracking-title">إغلاق الطلب</div><div class="tracking-role">النهاية</div></div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="glass-card">
                <div class="card-header-custom"><i class="fas fa-paperclip text-secondary"></i> المرفقات</div>
                <div class="p-3">
                    <?php 
                    $has_files = false;
                    for ($i = 1; $i <= 5; $i++) {
                        $col = 'attachment' . $i;
                        if (!empty($req[$col])) {
                            $has_files = true;
                            $ext = strtolower(pathinfo($req[$col], PATHINFO_EXTENSION));
                            ?>
                            <a href="<?= base_url('uploads/documents/' . $req[$col]) ?>" target="_blank" class="file-box">
                                <div class="file-icon"><i class="fas fa-file"></i></div>
                                <div class="ms-2 flex-grow-1"><div class="fw-bold fs-7">ملف <?= $i ?></div><small class="text-muted text-uppercase"><?= $ext ?></small></div>
                                <i class="fas fa-download text-muted"></i>
                            </a>
                            <?php
                        }
                    }
                    if (!$has_files): echo '<div class="text-center py-4 text-muted">لا توجد مرفقات</div>'; endif; 
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if($req['status'] == 'Pending' && $req['current_approver'] == $my_id): ?>
<div class="action-bar">
    <button class="btn btn-danger px-4 rounded-pill fw-bold" onclick="processDetail(<?= $req['id'] ?>, 'reject')"><i class="fas fa-times me-2"></i> رفض الطلب</button>
    <button class="btn btn-success px-5 rounded-pill fw-bold shadow" onclick="processDetail(<?= $req['id'] ?>, 'approve')"><i class="fas fa-check me-2"></i> اعتماد الطلب</button>
</div>
<?php endif; ?>

<?php if($is_admin): ?>
<div class="modal fade" id="adminEditModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning"><h5 class="modal-title fw-bold">Admin Control</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form id="hrAdminForm">
                    <input type="hidden" name="req_id" value="<?= $req['id'] ?>">
                    <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>">
                    
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <h6 class="fw-bold text-primary mb-3">Basic Info & Status</h6>
                            <div class="row g-3">
                                <div class="col-md-6"><label>Start</label><input type="date" name="start_date" class="form-control" value="<?= $req['start_date'] ?>"></div>
                                <div class="col-md-6"><label>End</label><input type="date" name="end_date" class="form-control" value="<?= $req['end_date'] ?>"></div>
                                <div class="col-md-4"><label>Days</label><input type="number" name="duration_days" class="form-control" value="<?= $req['duration_days'] ?>"></div>
                                <div class="col-md-4"><label>Status</label><select name="status" class="form-select"><option value="Pending" <?=$req['status']=='Pending'?'selected':''?>>Pending</option><option value="Approved" <?=$req['status']=='Approved'?'selected':''?>>Approved</option><option value="Rejected" <?=$req['status']=='Rejected'?'selected':''?>>Rejected</option></select></div>
                                <div class="col-md-4"><label>Approver</label><input type="text" name="current_approver" class="form-control" value="<?= $req['current_approver'] ?>"></div>
                            </div>
                            
                            <h6 class="fw-bold text-primary mt-4 mb-3">Financials (Manual Override)</h6>
                            <div class="row g-3">
                                <div class="col-md-3"><label>Allow.</label><input type="number" step="0.01" name="allowance_amount" id="adm_allowance" class="form-control" value="<?= $req['allowance_amount'] ?>" oninput="recalcAdminTotal()"></div>
                                <div class="col-md-3"><label>Fuel</label><input type="number" step="0.01" name="road_fuel_amount" id="adm_fuel" class="form-control" value="<?= $req['road_fuel_amount'] ?>" oninput="recalcAdminTotal()"></div>
                                <div class="col-md-3"><label>Ticket</label><input type="number" step="0.01" name="ticket_amount" id="adm_ticket" class="form-control" value="<?= $req['ticket_amount'] ?>" oninput="recalcAdminTotal()"></div>
                                <div class="col-md-3"><label class="text-success">Total</label><input type="number" step="0.01" name="total_amount" id="adm_total" class="form-control fw-bold border-success" value="<?= $req['total_amount'] ?>" readonly></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-primary mb-0">Route Editor (تعديل المدن)</h6>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="addAdminLeg()">+ Add Leg</button>
                            </div>
                            
                            <div id="adminRouteContainer" style="max-height: 400px; overflow-y: auto;">
                                <?php if(!empty($destinations)): foreach($destinations as $i => $d): ?>
                                <div class="row g-1 mb-2 align-items-center admin-leg-row">
                                    <div class="col-4"><input type="text" name="legs[<?=$i?>][from]" class="form-control form-control-sm" value="<?=$d['from_city']?>" placeholder="From"></div>
                                    <div class="col-4"><input type="text" name="legs[<?=$i?>][to]" class="form-control form-control-sm" value="<?=$d['to_city']?>" placeholder="To"></div>
                                    <div class="col-2"><input type="number" name="legs[<?=$i?>][km]" class="form-control form-control-sm" value="<?=$d['distance_km']?>" placeholder="KM"></div>
                                    <div class="col-2">
                                        <select name="legs[<?=$i?>][mode]" class="form-select form-select-sm">
                                            <option value="road" <?=$d['leg_mode']=='road'?'selected':''?>>Car</option>
                                            <option value="air" <?=$d['leg_mode']=='air'?'selected':''?>>Air</option>
                                        </select>
                                    </div>
                                    <div class="col-12 text-end mt-1"><button type="button" class="btn btn-danger btn-sm p-0 px-2" onclick="this.closest('.admin-leg-row').remove()">Remove</button></div>
                                </div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" onclick="submitHrEdit()">Save All Changes</button></div>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// 1. Goal Update
$('.goal-checkbox').on('change', function() {
    $.post('<?= site_url("users1/update_mandate_goal") ?>', {
        goal_id: $(this).val(), 
        is_achieved: $(this).is(':checked') ? 1 : 0, 
        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>' 
    });
});

// 2. Process Approval/Rejection
function processDetail(reqId, action) {
    if (action === 'reject') {
        Swal.fire({
            title: 'سبب الرفض',
            input: 'textarea',
            inputLabel: 'يرجى كتابة سبب الرفض أدناه',
            inputPlaceholder: 'السبب...',
            inputAttributes: { 'aria-label': 'اكتب سبب الرفض' },
            showCancelButton: true,
            confirmButtonText: 'تأكيد الرفض',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#dc3545',
            inputValidator: (value) => { if (!value) return 'يجب كتابة سبب الرفض!' }
        }).then((result) => {
            if (result.isConfirmed) submitDecision(reqId, action, result.value);
        });
    } else {
        Swal.fire({
            title: 'تأكيد الاعتماد',
            text: "هل أنت متأكد من اعتماد هذا الطلب؟",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'نعم، اعتمد',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#198754'
        }).then((result) => {
            if (result.isConfirmed) submitDecision(reqId, action, null);
        });
    }
}

// 3. Send to Server
function submitDecision(reqId, action, reason) {
    Swal.fire({ title: 'جاري المعالجة...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    
    $.post('<?= site_url("users1/do_mandate_approval") ?>', { 
        req_id: reqId, 
        action: action, 
        reason: reason, 
        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>' 
    }, function(response) {
        try {
            let res = JSON.parse(response);
            if(res.status === 'success') {
                Swal.fire('تم', res.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('خطأ', res.message, 'error');
            }
        } catch(e) {
            console.error(e);
            Swal.fire('خطأ', 'حدث خطأ غير متوقع', 'error');
        }
    });
}

// 4. HR Admin Calc
function recalcAdminTotal() {
    let a = parseFloat($('#adm_allowance').val())||0, f = parseFloat($('#adm_fuel').val())||0, t = parseFloat($('#adm_ticket').val())||0;
    $('#adm_total').val((a+f+t).toFixed(2));
}

// 5. Submit Admin Edit
function submitHrEdit() {
    if(!confirm('Force update?')) return;
    $.post('<?= site_url("users1/admin_update_mandate") ?>', $('#hrAdminForm').serialize(), function(r) { 
        try{
            if(JSON.parse(r).status=='success') location.reload();
            else alert(JSON.parse(r).message);
        } catch(e){ location.reload(); } 
    });
}

// 6. Admin Route Editor Logic
let legIndex = 100;
function addAdminLeg() {
    const html = `
    <div class="row g-1 mb-2 align-items-center admin-leg-row">
        <div class="col-4"><input type="text" name="legs[${legIndex}][from]" class="form-control form-control-sm" placeholder="From"></div>
        <div class="col-4"><input type="text" name="legs[${legIndex}][to]" class="form-control form-control-sm" placeholder="To"></div>
        <div class="col-2"><input type="number" name="legs[${legIndex}][km]" class="form-control form-control-sm" placeholder="KM"></div>
        <div class="col-2"><select name="legs[${legIndex}][mode]" class="form-select form-select-sm"><option value="road">Car</option><option value="air">Air</option></select></div>
        <div class="col-12 text-end mt-1"><button type="button" class="btn btn-danger btn-sm p-0 px-2" onclick="this.closest('.admin-leg-row').remove()">Remove</button></div>
    </div>`;
    $('#adminRouteContainer').append(html);
    legIndex++;
}
</script>

</body>
</html>