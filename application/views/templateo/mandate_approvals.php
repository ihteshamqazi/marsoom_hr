<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلبات الانتداب | My Approvals</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
        :root {
            --primary: #001f3f;
            --secondary: #6c757d;
            --accent: #FF8C00;
            --bg-light: #f4f6f9;
            --success: #198754;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #0dcaf0;
            --returned: #e74c3c;
            --text-dark: #2c3e50;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: var(--bg-light);
            color: var(--text-dark);
            padding-bottom: 50px;
        }

        /* Hero Section */
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, #1e3c72 100%);
            color: white;
            padding: 40px 0 80px;
            margin-bottom: -40px;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        /* Main Container */
        .main-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Card Styles */
        .glass-panel {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
            border: none;
        }

        /* Tabs Customization */
        .nav-tabs {
            border-bottom: none;
            gap: 10px;
            padding-left: 20px;
            padding-right: 20px;
        }

        .nav-tabs .nav-link {
            border: none;
            background: rgba(255, 255, 255, 0.8);
            color: var(--text-dark);
            border-radius: 12px 12px 0 0;
            padding: 12px 25px;
            font-weight: 700;
            transition: 0.3s;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.02);
        }

        .nav-tabs .nav-link.active {
            background: white;
            color: var(--primary);
            border-top: 3px solid var(--accent);
            box-shadow: 0 -5px 15px rgba(0,0,0,0.05);
        }

        .nav-tabs .nav-link:hover:not(.active) {
            background: #e9ecef;
            transform: translateY(-2px);
        }

        /* Table Styles */
        .table-custom th {
            background: #f8f9fa;
            color: var(--secondary);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            border-bottom: 2px solid #eee;
            padding: 15px;
        }
        
        .table-custom td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
        }

        .table-custom tr:last-child td {
            border-bottom: none;
        }

        .table-custom tr:hover {
            background-color: #fafbff;
        }

        /* Badges */
        .badge-status {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-approved { background: #d1e7dd; color: #0f5132; }
        .badge-rejected { background: #f8d7da; color: #842029; }
        .badge-returned { background: #ffeaa7; color: #d63031; border: 1px dashed #d63031; }

        /* Returned Row Highlight */
        .returned-row {
            background: #fff9e6 !important;
            border-left: 4px solid #ffc107;
        }
        
        .returned-row:hover {
            background: #fff4d1 !important;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-icon {
            font-size: 4rem;
            color: #e0e0e0;
            margin-bottom: 20px;
        }

        /* Action Buttons */
        .btn-view {
            background: var(--primary);
            color: white;
            border-radius: 50px;
            padding: 6px 20px;
            font-size: 0.9rem;
            transition: 0.2s;
        }
        .btn-view:hover {
            background: var(--accent);
            color: white;
            transform: translateX(-3px);
        }
        
        .btn-history {
            background: white;
            border: 1px solid #dee2e6;
            color: var(--text-dark);
            border-radius: 50px;
            padding: 6px 20px;
        }
        .btn-history:hover {
            background: #f8f9fa;
            border-color: var(--text-dark);
        }

        /* Returned Label */
        .returned-label {
            background: linear-gradient(135deg, #ffd166 0%, #ffc107 100%);
            color: #000;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            margin-right: 5px;
        }

        /* Return Info Badge */
        .return-info {
            background: #fef9e7;
            border: 1px solid #f7dc6f;
            color: #7d6608;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-top: 3px;
            display: inline-block;
        }
    </style>
</head>
<body>

<div class="page-header">
    <div class="main-container text-center">
        <h2 class="fw-bold mb-2"><i class="fas fa-tasks me-2"></i> طلبات الانتداب</h2>
        <p class="opacity-75">إدارة ومتابعة طلبات الموظفين (My Approvals)</p>
    </div>
</div>

<div class="main-container">

    <ul class="nav nav-tabs" id="approvalTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                <i class="fas fa-clock text-warning me-2"></i> قيد الإجراء 
                <span class="badge bg-warning text-dark ms-1 rounded-pill"><?= count($pending) ?></span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="returned-tab" data-bs-toggle="tab" data-bs-target="#returned" type="button" role="tab">
                <i class="fas fa-undo-alt text-danger me-2"></i> الطلبات المرتجعة
                <span class="badge bg-danger ms-1 rounded-pill"><?= count($returned) ?></span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                <i class="fas fa-history text-secondary me-2"></i> السجل السابق
                <span class="badge bg-secondary ms-1 rounded-pill"><?= count($history) ?></span>
            </button>
        </li>
    </ul>

    <div class="glass-panel">
        <div class="tab-content" id="approvalTabsContent">
            
            <div class="tab-pane fade show active" id="pending" role="tabpanel">
                <?php if(empty($pending)): ?>
                    <div class="empty-state">
                        <i class="fas fa-check-circle empty-icon text-success opacity-25"></i>
                        <h5 class="text-muted fw-bold">لا توجد طلبات معلقة</h5>
                        <p class="text-muted small">جميع الطلبات تم اتخاذ إجراء بشأنها.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>الموظف</th>
                                    <th>تاريخ التقديم</th>
                                    <th>المدة / المبلغ</th>
                                    <th>الحالة</th>
                                    <th>الإجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($pending as $row): ?>
                                <tr>
                                    <td><span class="fw-bold text-primary">#<?= $row['req_id'] ?></span></td>
                                    <td>
                                        <div class="fw-bold"><?= $row['subscriber_name'] ?></div>
                                        <small class="text-muted"><?= $row['department'] ?></small>
                                    </td>
                                    <td>
                                        <div><?= $row['request_date'] ?></div>
                                        <small class="text-muted">يبدأ: <?= $row['start_date'] ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= $row['duration_days'] ?> أيام</div>
                                        <small class="text-success fw-bold"><?= number_format($row['total_amount']) ?> SAR</small>
                                    </td>
                                    <td><span class="badge badge-status badge-pending">بانتظار موافقتك</span></td>
                                    <td>
                                        <a href="<?= site_url('users1/mandate_details/'.$row['req_id']) ?>" class="btn btn-view shadow-sm">
                                            <i class="fas fa-eye me-1"></i> معاينة
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="returned" role="tabpanel">
                <?php if(empty($returned)): ?>
                    <div class="empty-state">
                        <i class="fas fa-undo-alt empty-icon text-danger opacity-25"></i>
                        <h5 class="text-muted fw-bold">لا توجد طلبات مرتجعة</h5>
                        <p class="text-muted small">لم يتم إرجاع أي طلبات إليك.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>الموظف</th>
                                    <th>تاريخ الإرجاع</th>
                                    <th>سبب الإرجاع</th>
                                    <th>مرسل من</th>
                                    <th>الحالة</th>
                                    <th>الإجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($returned as $row): ?>
                                <tr class="returned-row">
                                    <td>
                                        <span class="fw-bold text-primary">#<?= $row['req_id'] ?></span>
                                        <span class="returned-label">مرتجع</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= $row['subscriber_name'] ?></div>
                                        <small class="text-muted"><?= $row['department'] ?></small>
                                    </td>
                                    <td>
                                        <div><?= date('Y-m-d', strtotime($row['return_date'])) ?></div>
                                        <small class="text-muted"><?= date('H:i', strtotime($row['return_date'])) ?></small>
                                    </td>
                                    <td>
                                        <div class="text-danger small" style="max-width: 200px;">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            <?= !empty($row['rejection_reason']) ? $row['rejection_reason'] : 'بدون سبب' ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= $row['rejected_by_name'] ?></div>
                                        <small class="text-muted"><?= $row['rejected_by_department'] ?></small>
                                        <div class="return-info">
                                            <i class="fas fa-user-clock me-1"></i> مرجع بتاريخ <?= date('Y-m-d', strtotime($row['return_date'])) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-status badge-returned">
                                            <i class="fas fa-undo me-1"></i> مرتجع للمراجعة
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('users1/mandate_details/'.$row['req_id']) ?>" class="btn btn-view shadow-sm">
                                            <i class="fas fa-redo me-1"></i> إعادة معاينة
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="history" role="tabpanel">
                <?php if(empty($history)): ?>
                    <div class="empty-state">
                        <i class="fas fa-folder-open empty-icon"></i>
                        <h5 class="text-muted fw-bold">السجل فارغ</h5>
                        <p class="text-muted small">لم تقم بأي عمليات موافقة أو رفض بعد.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>الموظف</th>
                                    <th>تاريخ إجرائك</th>
                                    <th>قرارك</th>
                                    <th>ملاحظاتك</th>
                                    <th>التفاصيل</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($history as $row): ?>
                                <tr>
                                    <td class="text-muted fw-bold">#<?= $row['req_id'] ?></td>
                                    <td><?= $row['subscriber_name'] ?></td>
                                    <td><?= date('Y-m-d', strtotime($row['action_date'])) ?> <small class="text-muted"><?= date('H:i', strtotime($row['action_date'])) ?></small></td>
                                    <td>
                                        <?php if($row['status'] == 'Approved'): ?>
                                            <span class="badge badge-status badge-approved"><i class="fas fa-check me-1"></i> موافقة</span>
                                        <?php else: ?>
                                            <span class="badge badge-status badge-rejected"><i class="fas fa-times me-1"></i> رفض</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-muted" style="max-width: 200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                        <?= !empty($row['notes']) ? $row['notes'] : '<span class="opacity-25">-</span>' ?>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('users1/mandate_details/'.$row['req_id']) ?>" class="btn btn-history btn-sm">
                                            <i class="fas fa-search"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Tab activation based on URL parameter
    $(document).ready(function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        
        if (tab) {
            $('#' + tab + '-tab').tab('show');
        }
        
        // Update URL when tab changes
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const tabId = $(e.target).attr('id').replace('-tab', '');
            const newUrl = window.location.pathname + '?tab=' + tabId;
            window.history.replaceState(null, null, newUrl);
        });
    });
</script>

</body>
</html>