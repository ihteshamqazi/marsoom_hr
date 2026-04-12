<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>تفاصيل الطلب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;}
        body{font-family:'Tajawal',sans-serif;overflow-y:auto !important;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .main-container{padding:30px 15px;position:relative;z-index:1}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.6rem;color:var(--text-light);margin-bottom:24px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .page-title::after{content:'';position:absolute;width:160px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
        .details-card{background:rgba(255,255,255,.95);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.3);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);}
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        
        /* --- NEW DESIGN STYLES --- */
        .details-header {
            padding: 1.5rem;
            border-bottom: 1px solid #dee2e6;
            text-align: center;
        }
        .details-header .request-type {
            font-family: 'El Messiri', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--marsom-blue);
        }
        .details-header .employee-name {
            font-size: 1.1rem;
            color: #6c757d;
        }
        .details-header .status-badge {
            font-size: 1rem;
            font-weight: 700;
            padding: .6em 1.2em;
            margin-top: 1rem;
        }

        .details-body {
            padding: 1.5rem;
        }
        .details-section {
            margin-bottom: 2rem;
        }
        .details-section-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--marsom-blue);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--marsom-orange);
            display: inline-block;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .detail-item .label {
            font-weight: 700;
            color: #6c757d;
        }
        .detail-item .value {
            font-weight: 500;
            color: var(--text-dark);
            text-align: left;
        }
        .sidebar-card {
            background-color: rgba(0, 31, 63, 0.04);
            border-radius: 12px;
            padding: 1.25rem;
            height: 100%;
        }
    </style>
</head>
<body>

<div class="top-actions">
    <a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
    <a href="<?php echo site_url('users1/main_emp'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="main-container container-xl">
    <div class="text-center"><h1 class="page-title">تفاصيل الطلب</h1></div>

    <div class="details-card">
        <div class="details-header">
            <div class="request-type">
                <?php echo htmlspecialchars($request['order_name'] ?? 'طلب'); ?>
                <span class="text-muted fs-5">#<?php echo htmlspecialchars($request['id'] ?? '—'); ?></span>
            </div>
            <div class="employee-name">
                مقدم الطلب: <?php echo htmlspecialchars($request['subscriber_name'] ?? '—'); ?>
            </div>
            <div>
                <?php 
                    $st = (int)($request['status'] ?? 0);
                    if ($st === 2) { echo '<span class="badge rounded-pill bg-success status-badge">تمت الموافقة</span>'; } 
                    elseif ($st === 3 || $st === -1) { echo '<span class="badge rounded-pill bg-danger status-badge">مرفوض</span>'; } 
                    elseif ($st === 1) { echo '<span class="badge rounded-pill bg-info status-badge">بانتظار موافقة الموارد البشرية</span>'; } 
                    else { echo '<span class="badge rounded-pill bg-warning text-dark status-badge">بانتظار المعالجة</span>'; }
                ?>
            </div>
        </div>

        <div class="details-body">
            <div class="row">
                <div class="col-lg-8">

                    <?php 
// FIXED: Check using the request 'type' (5 is for Leave requests) which is 100% reliable
if ((int)($request['type'] ?? 0) === 5 || mb_strpos($request['order_name'] ?? '', 'إجازة') !== false): 
?>
                        <div class="details-section">
                            <h5 class="details-section-title">تفاصيل الإجازة</h5>
                            <div class="detail-item"><span class="label">نوع الإجازة</span> <span class="value"><?php echo htmlspecialchars($request['vac_main_type'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">تاريخ البداية</span> <span class="value"><?php echo htmlspecialchars($request['vac_start'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">تاريخ النهاية</span> <span class="value"><?php echo htmlspecialchars($request['vac_end'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label"> في تاريخ يوم نصف</span> <span class="value"><?php echo htmlspecialchars($request['vac_half_date'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">    الفترة</span> <span class="value"><?php echo htmlspecialchars($request['vac_half_period'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">عدد الأيام المحسوبة</span> <span class="value fw-bold text-primary"><?php echo htmlspecialchars($request['vac_days_count'] ?? '—'); ?> يوم</span></div>
                            <div class="detail-item"><span class="label">السبب</span> <span class="value"><?php echo htmlspecialchars($request['vac_reason'] ?? '—'); ?></span></div>
                            <?php if (isset($delegate_name) && $delegate_name): ?>
                            <div class="detail-item">
                                <span class="label">الموظف المفوض</span>
                                <span class="value fw-bold text-success">
                                    <i class="fa-solid fa-user-check me-1"></i>
                                    <?php echo htmlspecialchars($delegate_name); ?>
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (($request['order_name'] ?? '') === 'استقالة'): ?>
                        <div class="details-section">
                            <h5 class="details-section-title">تفاصيل الاستقالة</h5>
                            <div class="detail-item"><span class="label">تاريخ آخر يوم عمل</span> <span class="value"><?php echo htmlspecialchars($request['date_of_the_last_working'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">سبب الاستقالة</span> <span class="value"><?php echo htmlspecialchars($request['reason_for_resignation'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">تفاصيل إضافية</span> <span class="value" style="white-space: pre-wrap;"><?php echo htmlspecialchars($request['resignation_details'] ?? '—'); ?></span></div>
                        </div>
                    <?php endif; ?>
                    
                     <div class="employee-name">
                  اسم الشركة   : <?php echo htmlspecialchars($request['company_name'] ?? '—'); ?>
            </div>
                   <?php if (($request['order_name'] ?? '') === 'عمل إضافي'): ?>
     <div class="details-section">
        <h5 class="details-section-title">تفاصيل العمل الإضافي</h5>
        <div class="detail-item"><span class="label">النوع</span> <span class="value"><?php echo htmlspecialchars($request['ot_type'] ?? '—'); ?></span></div>
        
        <?php if (($request['ot_type'] ?? '') === 'single'): ?>
            <div class="detail-item"><span class="label">التاريخ</span> <span class="value"><?php echo htmlspecialchars($request['ot_date'] ?? '—'); ?></span></div>
        <?php else: ?>
            <div class="detail-item"><span class="label">من تاريخ</span> <span class="value"><?php echo htmlspecialchars($request['ot_from'] ?? '—'); ?></span></div>
            <div class="detail-item"><span class="label">إلى تاريخ</span> <span class="value"><?php echo htmlspecialchars($request['ot_to'] ?? '—'); ?></span></div>
        <?php endif; ?>
        
        <div class="detail-item"><span class="label">عدد الساعات</span> <span class="value fw-bold text-primary"><?php echo htmlspecialchars($request['ot_hours'] ?? '—'); ?></span></div>

        <?php if (!empty($ot_estimated_amount) && $ot_estimated_amount > 0): ?>
        <div class="detail-item bg-light p-2 rounded">
            <span class="label text-success"><i class="fa-solid fa-money-bill-wave"></i> المبلغ المتوقع (للمالية)</span> 
            <span class="value fw-bold text-success fs-5">
                <?php echo number_format($ot_estimated_amount, 2); ?> ريال
            </span>
        </div>
        <?php endif; ?>
        <div class="detail-item"><span class="label">هل مدفوع؟</span> <span class="value"><?php echo ($request['ot_paid'] ?? '0') == '1' ? 'نعم' : 'لا'; ?></span></div>
        <div class="detail-item"><span class="label">السبب</span> <span class="value"><?php echo htmlspecialchars($request['ot_reason'] ?? '—'); ?></span></div>
    </div>
<?php endif; ?>

                    <?php if (($request['order_name'] ?? '') === 'تصحيح بصمة'): ?>
                         <div class="details-section">
                            <h5 class="details-section-title">تفاصيل تصحيح البصمة</h5>
                            <div class="detail-item"><span class="label">تاريخ التصحيح</span> <span class="value"><?php echo htmlspecialchars($request['correction_date'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">وقت الحضور المصحح</span> <span class="value"><?php echo htmlspecialchars($request['attendance_correction'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">ملاحظة الحضور</span> <span class="value"><?php echo htmlspecialchars($request['note_on_entry'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">وقت الانصراف المصحح</span> <span class="value"><?php echo htmlspecialchars($request['correction_of_departure'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">ملاحظة الانصراف</span> <span class="value"><?php echo htmlspecialchars($request['note_on_checkout'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">سبب التصحيح</span> <span class="value"><?php echo htmlspecialchars($request['reason_for_correction'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">تفاصيل السبب</span> <span class="value"><?php echo htmlspecialchars($request['details_of_the_reason'] ?? '—'); ?></span></div>
                        </div>
                    <?php endif; ?>

                    <?php if (($request['order_name'] ?? '') === 'مصاريف مالية'): ?>
                         <div class="details-section">
                            <h5 class="details-section-title">تفاصيل المصاريف المالية</h5>
                            <div class="detail-item"><span class="label">السبب الرئيسي للطلب</span> <span class="value"><?php echo htmlspecialchars($request['exp_reason'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">اسم العنصر</span> <span class="value"><?php echo htmlspecialchars($request['exp_item_name'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">المبلغ</span> <span class="value fw-bold text-primary"><?php echo number_format((float)($request['exp_amount'] ?? 0), 2); ?> ريال</span></div>
                            <div class="detail-item"><span class="label">تاريخ الصرف</span> <span class="value"><?php echo htmlspecialchars($request['exp_date'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">الوصف</span> <span class="value"><?php echo htmlspecialchars($request['exp_desc'] ?? '—'); ?></span></div>
                        </div>
                    <?php endif; ?>

                    <?php if (($request['order_name'] ?? '') === 'طلب عُهدة'): ?>
                         <div class="details-section">
                            <h5 class="details-section-title">تفاصيل طلب العهدة</h5>
                            <div class="detail-item"><span class="label">نوع العهدة</span> <span class="value"><?php echo htmlspecialchars($request['asset_type'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">وصف مختصر</span> <span class="value"><?php echo htmlspecialchars($request['asset_desc'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">السبب</span> <span class="value"><?php echo htmlspecialchars($request['asset_reason'] ?? '—'); ?></span></div>
                        </div>
                    <?php endif; ?>
                    <?php if (($request['order_name'] ?? '') === 'الاستئذان' || (int)($request['type'] ?? 0) === 12): ?>
                        <div class="details-section">
                            <h5 class="details-section-title">تفاصيل الاستئذان</h5>
                            
                            <div class="detail-item">
                                <span class="label">تاريخ الاستئذان</span>
                                <span class="value"><?php echo htmlspecialchars($request['permission_date'] ?? '—'); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="label">من الساعة</span>
                                <span class="value text-success fw-bold"><?php echo htmlspecialchars($request['permission_start_time'] ?? '—'); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="label">إلى الساعة</span>
                                <span class="value text-danger fw-bold"><?php echo htmlspecialchars($request['permission_end_time'] ?? '—'); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="label">المدة الزمنية</span>
                                <span class="value fw-bold text-primary"><?php echo htmlspecialchars($request['permission_hours'] ?? '—'); ?> ساعة</span>
                            </div>
                            <div class="detail-item">
                                <span class="label">السبب</span>
                                <span class="value" style="white-space: pre-wrap;"><?php echo htmlspecialchars($request['note'] ?? '—'); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (($request['order_name'] ?? '') === 'طلب خطاب'): ?>
                         <div class="details-section">
                            <h5 class="details-section-title">تفاصيل طلب الخطاب</h5>
                            <div class="detail-item"><span class="label">نوع الخطاب</span> <span class="value"><?php echo htmlspecialchars($request['letter_type'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">موجه إلى (عربي)</span> <span class="value"><?php echo htmlspecialchars($request['letter_to_ar'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">موجه إلى (إنجليزي)</span> <span class="value"><?php echo htmlspecialchars($request['letter_to_en'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">السبب</span> <span class="value"><?php echo htmlspecialchars($request['letter_reason'] ?? '—'); ?></span></div>
                        </div>
                    <?php endif; ?>

                    <?php 
                    // Create variables to check condition easily
                    $current_type = (int)($request['type'] ?? 0);
                    $current_name = trim($request['order_name'] ?? '');
                    
                    // Check if Type is 9 OR Name is 'work_mission' OR Name is 'مهمة عمل'
                    if ($current_type == 9 || $current_name === 'work_mission' || $current_name === 'مهمة عمل'): 
                    ?>
                        <div class="details-section">
                            <h5 class="details-section-title">تفاصيل مهمة العمل</h5>
                            
                            <div class="detail-item">
                                <span class="label">نوع المهمة</span>
                                <span class="value"><?php echo htmlspecialchars($request['mission_type'] ?? '—'); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="label">تاريخ المهمة</span>
                                <span class="value"><?php echo htmlspecialchars($request['mission_date'] ?? '—'); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="label">وقت البداية</span>
                                <span class="value text-success fw-bold"><?php echo htmlspecialchars($request['mission_start_time'] ?? '—'); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="label">وقت النهاية</span>
                                <span class="value text-danger fw-bold"><?php echo htmlspecialchars($request['mission_end_time'] ?? '—'); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="label">تفاصيل/ملاحظات</span>
                                <span class="value"><?php echo htmlspecialchars($request['mission_note'] ?? '—'); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    </div>

                <div class="col-lg-4">
                    <div class="sidebar-card">
                         <div class="details-section">
                            <h5 class="details-section-title">معلومات الطلب</h5>
                            <?php if ($can_act_on_request): ?>
                            <div class="details-section" id="actions-container">
                                <h5 class="details-section-title">الإجراءات</h5>
                                <p class="text-muted mb-3">قم باتخاذ الإجراء المناسب لهذا الطلب.</p>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success btn-lg js-approve" data-id="<?php echo $request['id']; ?>">
                                        <i class="fa-solid fa-check me-2"></i> موافقة
                                    </button>
                                    <button class="btn btn-danger btn-lg js-reject" data-id="<?php echo $request['id']; ?>">
                                        <i class="fa-solid fa-xmark me-2"></i> رفض
                                    </button>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="detail-item"><span class="label">تاريخ التقديم</span> <span class="value"><?php echo htmlspecialchars($request['date'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">وقت التقديم</span> <span class="value"><?php echo htmlspecialchars($request['time'] ?? '—'); ?></span></div>
                            
                            <div class="card mt-4">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-file-alt text-primary"></i> إدارة الخطاب
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php 
                                    $this->load->model('hr_model');
                                    // Fetch saved letter details
                                    $saved_letter = $this->hr_model->get_generated_letter_by_order_id($request['id']);
                                    // Check if status is APPROVED (status == 2)
                                    $is_approved = ((int)($request['status'] ?? 0) === 2);
                                    ?>
                                    
                                    <?php if ($saved_letter): ?>
                                        <div class="alert alert-success d-flex align-items-center mb-3">
                                            <i class="fas fa-check-circle fa-2x me-3"></i>
                                            <div>
                                                <h6 class="alert-heading mb-1">تم إنشاء الخطاب مسبقاً</h6>
                                                <p class="mb-0">تم إنشاء الخطاب في: <?php echo date('Y-m-d H:i', strtotime($saved_letter['created_at'])); ?></p>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex gap-2 flex-wrap mb-4">
                                            <a href="<?php echo site_url('users1/view_saved_letter/' . $saved_letter['order_id']); ?>" 
                                               class="btn btn-primary" target="_blank">
                                                <i class="fas fa-print"></i> طباعة الخطاب الحالي
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!$saved_letter || !$is_approved): ?>

                                        <?php if ($saved_letter): ?>
                                            <hr class="my-3">
                                            <p class="text-muted small fw-bold"><i class="fas fa-info-circle"></i> يمكنك إنشاء نموذج مختلف أدناه (سيتم تحديث الخطاب المحفوظ):</p>
                                        <?php else: ?>
                                            <div class="alert alert-info d-flex align-items-center">
                                                <i class="fas fa-info-circle fa-2x me-3"></i>
                                                <div>
                                                    <h6 class="alert-heading mb-1">لم يتم إنشاء خطاب بعد</h6>
                                                    <p class="mb-0">يمكنك إنشاء خطاب جديد للموظف من الخيارات أدناه</p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($can_act_on_request) && $can_act_on_request): ?>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <a href="<?php echo site_url('users1/generate_letter/salary-certificate/' . $request['emp_id'] . '/' . $request['id']); ?>" 
                                                   class="btn btn-success" target="_blank">
                                                    <i class="fas fa-file-invoice-dollar"></i> خطاب إثبات مزايا
                                                </a>
                                                <a href="<?php echo site_url('users1/generate_letter/salary-commitment/' . $request['emp_id'] . '/' . $request['id']); ?>" 
                                                   class="btn btn-success" target="_blank">
                                                    <i class="fas fa-exchange-alt"></i> التزام تحويل راتب
                                                </a>
                                                <a href="<?php echo site_url('users1/generate_letter/salary-commitment-marsoom/' . $request['emp_id'] . '/' . $request['id']); ?>" 
                                                   class="btn btn-success" target="_blank">
                                                    <i class="fas fa-file-signature"></i> التزام تحويل راتب (مرسوم)
                                                </a>
                                                <a href="<?php echo site_url('users1/generate_letter/embassy-letter/' . $request['emp_id'] . '/' . $request['id']); ?>" 
                                                   class="btn btn-success" target="_blank">
                                                    <i class="fas fa-passport"></i> خطاب للسفارة
                                                </a>
                                                <a href="<?php echo site_url('users1/generate_letter/eos-certificate/' . $request['emp_id'] . '/' . $request['id']); ?>" 
                                                   class="btn btn-success" target="_blank">
                                                    <i class="fas fa-user-check"></i> إفادة نهاية الخدمة
                                                </a>
                                                
                                                <a href="<?php echo site_url('users1/generate_letter/marsoom-definition/' . $request['emp_id'] . '/' . $request['id']); ?>" 
                                                   class="btn btn-primary" target="_blank">
                                                    <i class="fas fa-file-contract"></i> خطاب تعريف (مرسوم)
                                                </a>
                                                <a href="<?php echo site_url('users1/generate_letter/office-definition/' . $request['emp_id'] . '/' . $request['id']); ?>" 
                                                   class="btn btn-primary" target="_blank">
                                                    <i class="fas fa-briefcase"></i> خطاب تعريف (المكتب)
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i> 
                                                لا يمكنك إدارة الخطابات لهذا الطلب
                                            </div>
                                        <?php endif; ?>

                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="detail-item"><span class="label">المرفق</span> <span class="value">
                                <?php if (!empty($request['file'])): ?>
                                    <a href="<?php echo base_url(htmlspecialchars($request['file'])); ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa fa-eye me-1"></i> عرض</a>
                                <?php else: ?>
                                    لا يوجد
                                <?php endif; ?>
                            </span></div>
                            <?php if (!empty($request['reason_for_rejection'])): ?>
                                <div class="detail-item flex-column align-items-start">
                                    <span class="label text-danger">حالة</span>
                                    <span class="value mt-1 p-2 bg-danger-subtle rounded w-100"><?php echo htmlspecialchars($request['reason_for_rejection']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true" dir="rtl">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fa-solid fa-circle-xmark ms-1"></i> سبب الرفض</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">رجاءً اكتب سبب الرفض (3 أحرف على الأقل):</div>
                <textarea id="rejectReason" class="form-control" rows="3" placeholder="مثال: البيانات غير مكتملة"></textarea>
                <div class="invalid-feedback d-block d-none" id="rejectReasonError">اكتب سببًا لا يقل عن 3 أحرف.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-danger" id="confirmRejectBtn">تأكيد الرفض</button>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Check if the actions container exists on the page
    if (!$('#actions-container').length) {
        return; // Exit if no actions are possible
    }

    const UPDATE_URL = '<?= site_url("users1/update_order_status"); ?>';
    let CSRF_NAME = '<?= $this->security->get_csrf_token_name(); ?>';
    let CSRF_HASH = '<?= $this->security->get_csrf_hash(); ?>';

    const rejectionModal = new bootstrap.Modal(document.getElementById('rejectModal'));
    let requestIdToReject = null;

    function sendUpdateRequest(id, status, reason = null) {
        const payload = {
            id: id,
            status: status,
            [CSRF_NAME]: CSRF_HASH
        };
        if (reason) {
            payload.reason = reason;
        }

        $.ajax({
            url: UPDATE_URL,
            type: 'POST',
            dataType: 'json',
            data: payload,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                // Update CSRF token for the next request
                CSRF_HASH = response.csrfHash;

                if (response.ok) {
                    // Update UI on success
                    $('#actions-container').html('<div class="alert alert-success">تم اتخاذ الإجراء بنجاح.</div>');
                    const statusBadgeContainer = $('.details-header > div:last-child');
                    if (status === 2) {
                        statusBadgeContainer.html('<span class="badge rounded-pill bg-success status-badge">تمت الموافقة</span>');
                    } else {
                        statusBadgeContainer.html('<span class="badge rounded-pill bg-danger status-badge">مرفوض</span>');
                        // Optionally show the rejection reason on the page
                    }
                } else {
                    alert('فشل الإجراء: ' + (response.error || 'خطأ غير معروف'));
                }
            },
            error: function() {
                alert('خطأ في الاتصال بالخادم. الرجاء المحاولة مرة أخرى.');
            }
        });
    }

    // --- Event Listeners ---
    $('.js-approve').on('click', function() {
        const requestId = $(this).data('id');
        if (confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')) {
            sendUpdateRequest(requestId, 2);
        }
    });

    $('.js-reject').on('click', function() {
        requestIdToReject = $(this).data('id');
        $('#rejectReason').val('');
        $('#rejectReasonError').addClass('d-none');
        rejectionModal.show();
    });

    $('#confirmRejectBtn').on('click', function() {
        const reason = $('#rejectReason').val().trim();
        if (reason.length < 3) {
            $('#rejectReasonError').removeClass('d-none');
            return;
        }
        sendUpdateRequest(requestIdToReject, 3, reason);
        rejectionModal.hide();
    });
});
</script>
</body>
</html>