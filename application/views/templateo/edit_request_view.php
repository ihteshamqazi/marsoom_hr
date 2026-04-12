<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الطلب وسير العمل</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" rel="stylesheet" />

    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;}
        body{font-family:'Tajawal',sans-serif;overflow-y:auto !important;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative; min-height: 100vh;}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        
        .main-container{padding:30px 15px;position:relative;z-index:1; max-width: 1100px;}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.6rem;color:var(--text-light);margin-bottom:24px;text-align:center;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        
        /* Cards */
        .modern-card { background:rgba(255,255,255,.95); backdrop-filter:blur(10px); border-radius:15px; box-shadow:0 10px 30px rgba(0,0,0,.15); overflow: hidden; margin-bottom: 25px; border: 1px solid rgba(255,255,255,0.2); }
        .card-header-custom { background: rgba(0, 31, 63, 0.05); padding: 15px 25px; border-bottom: 1px solid rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between; }
        .card-title-custom { font-family: 'El Messiri', sans-serif; font-size: 1.4rem; color: var(--marsom-blue); margin: 0; font-weight: bold; }
        .card-body-custom { padding: 25px; }

        /* Form Elements */
        .form-label { font-weight: 700; color: #555; margin-bottom: 0.5rem; display: block; font-size: 0.9rem; }
        .form-label i { color: var(--marsom-blue); margin-left: 5px; }
        .form-control, .form-select { border-radius: 8px; padding: 0.6rem 1rem; border: 1px solid #dee2e6; font-size: 0.95rem; }
        .form-control:focus, .form-select:focus { border-color: var(--marsom-orange); box-shadow: 0 0 0 0.2rem rgba(255, 140, 0, 0.15); }

        /* Workflow Table */
        .workflow-step { background: #fff; border: 1px solid #eee; border-radius: 10px; padding: 15px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; transition: all 0.3s; }
        .workflow-step:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-color: #ddd; }
        .step-info { display: flex; align-items: center; gap: 15px; }
        .step-badge { width: 35px; height: 35px; background: var(--marsom-blue); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-family: 'El Messiri'; }
        .step-status { font-size: 0.85rem; font-weight: 600; }
        .status-pending { color: #f39c12; }
        .status-approved { color: #27ae60; }
        .status-rejected { color: #c0392b; }
        
        .approver-select-container { min-width: 300px; }

        /* Actions */
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .top-actions a:hover{background:rgba(255,255,255,.25);}
        
        .btn-save { background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); color: white; border:none; padding: 12px 40px; border-radius: 30px; font-weight: bold; font-size: 1.1rem; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(46, 204, 113, 0.4); color: white; }
    </style>
</head>
<body>

<div class="top-actions">
    <a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
    <a href="<?php echo site_url('users1/main_emp'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="main-container container-xl">
    <div class="text-center">
        <h1 class="page-title">
            <i class="fa fa-pen-to-square"></i> تعديل الطلب وسير العمل
        </h1>
    </div>

    <form action="<?php echo base_url('users1/update_request_submission'); ?>" method="post">
        <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
        <input type="hidden" name="type" value="<?php echo $request['type']; ?>">
        <input type="hidden" name="emp_id" value="<?php echo $request['emp_id']; ?>">

        <div class="row">
            <div class="col-lg-7">
                <div class="modern-card">
                    <div class="card-header-custom">
                        <div class="card-title-custom"><i class="fa fa-file-alt"></i> بيانات الطلب</div>
                        <span class="badge bg-secondary"><?php echo $request['order_name']; ?></span>
                    </div>
                    <div class="card-body-custom">
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="fa fa-sticky-note"></i> ملاحظات عامة</label>
                            <textarea class="form-control" name="note"><?php echo $request['note']; ?></textarea>
                        </div>

                        <?php if($request['type'] == 5): ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">تاريخ البداية</label>
                                <input type="date" class="form-control" name="vac_start" value="<?php echo $request['vac_start']; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">تاريخ النهاية</label>
                                <input type="date" class="form-control" name="vac_end" value="<?php echo $request['vac_end']; ?>">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">الموظف البديل</label>
                                <select class="form-select select2" name="delegation_employee_id">
                                    <option value="">لا يوجد</option>
                                    <?php foreach($employees as $emp): ?>
                                        <option value="<?php echo $emp['username']; ?>" <?php echo ($request['delegation_employee_id'] == $emp['username']) ? 'selected' : ''; ?>>
                                            <?php echo $emp['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($request['type'] == 1): ?>
                        <div class="mb-3">
                            <label class="form-label">تاريخ آخر يوم عمل</label>
                            <input type="date" class="form-control" name="date_of_the_last_working" value="<?php echo $request['date_of_the_last_working']; ?>">
                        </div>
                        <?php endif; ?>
                        
                        </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="modern-card">
                    <div class="card-header-custom">
                        <div class="card-title-custom"><i class="fa fa-users-gear"></i> سلسلة الاعتمادات</div>
                        <small class="text-muted">يمكنك تغيير المسؤول</small>
                    </div>
                    <div class="card-body-custom" style="background: #f9f9f9;">
                        
                        <?php if(!empty($workflow_steps)): ?>
                            <?php foreach($workflow_steps as $step): ?>
                                <div class="workflow-step">
                                    <div class="step-info">
                                        <div class="step-badge"><?php echo $step['approval_level']; ?></div>
                                        <div>
                                            <div style="font-weight:bold; color:#001f3f;">المستوى <?php echo $step['approval_level']; ?></div>
                                            <div class="step-status <?php echo 'status-'.$step['status']; ?>">
                                                <?php 
                                                if($step['status'] == 'pending') echo '<i class="fa fa-clock"></i> بانتظار الإجراء';
                                                elseif($step['status'] == 'approved') echo '<i class="fa fa-check"></i> تمت الموافقة';
                                                elseif($step['status'] == 'rejected') echo '<i class="fa fa-times"></i> مرفوض';
                                                else echo $step['status'];
                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="approver-select-container">
                                        <?php if($step['status'] == 'pending'): ?>
                                            <select class="form-select select2" name="workflow[<?php echo $step['id']; ?>]">
                                                <?php foreach($employees as $emp): ?>
                                                    <option value="<?php echo $emp['username']; ?>" <?php echo ($step['approver_id'] == $emp['username']) ? 'selected' : ''; ?>>
                                                        <?php echo $emp['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <input type="text" class="form-control" disabled value="<?php echo $step['approver_name']; ?>" style="background: #fff;">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-warning">لا يوجد سلسلة اعتمادات محددة لهذا الطلب.</div>
                        <?php endif; ?>

                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-save w-100 shadow-lg">
                        <i class="fa fa-save"></i> حفظ التغييرات وتحديث السلسلة
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: "bootstrap-5",
            width: '100%',
            dir: "rtl"
        });
    });
</script>

</body>
</html>