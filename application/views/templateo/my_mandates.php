<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سجل الانتدابات المفصل</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
        :root { --marsom-blue: #001f3f; --marsom-orange: #FF8C00; }
        body { font-family: 'Tajawal'; background: #f4f7fa; font-size: 0.9rem; }
        
        .main-container { max-width: 1400px; margin: 40px auto; padding: 0 15px; }
        
        .dashboard-card {
            background: white; border-radius: 16px; border: none;
            box-shadow: 0 5px 25px rgba(0,0,0,0.05); overflow: hidden;
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, var(--marsom-blue), #1a3c5e);
            color: white; padding: 20px 30px; border-bottom: 4px solid var(--marsom-orange);
            display: flex; justify-content: space-between; align-items: center;
        }

        /* --- New Filter Styles --- */
        .filter-area {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 20px 30px;
        }
        .filter-label {
            font-weight: 700; color: var(--marsom-blue); font-size: 0.8rem; margin-bottom: 5px; display: block;
        }
        .form-control-custom {
            border-radius: 8px; border: 1px solid #dee2e6; padding: 8px 12px; font-size: 0.9rem;
        }
        .form-control-custom:focus {
            border-color: var(--marsom-orange); box-shadow: 0 0 0 0.2rem rgba(255, 140, 0, 0.15);
        }
        /* ------------------------- */

        .table-custom thead th {
            background: #f1f3f5; color: #495057; font-weight: 700; 
            border-bottom: 2px solid #dee2e6; padding: 15px; font-size: 0.85rem; text-transform: uppercase;
        }
        .table-custom tbody tr { transition: 0.2s; border-bottom: 1px solid #f1f1f1; }
        .table-custom tbody tr:hover { background-color: #fafafa; }
        .table-custom td { padding: 15px; vertical-align: middle; }

        .emp-info { display: flex; flex-direction: column; line-height: 1.3; }
        .emp-name { font-weight: bold; color: #000; font-size: 0.95rem; }
        .emp-meta { font-size: 0.8rem; color: #6c757d; }
        
        .route-badge { 
            background: #e9ecef; color: #333; padding: 5px 10px; 
            border-radius: 6px; font-size: 0.8rem; display: inline-block; margin-bottom: 3px;
            border: 1px solid #dee2e6;
        }
        .route-arrow { color: var(--marsom-orange); margin: 0 5px; }

        .status-pill { padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; }
        .st-approved { background: #d1e7dd; color: #0f5132; }
        .st-pending { background: #fff3cd; color: #856404; }
        .st-rejected { background: #f8d7da; color: #842029; }

        .btn-view {
            background: white; border: 1px solid #ddd; color: var(--marsom-blue);
            width: 35px; height: 35px; border-radius: 8px; display: inline-flex;
            align-items: center; justify-content: center; transition: 0.2s;
        }
        .btn-view:hover { background: var(--marsom-blue); color: white; border-color: var(--marsom-blue); }
    </style>
</head>
<body>

<div class="main-container">
    <div class="dashboard-card">
        
        <div class="card-header-custom">
            <div>
                <h4 class="m-0 fw-bold"><i class="fas fa-list-alt me-2"></i> سجل الانتدابات المفصل</h4>
            </div>
            <a href="<?= site_url('users1/mandate_request') ?>" class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm">
                <i class="fas fa-plus me-2"></i> طلب جديد
            </a>
        </div>

        <div class="filter-area">
<form method="GET" action="">
                <div class="row g-3 align-items-end">
                    
                    <div class="col-md-2 col-6">
                        <label class="filter-label">الرقم الوظيفي</label>
                        <input type="text" name="emp_code" class="form-control form-control-custom" placeholder="102.." value="<?= isset($_GET['emp_code']) ? $_GET['emp_code'] : '' ?>">
                    </div>

                    <div class="col-md-2 col-6">
                        <label class="filter-label">اسم الموظف</label>
                        <input type="text" name="emp_name" class="form-control form-control-custom" placeholder="بحث..." value="<?= isset($_GET['emp_name']) ? $_GET['emp_name'] : '' ?>">
                    </div>

                    <div class="col-md-2 col-6">
                        <label class="filter-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control form-control-custom" value="<?= isset($_GET['date_from']) ? $_GET['date_from'] : '' ?>">
                    </div>

                    <div class="col-md-2 col-6">
                        <label class="filter-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control form-control-custom" value="<?= isset($_GET['date_to']) ? $_GET['date_to'] : '' ?>">
                    </div>

                    <div class="col-md-1 col-6">
                        <label class="filter-label">الحالة</label>
                        <select name="status" class="form-select form-control-custom px-1">
                            <option value="">الكل</option>
                            <option value="Pending" <?= (isset($_GET['status']) && $_GET['status'] == 'Pending') ? 'selected' : '' ?>>قيد الإجراء</option>
                            <option value="Approved" <?= (isset($_GET['status']) && $_GET['status'] == 'Approved') ? 'selected' : '' ?>>معتمد</option>
                            <option value="Rejected" <?= (isset($_GET['status']) && $_GET['status'] == 'Rejected') ? 'selected' : '' ?>>مرفوض</option>
                        </select>
                    </div>

                    <div class="col-md-2 col-6">
                        <label class="filter-label">الاعتماد الحالي</label>
                        <select name="approver_role" class="form-select form-control-custom">
                            <option value="">الكل</option>
                            <option value="manager" <?= (isset($_GET['approver_role']) && $_GET['approver_role'] == 'manager') ? 'selected' : '' ?>>المدير المباشر</option>
                            <option value="hr" <?= (isset($_GET['approver_role']) && $_GET['approver_role'] == 'hr') ? 'selected' : '' ?>>الموارد البشرية</option>
                            <option value="finance" <?= (isset($_GET['approver_role']) && $_GET['approver_role'] == 'finance') ? 'selected' : '' ?>>المالية</option>
                        </select>
                    </div>

                    <div class="col-md-1 col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100" style="background: var(--marsom-blue); border:none;"><i class="fas fa-search"></i></button>
                            <a href="<?= current_url() ?>" class="btn btn-outline-secondary" title="إلغاء الفلاتر"><i class="fas fa-undo"></i></a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-custom mb-0 text-center">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <?php if($is_hr): ?>
                            <th width="15%" class="text-start">الموظف / القسم</th>
                        <?php else: ?>
                            <th width="10%">القسم</th>
                        <?php endif; ?>
                        <th width="12%">التاريخ والمدة</th>
                        <th width="20%">خط السير (From -> To)</th>
                        <th width="8%">المسافة</th>
                        <th width="20%">الأهداف (Goals)</th>
                        <th width="10%">المبلغ</th>
                        <th width="10%">الحالة</th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($requests)): ?>
                        <tr><td colspan="9" class="text-muted py-5">لا توجد بيانات متاحة.</td></tr>
                    <?php else: ?>
                        <?php foreach($requests as $r): ?>
                        <tr>
                            <td class="fw-bold text-muted">#<?= $r['id'] ?></td>
                            
                            <?php if($is_hr): ?>
                                <td class="text-start">
                                    <div class="emp-info">
                                        <span class="emp-name"><?= $r['emp_name'] ?></span>
                                        <span class="emp-meta">
                                            <i class="fas fa-id-card"></i> <?= $r['emp_code'] ?> 
                                            <span class="mx-1">|</span> 
                                            <i class="fas fa-building"></i> <?= $r['department'] ?>
                                        </span>
                                    </div>
                                </td>
                            <?php else: ?>
                                <td><span class="badge bg-light text-dark border"><?= $r['department'] ?></span></td>
                            <?php endif; ?>

                            <td>
                                <div class="fw-bold text-dark"><?= $r['start_date'] ?></div>
                                <div class="small text-muted"><?= $r['duration_days'] ?> أيام</div>
                            </td>

                            <td class="text-start">
                                <?php 
                                    if(!empty($r['itinerary'])) {
                                        $routes = explode('|', $r['itinerary']);
                                        foreach($routes as $route) {
                                            echo '<div class="route-badge">' . str_replace('➝', '<i class="fas fa-arrow-left route-arrow"></i>', $route) . '</div>';
                                        }
                                    } else {
                                        echo '<span class="text-muted">-</span>';
                                    }
                                ?>
                            </td>

                            <td>
                                <span class="fw-bold"><?= $r['road_total_km'] ?></span> <small>km</small>
                                <div class="small text-muted">
                                    <?= $r['transport_mode'] == 'air' ? '<i class="fas fa-plane"></i>' : '<i class="fas fa-car"></i>' ?>
                                </div>
                            </td>

                            <td class="text-start small text-muted">
                                <?= mb_strimwidth($r['goals_summary'], 0, 50, '...') ?>
                            </td>

                            <td>
                                <div class="fw-bold text-success"><?= number_format($r['total_amount'], 0) ?></div>
                                <small class="text-muted">SAR</small>
                            </td>

                            <td>
                                <?php 
                                    if($r['status'] == 'Approved') echo '<span class="status-pill st-approved">معتمد</span>';
                                    elseif($r['status'] == 'Rejected') echo '<span class="status-pill st-rejected">مرفوض</span>';
                                    else echo '<span class="status-pill st-pending">قيد الإجراء</span>';
                                ?>
                            </td>

                            <td>
                                <a href="<?= site_url('users1/mandate_details/'.$r['id']) ?>" class="btn-view" title="عرض التفاصيل الكاملة">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>