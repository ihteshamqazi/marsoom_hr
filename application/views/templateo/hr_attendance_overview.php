<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= html_escape($page_title ?? 'نظرة عامة على الحضور') ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <style>
        :root {
            --font-main: 'Tajawal', sans-serif;
            --primary-gradient: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            --bg-body: #f3f4f6;
            --bg-card: #ffffff;
            --text-dark: #111827;
            --text-light: #6b7280;
            --border-color: #e5e7eb;
            
            /* Status Colors (High Contrast) */
            --st-present-bg: #dcfce7; --st-present-text: #14532d;
            --st-late-bg: #ffedd5;    --st-late-text: #7c2d12;
            --st-absent-bg: #fee2e2;  --st-absent-text: #991b1b;
            --st-leave-bg: #dbeafe;   --st-leave-text: #1e3a8a;
            --st-weekend-bg: #f9fafb; --st-weekend-text: #9ca3af;
            --st-incomplete-bg: #e5e7eb; --st-incomplete-text: #374151;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg-body);
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        /* Header */
        .page-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem 0 4rem;
            margin-bottom: -3rem;
            border-radius: 0 0 2rem 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .page-title { font-weight: 800; font-size: 1.8rem; margin: 0; }

        /* Filters */
        .filter-container {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .form-label { font-weight: 700; font-size: 0.85rem; color: #374151; margin-bottom: 0.4rem; }
        .form-control, .form-select {
            padding: 0.6rem 1rem;
            border-radius: 0.5rem;
            border-color: #d1d5db;
            font-size: 0.9rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }

        /* KPI Cards */
        .kpi-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .kpi-card {
            background: white;
            padding: 1.25rem;
            border-radius: 1rem;
            border-right: 5px solid transparent;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            transition: all 0.2s;
        }
        .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .kpi-card.active { background-color: #eff6ff; border-right-color: #3b82f6; }
        
        .kpi-val { font-size: 2rem; font-weight: 800; line-height: 1; }
        .kpi-lbl { font-size: 0.85rem; font-weight: 600; color: var(--text-light); }
        .kpi-icon { 
            width: 45px; height: 45px; 
            border-radius: 10px; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 1.4rem; 
        }

        /* Specific KPI Styles */
        .kpi-late { border-right-color: #f59e0b; } .kpi-late .kpi-icon { background: #fff7ed; color: #f59e0b; } .kpi-late .kpi-val { color: #f59e0b; }
        .kpi-absent { border-right-color: #ef4444; } .kpi-absent .kpi-icon { background: #fef2f2; color: #ef4444; } .kpi-absent .kpi-val { color: #ef4444; }
        .kpi-leave { border-right-color: #3b82f6; } .kpi-leave .kpi-icon { background: #eff6ff; color: #3b82f6; } .kpi-leave .kpi-val { color: #3b82f6; }
        .kpi-incomplete { border-right-color: #6b7280; } .kpi-incomplete .kpi-icon { background: #f3f4f6; color: #6b7280; } .kpi-incomplete .kpi-val { color: #6b7280; }
        .kpi-req { border-right-color: #06b6d4; } .kpi-req .kpi-icon { background: #ecfeff; color: #06b6d4; } .kpi-req .kpi-val { color: #06b6d4; }

        /* Matrix Table */
        .table-container {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            overflow: hidden;
            padding: 0;
        }
        .table-responsive { max-height: 75vh; overflow: auto; }
        .custom-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 0.85rem; }
        
        .custom-table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            padding: 1rem 0.5rem;
            text-align: center;
            border-bottom: 2px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 10;
            white-space: nowrap;
        }

        /* Sticky First Column */
        .custom-table th:first-child, .custom-table td:first-child {
            position: sticky;
            right: 0;
            background-color: white;
            z-index: 20;
            border-left: 2px solid #e2e8f0;
            min-width: 240px;
            max-width: 240px;
        }
        .custom-table thead th:first-child { z-index: 30; background-color: #f8fafc; }

        .custom-table td {
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #f1f5f9;
            padding: 0;
            vertical-align: middle;
            height: 60px;
        }

        .employee-info { padding: 0.75rem 1rem; display: flex; flex-direction: column; justify-content: center; }
        .emp-name { font-weight: 800; font-size: 0.95rem; color: #1e293b; margin-bottom: 2px; }
        .emp-meta { font-size: 0.75rem; color: #64748b; font-weight: 500; }

        .cell-content {
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            width: 100%; height: 100%; padding: 0.25rem;
            font-weight: 700; font-size: 0.75rem;
            transition: filter 0.2s;
        }
        .cell-content:hover { filter: brightness(0.95); }

        /* Cell Colors */
        .st-present { background-color: var(--st-present-bg); color: var(--st-present-text); }
        .st-late { background-color: var(--st-late-bg); color: var(--st-late-text); border-bottom: 3px solid #f59e0b; }
        .st-absent { background-color: var(--st-absent-bg); color: var(--st-absent-text); font-size: 0.9rem; }
        .st-leave { background-color: var(--st-leave-bg); color: var(--st-leave-text); }
        .st-weekend { background-color: var(--st-weekend-bg); color: var(--st-weekend-text); background-image: radial-gradient(#e5e7eb 1px, transparent 1px); background-size: 8px 8px; }
        .st-incomplete { background-color: var(--st-incomplete-bg); color: var(--st-incomplete-text); border: 1px dashed #9ca3af; }

        .sum-col { background-color: #f8fafc !important; font-weight: 800; color: #334155; font-size: 0.9rem; text-align: center; }
        
        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 5px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body>

<div class="page-header">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">📋 نظرة عامة على الحضور</h1>
                <p class="mb-0 opacity-75">متابعة حضور وانصراف الموظفين وتحليل المخالفات</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= site_url('users1/main_hr1'); ?>" class="btn btn-outline-light fw-bold">
                    <i class="fas fa-arrow-right me-2"></i> رجوع
                </a>
                <?php
                    $export_params = http_build_query([
                        'start_date' => $filters['start_date'],
                        'end_date' => $filters['end_date'],
                        'employee_id' => $filters['employee_id'],
                        'department' => $filters['department'],
                        'profession' => $filters['profession'] ?? '',
                        'company' => $filters['company'] ?? '',
                        'location' => $filters['location'] ?? '',
                        'device' => $filters['device'] ?? '',
                        'job_type' => $filters['job_type'] ?? ''
                    ]);
                ?>
                <a href="<?= site_url('users1/export_attendance_overview?' . $export_params) ?>" class="btn btn-light text-primary fw-bold">
                    <i class="fas fa-file-excel me-2"></i> تصدير Excel
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4" style="margin-top: 3.5rem;">
    
    <div class="filter-container">
        <form method="GET" action="<?= site_url('users1/attendance_overview') ?>">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">📅 الفترة الزمنية</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="far fa-calendar-alt text-primary"></i></span>
                        <input type="text" id="date_range" name="date_range" class="form-control border-start-0 ps-0">
                    </div>
                    <input type="hidden" name="start_date" id="start_date" value="<?= html_escape($filters['start_date']) ?>">
                    <input type="hidden" name="end_date" id="end_date" value="<?= html_escape($filters['end_date']) ?>">
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">🔍 بحث (اسم / رقم)</label>
                    <input type="text" name="employee_id" class="form-control" value="<?= html_escape($filters['employee_id']) ?>" placeholder="ابحث عن موظف...">
                </div>

                <div class="col-lg-2 col-md-4">
                    <label class="form-label">الإدارة</label>
                    <select name="department" class="form-select">
                        <option value="">الكل</option>
                        <?php foreach($departments as $dept): ?>
                            <option value="<?= html_escape($dept) ?>" <?= ($filters['department'] == $dept) ? 'selected' : '' ?>><?= html_escape($dept) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
              <div class="col-lg-2 col-md-4">
    <label class="form-label">جهاز البصمة</label>
    <select name="device" class="form-select">
        <option value="">الكل</option>
        <?php if(!empty($devices)): ?>
            <?php foreach($devices as $dev): ?>
                <?php 
                    // Determine display name
                    $displayName = $dev;
                    if($dev == 'Mobile App') $displayName = '📱 تطبيق الجوال';
                ?>
                <option value="<?= html_escape($dev) ?>" <?= ($filters['device'] == $dev) ? 'selected' : '' ?>>
                    <?= html_escape($displayName) ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label">المسمى الوظيفي</label>
                    <select name="profession" class="form-select">
                        <option value="">الكل</option>
                        <?php foreach($professions as $prof): ?>
                            <option value="<?= html_escape($prof) ?>" <?= ($filters['profession'] == $prof) ? 'selected' : '' ?>><?= html_escape($prof) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-lg-2 col-md-4">
                    <label class="form-label">الشركة</label>
                    <select name="company" class="form-select">
                        <option value="">الكل</option>
                        <?php foreach($companies as $comp): ?>
                            <option value="<?= html_escape($comp) ?>" <?= ($filters['company'] == $comp) ? 'selected' : '' ?>><?= html_escape($comp) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-lg-2 col-md-4">
                    <label class="form-label">الموقع</label>
                    <select name="location" class="form-select">
                        <option value="">الكل</option>
                        <?php foreach($locations as $loc): ?>
                            <option value="<?= html_escape($loc) ?>" <?= ($filters['location'] == $loc) ? 'selected' : '' ?>><?= html_escape($loc) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Add this after the location filter -->
<div class="col-lg-2 col-md-4">
    <label class="form-label">نوع العمل</label>
    <select name="job_type" class="form-select">
        <option value="">الكل</option>
        <?php if(!empty($job_types)): ?>
            <?php foreach($job_types as $job_type): ?>
                <?php if(!empty($job_type)): ?>
                    <?php 
                        // Arabic translations for common job types
                        $displayName = $job_type;
                        if($job_type == 'fulltime') $displayName = 'دوام كامل';
                        elseif($job_type == 'parttime') $displayName = 'دوام جزئي';
                        elseif($job_type == 'contract') $displayName = 'عقد';
                        elseif($job_type == 'freelance') $displayName = 'عمل حر';
                    ?>
                    <option value="<?= html_escape($job_type) ?>" <?= ($filters['job_type'] ?? '') == $job_type ? 'selected' : '' ?>>
                        <?= html_escape($displayName) ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>
                <div class="col-lg-2 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-bold w-100">تصفية</button>
                    <a href="<?= site_url('users1/attendance_overview') ?>" class="btn btn-outline-secondary"><i class="fas fa-redo"></i></a>
                </div>
            </div>
        </form>
    </div>

    <div class="kpi-row">
        <div class="kpi-card kpi-late" data-filter="حضور متأخر">
            <div class="kpi-info">
                <div class="kpi-val"><?= $kpis['total_late'] ?? 0 ?></div>
                <div class="kpi-lbl">حضور متأخر</div>
            </div>
            <div class="kpi-icon"><i class="fas fa-clock"></i></div>
        </div>
        <div class="kpi-card kpi-absent" data-filter="غياب">
            <div class="kpi-info">
                <div class="kpi-val"><?= $kpis['total_absent'] ?? 0 ?></div>
                <div class="kpi-lbl">غياب</div>
            </div>
            <div class="kpi-icon"><i class="fas fa-user-xmark"></i></div>
        </div>
        <div class="kpi-card kpi-incomplete" data-filter="سجل غير مكتمل">
            <div class="kpi-info">
                <div class="kpi-val"><?= $kpis['total_incomplete'] ?? 0 ?></div>
                <div class="kpi-lbl">بصمة ناقصة</div>
            </div>
            <div class="kpi-icon"><i class="fas fa-fingerprint"></i></div>
        </div>
        <div class="kpi-card kpi-leave" data-filter="إجازة">
            <div class="kpi-info">
                <div class="kpi-val"><?= $kpis['total_on_leave'] ?? 0 ?></div>
                <div class="kpi-lbl">في إجازة</div>
            </div>
            <div class="kpi-icon"><i class="fas fa-plane"></i></div>
        </div>
        <div class="kpi-card kpi-req" data-filter="تصحيح">
            <div class="kpi-info">
                <div class="kpi-val"><?= $kpis['total_requests'] ?? 0 ?></div>
                <div class="kpi-lbl">تصحيح بصمة</div>
            </div>
            <div class="kpi-icon"><i class="fas fa-file-contract"></i></div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>الموظف</th>
                        <?php foreach($date_headers as $date): ?>
                            <th>
                                <div class="d-flex flex-column align-items-center">
                                    <span style="font-size:1.1rem"><?= date('d', strtotime($date)) ?></span>
                                    <span style="font-size:0.7rem; opacity:0.7"><?= ['أحد','إثنين','ثلاثاء','أربعاء','خميس','جمعة','سبت'][date('w', strtotime($date))] ?></span>
                                </div>
                            </th>
                        <?php endforeach; ?>
                        <th class="text-danger">غياب</th>
                        <th class="text-primary">إجازة</th>
                        <th class="text-warning">تأخير</th>
                        <th class="text-info">تصحيح</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pivoted_data)): ?>
                        <tr>
                            <td colspan="<?= count($date_headers) + 5 ?>" class="text-center py-5">
                                <div class="opacity-50 mb-2"><i class="fas fa-search fa-3x"></i></div>
                                <h5 class="text-muted">لا توجد بيانات للعرض</h5>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($pivoted_data as $emp_id => $emp_data): ?>
                            <tr class="employee-row">
                                <td>
                                    <div class="employee-info">
                                        <div class="emp-name"><?= html_escape($emp_data['employee_name']) ?></div>
                                        <div class="emp-meta">
                                            <span class="badge bg-light text-dark border me-1">#<?= html_escape($emp_id) ?></span>
                                            <span><?= html_escape($emp_data['department']) ?></span>
                                            <?php if(!empty($emp_data['job_type'])): ?>
        <?php 
            $job_type_display = $emp_data['job_type'];
            if($emp_data['job_type'] == 'fulltime') $job_type_display = 'دوام كامل';
            elseif($emp_data['job_type'] == 'parttime') $job_type_display = 'دوام جزئي';
        ?>
        <span class="badge bg-info bg-opacity-10 text-info border"><?= html_escape($job_type_display) ?></span>
    <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <?php foreach($date_headers as $date): ?>
                                    <?php
                                        $cell_data = $emp_data['dates'][$date] ?? null;
                                        $cell_class = '';
                                        $content_top = '—';
                                        $content_btm = '';
                                        $dayOfWeek = date('w', strtotime($date));
                                        $tooltip = '';

                                        if ($cell_data) {
                                            $status = $cell_data['day_status'];
                                            if ($status === 'حاضر') {
                                                $cell_class = 'st-present';
                                                $content_top = $cell_data['check_in'] ?? '--';
                                                $content_btm = $cell_data['check_out'] ?? '--';
                                                $tooltip = "ساعات العمل: {$cell_data['work_duration']}";
                                                if ($cell_data['violation'] === 'حضور متأخر') { $cell_class = 'st-late'; $tooltip .= " (متأخر)"; }
                                                elseif ($cell_data['violation'] === 'سجل غير مكتمل') { $cell_class = 'st-incomplete'; $content_btm = '<i class="fas fa-exclamation text-danger"></i>'; }
                                            } elseif ($status === 'غياب') {
                                                $cell_class = 'st-absent'; $content_top = 'غائب';
                                            } elseif (strpos($status, 'إجازة') !== false) {
                                                $cell_class = 'st-leave'; $content_top = '<i class="fas fa-umbrella-beach fa-lg"></i>'; $tooltip = $status;
                                            } elseif ($status === 'عطلة رسمية') {
                                                $cell_class = 'st-leave'; $content_top = 'عطلة';
                                            } elseif ($status === 'تصحيح بصمة') {
                                                $cell_class = 'bg-info bg-opacity-10 text-info fw-bold'; $content_top = 'تصحيح';
                                            }
                                        } elseif ($dayOfWeek == 5 || $dayOfWeek == 6) {
                                            $cell_class = 'st-weekend';
                                        }
                                    ?>
                                    <td>
                                        <div class="cell-content <?= $cell_class ?>" data-bs-toggle="tooltip" title="<?= html_escape($tooltip) ?>">
                                            <span><?= $content_top ?></span>
                                            <?php if($content_btm): ?><span class="time-sub"><?= $content_btm ?></span><?php endif; ?>
                                        </div>
                                    </td>
                                <?php endforeach; ?>
                                <td class="sum-col text-danger"><?= $emp_data['total_absences'] ?: '-' ?></td>
                                <td class="sum-col text-primary"><?= $emp_data['total_vacations'] ?: '-' ?></td>
                                <td class="sum-col text-warning"><?= $emp_data['total_violations'] ?: '-' ?></td>
                                <td class="sum-col text-info"><?= $emp_data['total_corrections'] ?: '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
$(function() {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    const start = moment('<?= html_escape($filters['start_date']) ?>');
    const end = moment('<?= html_escape($filters['end_date']) ?>');
    
    $('#date_range').daterangepicker({
        startDate: start, endDate: end, opens: 'left',
        locale: { format: 'YYYY-MM-DD', separator: ' ➜ ', applyLabel: 'تطبيق', cancelLabel: 'إلغاء', fromLabel: 'من', toLabel: 'إلى', customRangeLabel: 'مخصص', daysOfWeek: ['أحد', 'إثنين', 'ثلاثاء', 'أربعاء', 'خميس', 'جمعة', 'سبت'], monthNames: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'] },
        ranges: { 'هذا الشهر': [moment().startOf('month'), moment().endOf('month')], 'الشهر الماضي': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')], 'آخر 7 أيام': [moment().subtract(6, 'days'), moment()] }
    }, function(start, end) { $('#start_date').val(start.format('YYYY-MM-DD')); $('#end_date').val(end.format('YYYY-MM-DD')); });

    let currentFilter = 'all';
    $('.kpi-card').on('click', function() {
        const filterText = $(this).data('filter');
        if (currentFilter === filterText) { currentFilter = 'all'; $('.kpi-card').removeClass('active'); } 
        else { currentFilter = filterText; $('.kpi-card').removeClass('active'); $(this).addClass('active'); }
        
        $('.employee-row').each(function() {
            if (currentFilter === 'all') { $(this).show(); return; }
            let match = false;
            $(this).find('.cell-content').each(function() {
                if (currentFilter === 'غياب' && $(this).hasClass('st-absent')) match = true;
                if (currentFilter === 'حضور متأخر' && $(this).hasClass('st-late')) match = true;
                if (currentFilter === 'سجل غير مكتمل' && $(this).hasClass('st-incomplete')) match = true;
                if (currentFilter === 'إجازة' && $(this).hasClass('st-leave')) match = true;
                if (currentFilter === 'تصحيح' && $(this).text().includes('تصحيح')) match = true;
            });
            $(this).toggle(match);
        });
    });
});
</script>
</body>
</html>