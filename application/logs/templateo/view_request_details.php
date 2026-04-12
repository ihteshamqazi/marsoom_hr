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
    <a href="<?php echo site_url('dashboard'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
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

                    <?php if (($request['order_name'] ?? '') === 'إجازة'): ?>
                        <div class="details-section">
                            <h5 class="details-section-title">تفاصيل الإجازة</h5>
                            <div class="detail-item"><span class="label">نوع الإجازة</span> <span class="value"><?php echo htmlspecialchars($request['vac_main_type'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">تاريخ البداية</span> <span class="value"><?php echo htmlspecialchars($request['vac_start'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">تاريخ النهاية</span> <span class="value"><?php echo htmlspecialchars($request['vac_end'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label"> في تاريخ يوم نصف</span> <span class="value"><?php echo htmlspecialchars($request['vac_half_date'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">    الفترة</span> <span class="value"><?php echo htmlspecialchars($request['vac_half_period'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">عدد الأيام المحسوبة</span> <span class="value fw-bold text-primary"><?php echo htmlspecialchars($request['vac_days_count'] ?? '—'); ?> يوم</span></div>
                            <div class="detail-item"><span class="label">السبب</span> <span class="value"><?php echo htmlspecialchars($request['vac_reason'] ?? '—'); ?></span></div>
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

                    <?php if (($request['order_name'] ?? '') === 'طلب خطاب'): ?>
                         <div class="details-section">
                            <h5 class="details-section-title">تفاصيل طلب الخطاب</h5>
                            <div class="detail-item"><span class="label">نوع الخطاب</span> <span class="value"><?php echo htmlspecialchars($request['letter_type'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">موجه إلى (عربي)</span> <span class="value"><?php echo htmlspecialchars($request['letter_to_ar'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">موجه إلى (إنجليزي)</span> <span class="value"><?php echo htmlspecialchars($request['letter_to_en'] ?? '—'); ?></span></div>
                            <div class="detail-item"><span class="label">السبب</span> <span class="value"><?php echo htmlspecialchars($request['letter_reason'] ?? '—'); ?></span></div>
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
                             <div class="detail-item"><span class="label">المرفق</span> <span class="value">
                                <?php if (!empty($request['file'])): ?>
                                    <a href="<?php echo base_url(htmlspecialchars($request['file'])); ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa fa-eye me-1"></i> عرض</a>
                                <?php else: ?>
                                    لا يوجد
                                <?php endif; ?>
                            </span></div>
                            <?php if (!empty($request['reason_for_rejection'])): ?>
                                <div class="detail-item flex-column align-items-start">
                                    <span class="label text-danger">سبب الرفض</span>
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