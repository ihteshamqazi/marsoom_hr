<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>نظام تجديد الهويات</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        body { font-family: 'Tajawal', sans-serif; background: #f3f4f6; padding-bottom: 50px; }
        .hero-bar { background: linear-gradient(135deg, #001f3f 0%, #1e3c72 100%); color: white; padding: 40px 0 60px; margin-bottom: -40px; border-radius: 0 0 30px 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .glass-card { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-bottom: 20px; border: 1px solid rgba(0,0,0,0.02); }
        .glass-card:hover { transform: translateY(-2px); }
        .nav-tabs { border-bottom: 0; }
        .nav-tabs .nav-link { font-weight: 700; border: none; padding: 12px 25px; color: #6c757d; background: rgba(255,255,255,0.8); margin-left: 10px; border-radius: 10px; transition: 0.3s; }
        .nav-tabs .nav-link.active { color: white; background: #FF8C00; box-shadow: 0 4px 10px rgba(255, 140, 0, 0.3); }
        
        /* Workflow Train */
        .workflow-track { position: relative; margin-top: 20px; padding-right: 10px; }
        .workflow-step { display: flex; position: relative; padding-bottom: 25px; }
        .workflow-step:not(:last-child)::after { content: ''; position: absolute; right: 20px; top: 40px; bottom: 0; width: 2px; background: #e9ecef; }
        .step-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; z-index: 2; flex-shrink: 0; margin-left: 15px; }
        .step-done .step-icon { background: #198754; color: white; box-shadow: 0 0 0 4px #d1e7dd; }
        .step-pending .step-icon { background: #f8f9fa; color: #adb5bd; border: 2px solid #dee2e6; }
        .step-content { flex-grow: 1; padding-top: 5px; }
        .step-title { font-weight: bold; font-size: 0.95rem; margin-bottom: 2px; }
        .step-date { font-size: 0.75rem; color: #888; font-family: monospace; }
        .step-user { font-size: 0.85rem; font-weight: bold; color: #001f3f; }
        
        /* Sliders */
        .range-container { background: #f8f9fa; padding: 20px; border-radius: 12px; margin-bottom: 15px; border: 1px solid #e9ecef; }
        .range-label { font-weight: 800; display: flex; justify-content: space-between; margin-bottom: 10px; color: #001f3f; }
        input[type=range] { width: 100%; height: 8px; border-radius: 5px; background: #dee2e6; accent-color: #FF8C00; cursor: pointer; }
        .total-box { background: #001f3f; color: white; padding: 15px; border-radius: 12px; text-align: center; font-size: 1.4rem; font-weight: 800; }
        
        /* Modal */
        .modal-header { background: #001f3f; color: white; }
        .btn-close-white { filter: invert(1); }
    </style>
</head>
<body>

<div class="hero-bar text-center">
    <h2 class="fw-bold mb-3">نظام تجديد الهويات والإقامات</h2>
    <button class="btn btn-warning btn-lg fw-bold shadow px-5 rounded-pill" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="fas fa-plus-circle me-2"></i> تقديم طلب جديد
    </button>
</div>

<div class="container" style="max-width: 1100px;">
    
    <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success fw-bold text-center mt-5 shadow-sm rounded-pill"><i class="fas fa-check-circle me-2"></i> <?= $this->session->flashdata('success') ?></div>
    <?php endif; ?>

    <ul class="nav nav-tabs mt-5 mb-4 justify-content-center">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-tasks"><i class="fas fa-bell me-2"></i> المهام المعلقة (<?= count($my_tasks) ?>)</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-requests"><i class="fas fa-file-alt me-2"></i> طلباتي (<?= count($my_requests) ?>)</button></li>
        <?php if(in_array($user_id, ['1127', '2230', '1001'])): ?>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-history"><i class="fas fa-archive me-2"></i> الأرشيف</button></li>
        <?php endif; ?>
    </ul>

    <div class="tab-content">
        
        <div class="tab-pane fade show active" id="tab-tasks">
            <?php if(!empty($my_tasks)): foreach($my_tasks as $task): ?>
            <div class="glass-card border-end border-5 border-warning">
                <div class="row align-items-center">
                    <div class="col-md-9">
                        <h5 class="fw-bold mb-1"><?= $task['subscriber_name'] ?></h5>
                        <div class="text-muted small"><?= $task['department'] ?> | <span class="text-danger fw-bold">ينتهي: <?= $task['current_expiry_date'] ?></span></div>
                    </div>
                    <div class="col-md-3 text-end"><button class="btn btn-dark fw-bold w-100 rounded-pill" data-bs-toggle="modal" data-bs-target="#modal<?= $task['id'] ?>">تنفيذ الإجراء</button></div>
                </div>
            </div>

            <div class="modal fade" id="modal<?= $task['id'] ?>" tabindex="-1"><div class="modal-dialog modal-lg"><form class="modal-content" action="<?= site_url('users1/process_renewal_system') ?>" method="post"><input type="hidden" name="req_id" value="<?= $task['id'] ?>"><input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>"><div class="modal-header"><h5 class="modal-title fw-bold">تنفيذ المهمة #<?= $task['id'] ?></h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body bg-light">
                
                <div class="bg-white p-3 rounded mb-3 shadow-sm border">
                    <?php if($user_id == '1127' && $task['status'] == 'pending_hr_action'): ?>
                        <input type="hidden" name="action_type" value="assign">
                        <label class="fw-bold mb-2">اختر المدير المقيّم:</label>
                        <select name="evaluator_id" class="form-select select2-modal" style="width:100%" required>
                            <option value="">-- ابحث عن المدير --</option>
                            <?php if(!empty($managers)): foreach($managers as $m): ?>
                                <option value="<?= $m['employee_id'] ?>"><?= $m['subscriber_name'] ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                        <button class="btn btn-primary w-100 mt-4 py-2 fw-bold shadow">إرسال للمقيم</button>

                    <?php elseif($task['status'] == 'pending_evaluation'): ?>
                        <input type="hidden" name="action_type" value="evaluate">
                        <div class="range-container"><div class="range-label"><span>الحضور (25)</span><span class="badge bg-primary" id="v1_<?= $task['id'] ?>">0</span></div><input type="range" name="attendance" min="0" max="25" value="0" oninput="upd(<?= $task['id'] ?>,this,'v1')"></div>
                        <div class="range-container"><div class="range-label"><span>السلوك (10)</span><span class="badge bg-primary" id="v2_<?= $task['id'] ?>">0</span></div><input type="range" name="behaviour" min="0" max="10" value="0" oninput="upd(<?= $task['id'] ?>,this,'v2')"></div>
                        <div class="range-container"><div class="range-label"><span>المهام (65)</span><span class="badge bg-primary" id="v3_<?= $task['id'] ?>">0</span></div><input type="range" name="tasks" min="0" max="65" value="0" oninput="upd(<?= $task['id'] ?>,this,'v3')"></div>
                        <div class="total-box shadow-sm">المجموع: <span id="tot_<?= $task['id'] ?>">0</span>%</div>
                        <button class="btn btn-warning w-100 mt-3 fw-bold py-2 shadow">اعتماد التقييم</button>

                    <?php elseif(in_array($task['status'], ['pending_hr_manager', 'pending_ceo'])): ?>
                        <input type="hidden" name="action_type" value="approve">
                        <div class="alert alert-primary text-center fw-bold fs-5">النتيجة: <?= $task['total_score'] ?>%</div>
                        <button class="btn btn-success w-100 fw-bold py-2 shadow">موافقة واعتماد</button>

                    <?php elseif($task['status'] == 'pending_renewal'): ?>
                        <input type="hidden" name="action_type" value="complete">
                        <div class="text-center py-3"><i class="fas fa-passport fa-3x text-success mb-2"></i><h5>جاهز للتجديد</h5></div>
                        <button class="btn btn-primary w-100 fw-bold py-2 shadow">تم التجديد وإغلاق</button>
                    <?php endif; ?>
                </div>

                <?php
                    // Get Real Names from Controller Data
                    $hr_name  = isset($app_names['1127']) ? $app_names['1127'] : 'HR Admin';
                    $hrm_name = isset($app_names['2230']) ? $app_names['2230'] : 'HR Manager';
                    $ceo_name = isset($app_names['1001']) ? $app_names['1001'] : 'CEO';
                    // Evaluator Name (if assigned in this task)
                    $eval_name = isset($task['evaluator_name']) && $task['evaluator_name'] ? $task['evaluator_name'] : 'المقيم';

                    $steps = [
                        ['k'=>'Created',   'l'=>'تقديم الطلب', 'u'=>$task['subscriber_name']], // Requester
                        ['k'=>'Assigned',  'l'=>'توجيه HR',    'u'=>$hr_name],
                        ['k'=>'Submitted', 'l'=>'التقييم',     'u'=>$eval_name],
                        ['k'=>'HR Manager','l'=>'اعتماد HR',   'u'=>$hrm_name],
                        ['k'=>'CEO',       'l'=>'اعتماد CEO',  'u'=>$ceo_name],
                        ['k'=>'Completed', 'l'=>'تم التجديد',  'u'=>$hr_name]
                    ];
                ?>
                <div class="mt-4 pt-3 border-top">
                    <h6 class="fw-bold text-muted mb-3">مسار العمل (Workflow)</h6>
                    <div class="workflow-track">
                        <?php foreach($steps as $step): 
                            $is_done = false; $log_details = null;
                            if(!empty($task['history_logs'])) { foreach($task['history_logs'] as $log) { if(strpos($log['action_name'], $step['k']) !== false) { $is_done = true; $log_details = $log; break; }}}
                            $status_class = $is_done ? 'step-done' : 'step-pending';
                            
                            // Name Logic: If done, show who did it. If pending, show who is supposed to do it.
                            $display_name = $is_done ? ($log_details['subscriber_name'] ?: $log_details['action_by']) : $step['u'];
                            $display_date = $is_done ? $log_details['created_at'] : '---';
                        ?>
                        <div class="workflow-step <?= $status_class ?>">
                            <div class="step-icon"><i class="fas fa-check"></i></div>
                            <div class="step-content">
                                <div class="step-title"><?= $step['l'] ?></div>
                                <div class="step-user"><?= $display_name ?></div>
                                <div class="step-date"><?= $display_date ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div></form></div></div>
            <?php endforeach; else: ?><div class="text-center py-5 text-muted">لا يوجد مهام</div><?php endif; ?>
        </div>

        <?php foreach(['tab-requests' => $my_requests, 'tab-history' => $all_history] as $tab_id => $data_list): ?>
        <div class="tab-pane fade" id="<?= $tab_id ?>">
            <div class="glass-card">
                <table class="table table-hover align-middle">
                    <thead class="table-light"><tr><th>رقم الطلب</th><th>الموظف</th><th>الحالة</th><th>عرض</th></tr></thead>
                    <tbody>
                        <?php if(!empty($data_list)): foreach($data_list as $r): ?>
                        <tr>
                            <td>#<?= $r['id'] ?></td>
                            <td class="fw-bold"><?= isset($r['subscriber_name']) ? $r['subscriber_name'] : '-' ?></td>
                            <td><span class="badge bg-secondary"><?= $r['status'] ?></span></td>
                            <td><button class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal<?= $r['id'] ?>"><i class="fas fa-eye"></i></button></td>
                        </tr>
                        <div class="modal fade" id="viewModal<?= $r['id'] ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">التفاصيل #<?= $r['id'] ?></h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body">
                            <?php 
                                $hr_name = isset($app_names['1127']) ? $app_names['1127'] : 'HR Admin';
                                $hrm_name = isset($app_names['2230']) ? $app_names['2230'] : 'HR Manager';
                                $ceo_name = isset($app_names['1001']) ? $app_names['1001'] : 'CEO';
                                $ev_name = isset($r['evaluator_name']) && $r['evaluator_name'] ? $r['evaluator_name'] : 'المقيم';
                                $req_name = isset($r['subscriber_name']) ? $r['subscriber_name'] : 'الموظف';

                                $v_steps = [
                                    ['k'=>'Created', 'l'=>'التقديم', 'u'=>$req_name], ['k'=>'Assigned', 'l'=>'توجيه HR', 'u'=>$hr_name],
                                    ['k'=>'Submitted', 'l'=>'التقييم', 'u'=>$ev_name], ['k'=>'HR Manager', 'l'=>'اعتماد HR', 'u'=>$hrm_name],
                                    ['k'=>'CEO', 'l'=>'اعتماد CEO', 'u'=>$ceo_name], ['k'=>'Completed', 'l'=>'تم التجديد', 'u'=>$hr_name]
                                ];
                            ?>
                            <div class="workflow-track">
                                <?php foreach($v_steps as $step): 
                                    $is_done = false; $log_d = null;
                                    if(!empty($r['history_logs'])) { foreach($r['history_logs'] as $l) { if(strpos($l['action_name'], $step['k']) !== false) { $is_done=true; $log_d=$l; break; }}}
                                    $cls = $is_done ? 'step-done' : 'step-pending';
                                    $dn = $is_done ? ($log_d['subscriber_name']?:$log_d['action_by']) : $step['u'];
                                    $dd = $is_done ? $log_d['created_at'] : '---';
                                    echo "<div class='workflow-step $cls'><div class='step-icon'><i class='fas fa-check'></i></div><div class='step-content'><div class='step-title'>{$step['l']}</div><div class='step-user'>$dn</div><div class='step-date'>$dd</div></div></div>";
                                endforeach; ?>
                            </div>
                        </div></div></div></div>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="<?= site_url('users1/process_renewal_system') ?>" method="post">
            <input type="hidden" name="action_type" value="create">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            
            <div class="modal-header">
                <h5 class="modal-title fw-bold">طلب تجديد جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                
                <?php if(isset($user_id) && $user_id == '1127'): ?>
                    <div class="mb-3">
                        <label class="fw-bold mb-2">اختر الموظف (تقديم نيابة عن):</label>
                        <select name="emp_id_manual" class="form-select select2-modal" style="width:100%" required>
                            <option value="">-- ابحث بالاسم أو الرقم الوظيفي --</option>
                            <?php if(!empty($all_employees)): foreach($all_employees as $emp): ?>
                                <option value="<?= $emp['employee_id'] ?>">
                                    <?= $emp['subscriber_name'] ?> (<?= $emp['employee_id'] ?>)
                                </option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <div class="mb-3"><strong>الموظف:</strong> <?= $my_info['subscriber_name'] ?></div>
                <?php endif; ?>

                <label class="fw-bold text-danger">تاريخ الانتهاء الحالي *</label>
                <input type="date" name="expiry_date" class="form-control" required value="<?= $my_info['Iqama_expiry_date'] ?? '' ?>">
            </div>
            
            <div class="modal-footer">
                <button class="btn btn-warning w-100 fw-bold">إرسال الطلب</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.modal').on('shown.bs.modal', function () {
            $(this).find('.select2-modal').select2({ theme: 'bootstrap-5', dir: 'rtl', dropdownParent: $(this) });
        });
    });
    function upd(id, el, lbl) {
        $('#' + lbl + '_' + id).text($(el).val());
        let f = $(el).closest('form');
        let a = parseInt(f.find('input[name="attendance"]').val())||0;
        let b = parseInt(f.find('input[name="behaviour"]').val())||0;
        let c = parseInt(f.find('input[name="tasks"]').val())||0;
        $('#tot_' + id).text(a+b+c);
    }
</script>

</body>
</html>