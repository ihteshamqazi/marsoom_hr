<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المهام</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        :root {
            --marsom-blue: #001f3f;
            --marsom-orange: #FF8C00;
            --text-light: #fff;
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, var(--marsom-blue) 0%, #34495e 50%, var(--marsom-orange) 100%);
            background-size: 400% 400%;
            animation: gradientAnimation 20s ease infinite;
            min-height: 100vh;
            color: #333;
            overflow-x: hidden;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Particles Background */
        .particles { position: fixed; inset: 0; z-index: -1; overflow: hidden; }
        .particle { position: absolute; background: rgba(255, 140, 0, 0.15); clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%); animation: float 25s infinite ease-in-out; opacity: 0; }
        .particle:nth-child(even) { background: rgba(0, 31, 63, 0.15); }
        @keyframes float { 0% { transform: translateY(0) rotate(0deg); opacity: 0; } 20% { opacity: 1; } 80% { opacity: 1; } 100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; } }

        /* Navigation Buttons */
        .top-actions { position: fixed; top: 20px; right: 20px; display: flex; gap: 10px; z-index: 100; }
        .top-nav-button {
            background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px; padding: 8px 15px; color: white; text-decoration: none;
            font-family: 'El Messiri', sans-serif; font-weight: 500; display: flex; align-items: center; gap: 8px;
            transition: all 0.3s ease; backdrop-filter: blur(5px);
        }
        .top-nav-button:hover { background: rgba(255, 255, 255, 0.3); color: var(--marsom-orange); transform: translateY(-2px); }

        /* Main Container */
        .main-container { max-width: 1200px; margin: 80px auto 40px; padding: 0 15px; }

        .page-title {
            font-family: 'El Messiri', sans-serif; font-weight: 700; color: white; text-align: center;
            margin-bottom: 30px; font-size: 2.5rem; text-shadow: 0 4px 10px rgba(0,0,0,0.3);
            position: relative; display: inline-block; padding-bottom: 10px; width: 100%;
        }
        .page-title::after {
            content: ''; position: absolute; bottom: 0; left: 50%; transform: translateX(-50%);
            width: 80px; height: 4px; background: linear-gradient(90deg, var(--marsom-blue), var(--marsom-orange)); border-radius: 2px;
        }

        /* Cards */
        .glass-card {
            background: var(--glass-bg); backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 16px;
            padding: 30px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); margin-bottom: 30px;
        }

        .section-header {
            border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px; color: var(--marsom-blue);
        }
        .section-header i { color: var(--marsom-orange); font-size: 1.4rem; }
        .section-header h4 { margin: 0; font-weight: 700; font-family: 'El Messiri', sans-serif; }

        /* Form Elements */
        .form-label { font-weight: 600; color: #444; margin-bottom: 8px; }
        .form-control, .form-select {
            border-radius: 8px; border: 1px solid #ddd; padding: 10px 15px;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus { border-color: var(--marsom-orange); box-shadow: 0 0 0 3px rgba(255, 140, 0, 0.15); }
        
        .btn-submit {
            background: var(--marsom-orange); border: none; color: white;
            padding: 12px 30px; border-radius: 8px; font-weight: 700; width: 100%;
            transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-submit:hover { background: #e07b00; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(224, 123, 0, 0.3); }

        /* Table */
        .table-responsive { border-radius: 12px; overflow: hidden; }
        .custom-table thead th {
            background-color: var(--marsom-blue) !important; color: white;
            padding: 15px; font-weight: 500; border: none; text-align: center;
        }
        .custom-table tbody td {
            padding: 15px; vertical-align: middle; text-align: center;
            border-bottom: 1px solid #eee; background: white;
        }
        .custom-table tbody tr:last-child td { border-bottom: none; }
        .custom-table tbody tr:hover td { background-color: #f9f9f9; }

        /* Status Badges */
        .badge-status { padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 500; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-progress { background: #cff4fc; color: #055160; }
        .status-completed { background: #d1e7dd; color: #0f5132; }
        .row-overdue td { background-color: #fff5f5 !important; }
        .badge-overdue { background: #dc3545; color: white; font-size: 0.75rem; padding: 4px 8px; border-radius: 4px; margin-top: 4px; display: inline-block;}
        
        /* Select2 Custom */
        .select2-container--bootstrap-5 .select2-selection { border-radius: 8px; padding: 5px; border-color: #ddd; }
    </style>
</head>
<body>

<div class="particles">
    <div class="particle" style="width:40px; height:40px; left:10%; top:20%; animation-delay:0s"></div>
    <div class="particle" style="width:70px; height:70px; left:80%; top:60%; animation-delay:2s"></div>
    <div class="particle" style="width:50px; height:50px; left:40%; top:40%; animation-delay:4s"></div>
    <div class="particle" style="width:60px; height:60px; left:20%; top:80%; animation-delay:6s"></div>
</div>

<div class="top-actions">
    <a href="javascript:history.back()" class="top-nav-button"><i class="fas fa-arrow-right"></i> رجوع</a>
    <a href="<?php echo site_url('users1/main_hr1'); ?>" class="top-nav-button"><i class="fas fa-home"></i> الرئيسية</a>
</div>

<div class="main-container">
    <h1 class="page-title">إدارة وتوزيع المهام</h1>

    <div class="glass-card">
        <div class="section-header">
            <i class="fas fa-plus-circle"></i>
            <h4>إسناد مهمة جديدة</h4>
        </div>
        
        <form id="createTaskForm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">الموظف المسؤول <span class="text-danger">*</span></label>
                    <select name="employee_id" id="employee_select" class="form-select" required>
                        <option value="">-- اختر موظف --</option>
                        <?php foreach($subordinates as $sub): ?>
                            <option value="<?= $sub['username'] ?>">
                                <?= $sub['name'] ?> (<?= $sub['username'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">عنوان المهمة <span class="text-danger">*</span></label>
                    <input type="text" name="task_title" class="form-control" placeholder="مثال: إعداد تقرير المبيعات الشهري" required>
                </div>
                
                <div class="col-md-12">
                    <label class="form-label">تفاصيل المهمة</label>
                    <textarea name="task_description" class="form-control" rows="3" placeholder="اكتب تفاصيل ومتطلبات المهمة هنا..."></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">تاريخ البدء</label>
                    <input type="date" name="start_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">تاريخ التسليم (Deadline) <span class="text-danger">*</span></label>
                    <input type="date" name="due_date" class="form-control" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> إرسال المهمة
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="glass-card" style="padding: 0; overflow: hidden;">
        <div class="section-header" style="margin: 20px 30px 10px;">
            <i class="fas fa-tasks"></i>
            <h4>سجل المهام المسندة</h4>
        </div>
        
        <div class="table-responsive">
            <table class="table custom-table mb-0">
                <thead>
                    <tr>
                        <th>الموظف</th>
                        <th>المهمة</th>
                        <th>تاريخ التسليم</th>
                        <th>الحالة</th>
                        <th>حالة الوقت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($my_created_tasks)): ?>
                        <tr><td colspan="5" class="text-muted py-4">لم تقم بإسناد أي مهام بعد.</td></tr>
                    <?php else: ?>
                        <?php foreach($my_created_tasks as $task): 
                            $is_overdue = ($task['status'] != 'completed' && $task['due_date'] < date('Y-m-d'));
                            $row_class = $is_overdue ? 'row-overdue' : '';
                            
                            $badges = [
                                'pending' => '<span class="badge-status status-pending"><i class="far fa-clock me-1"></i> قيد الانتظار</span>',
                                'in_progress' => '<span class="badge-status status-progress"><i class="fas fa-spinner fa-spin me-1"></i> جاري العمل</span>',
                                'completed' => '<span class="badge-status status-completed"><i class="fas fa-check me-1"></i> مكتملة</span>'
                            ];
                        ?>
                        <tr class="<?= $row_class ?>">
                            <td class="fw-bold text-primary"><?= $task['emp_name'] ?></td>
                            <td>
                                <div class="fw-bold"><?= $task['task_title'] ?></div>
                                <small class="text-muted"><?= mb_strimwidth($task['task_description'], 0, 50, '...') ?></small>
                            </td>
                            <td dir="ltr" class="fw-bold"><?= $task['due_date'] ?></td>
                            <td><?= $badges[$task['status']] ?></td>
                            <td>
                                <?php if($is_overdue): ?>
                                    <span class="badge-overdue"><i class="fas fa-exclamation-triangle"></i> متأخرة</span>
                                <?php elseif($task['status'] == 'completed'): ?>
                                    <i class="fas fa-check-circle text-success fa-lg"></i>
                                <?php else: ?>
                                    <span class="text-muted small">في الوقت</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('#employee_select').select2({
        theme: 'bootstrap-5',
        placeholder: '-- اختر موظف --',
        dir: 'rtl'
    });

    $('#createTaskForm').on('submit', function(e){
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('<?= $csrf_token_name ?>', '<?= $csrf_hash ?>');

        $.ajax({
            url: '<?= site_url('users1/ajax_create_task') ?>',
            type: 'POST',
            data: formData,
            processData: false, contentType: false, dataType: 'json',
            beforeSend: function() {
                Swal.fire({title: 'جاري الإرسال...', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
            },
            success: function(res){
                if(res.status == 'success'){
                    Swal.fire({
                        icon: 'success',
                        title: 'تم بنجاح',
                        text: res.message,
                        confirmButtonColor: '#001f3f'
                    }).then(() => location.reload());
                } else {
                    Swal.fire('خطأ', res.message, 'error');
                }
            },
            error: function() {
                Swal.fire('خطأ', 'حدث خطأ في الاتصال بالخادم', 'error');
            }
        });
    });
});
</script>
</body>
</html>