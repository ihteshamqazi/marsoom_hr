<?php
// --- [START] UPDATED: FETCH SHEET ID WITH ARABIC DATE SUPPORT ---
$CI =& get_instance();
$CI->load->model('hr_model');

// 1. Get the raw input from URL (e.g., "مسير راتب شهر يناير 2026")
$raw_input = urldecode($this->input->get('month'));
if (empty($raw_input) && isset($payroll_data[0]['n13'])) {
    $raw_input = $payroll_data[0]['n13'];
}

// 2. Initialize Default Date
$clean_date = date('Y-m');

// 3. Logic to Parse the Date
// A. Check if it's already in YYYY-MM format (e.g., 2026-01)
if (preg_match('/(\d{4}-\d{2})/', $raw_input, $matches)) {
    $clean_date = $matches[1];
} 
// B. Check if it contains Arabic Month Names (e.g., يناير 2026)
else {
    $arabic_months = [
        'يناير' => '01', 'فبراير' => '02', 'مارس' => '03', 'أبريل' => '04',
        'مايو' => '05', 'يونيو' => '06', 'يوليو' => '07', 'أغسطس' => '08',
        'سبتمبر' => '09', 'أكتوبر' => '10', 'نوفمبر' => '11', 'ديسمبر' => '12'
    ];

    foreach ($arabic_months as $ar_name => $en_num) {
        // Check if the input string contains this Arabic month name
        if (mb_strpos($raw_input, $ar_name) !== false) {
            // Found the month! Now try to find the Year (4 digits)
            if (preg_match('/(\d{4})/', $raw_input, $year_matches)) {
                $year = $year_matches[1];
                $clean_date = $year . '-' . $en_num; // Result: 2026-01
            }
            break; // Stop loop once found
        }
    }
}

// 4. Construct Search Date (Always use the 1st of the month)
$search_date = $clean_date . '-01'; 

// 5. Fetch the Sheet ID from Database
$target_sheet = $CI->hr_model->get_salary_sheet_by_date($search_date);
$current_sheet_id = $target_sheet ? $target_sheet['id'] : 0;

// 6. Common Data Processing (Existing Code)
$descriptions_map = $this->hr_model->get_payroll_descriptions($raw_input);

$total_net = 0; $total_gross = 0; $total_deductions = 0;
$total_basic = 0; $total_housing = 0; $total_transport = 0; $total_other = 0;

if(isset($payroll_data) && is_array($payroll_data)){
    foreach($payroll_data as $row) {
        $net = (float)($row['n12'] ?? 0);
        $gross = (float)($row['n6'] ?? 0);
        $deduct = (float)($row['n11'] ?? 0);
        $total_basic += (float)($row['n21'] ?? 0);
        $total_housing += (float)($row['n22'] ?? 0);
        $total_transport += (float)($row['n23'] ?? 0);
        $total_other += (float)($row['n24'] ?? 0);
        $total_net += $net;
        $total_gross += $gross;
        $total_deductions += $deduct;
    }
}
// --- [END] ---
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($page_title ?? 'تقرير الرواتب المعالجة') ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root { --primary-color: #001f3f; --accent-color: #FF8C00; --bg-light: #f4f7fa; --text-dark: #2c3e50; }
        body { font-family: 'Tajawal', sans-serif; background-color: var(--bg-light); color: var(--text-dark); font-size: 0.9rem; }
        .summary-card { background: white; border-radius: 12px; padding: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); height: 100%; border-bottom: 3px solid transparent; }
        .border-gross { border-bottom-color: var(--primary-color); }
        .border-net { border-bottom-color: #198754; }
        .border-deduct { border-bottom-color: #dc3545; }
        .card-label { color: #6c757d; font-size: 0.85rem; font-weight: 600; margin-bottom: 5px; }
        .card-value { font-size: 1.4rem; font-weight: 800; color: var(--text-dark); }
        .main-card { background: white; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); border: none; overflow: hidden; }
        table.dataTable thead th { background-color: var(--primary-color) !important; color: white; vertical-align: middle; text-align: center; white-space: nowrap; }
        .bg-net { background-color: #eff6ff; font-weight: 800; color: #1e40af; font-size: 1.1em; }
        .badge-stopped { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        .val-pos { color: #198754; font-weight: 600; font-family: 'Courier New', monospace; }
        .val-neg { color: #dc3545; font-weight: 600; font-family: 'Courier New', monospace; }

        /* Print Styles */
        #payslip-template { display: none; background: white; padding: 40px; max-width: 210mm; margin: 0 auto; color: #000; font-family: 'Tajawal', sans-serif; }
        .print-header-container { text-align: center; margin-bottom: 30px; position: relative; border-bottom: 2px solid #eee; padding-bottom: 20px; }
        .print-logo { max-height: 100px; width:220px; margin-bottom: 15px; }
        .company-name { font-size: 18px; font-weight: bold; color: #001f3f; margin-bottom: 5px; }
        .print-title { font-size: 24px; font-weight: 800; margin: 10px 0; color: #000; }
        .print-month { font-size: 16px; color: #555; font-weight: bold; }
        .emp-info-box { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin-bottom: 25px; }
        .emp-info-table { width: 100%; }
        .emp-info-table td { padding: 6px; font-size: 14px; }
        .label-cell { color: #6b7280; font-weight: 600; width: 15%; }
        .value-cell { color: #111827; font-weight: 700; width: 35%; }
        .details-container { display: flex; gap: 20px; margin-bottom: 30px; }
        .details-column { flex: 1; border: 1px solid #000; border-radius: 4px; overflow: hidden; }
        .details-header { padding: 10px; text-align: center; font-weight: bold; font-size: 15px; border-bottom: 1px solid #000; }
        .header-earnings { background-color: #f0fdf4; color: #166534; }
        .header-deductions { background-color: #fef2f2; color: #991b1b; }
        .details-row { display: flex; justify-content: space-between; padding: 8px 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .details-row:last-child { border-bottom: none; }
        .details-row span:last-child { font-family: 'Courier New', monospace; font-weight: 700; }
        .total-row { background-color: #f8f9fa; border-top: 2px solid #000; font-weight: 800; }
        .net-salary-box { border: 2px solid #001f3f; border-radius: 8px; background-color: #f0f9ff; padding: 15px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .net-label { font-size: 18px; font-weight: bold; color: #001f3f; }
        .net-value { font-size: 22px; font-weight: 900; color: #001f3f; }
        .bank-info-box { border-top: 1px dashed #ccc; padding-top: 15px; font-size: 13px; color: #555; }
        .bank-row { display: flex; justify-content: space-between; margin-bottom: 5px; }

        @media print {
            body * { visibility: hidden; }
            #payslip-template, #payslip-template * { visibility: visible; }
            #payslip-template { display: block !important; position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 10mm; }
            @page { size: A4; margin: 0; }
        }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h3 class="mb-0 fw-bold text-dark"><i class="fas fa-chart-pie me-2 text-warning"></i> <?= html_escape($page_title) ?></h3>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= site_url('users1/export_processed_payroll') . '?' . http_build_query($filters); ?>" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">
                <i class="fas fa-file-excel me-2"></i> تصدير Excel
            </a>
            <a href="<?= site_url('users1/main_hr1'); ?>" class="btn btn-secondary btn-sm rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> عودة للرئيسية
            </a>
        </div>
    </div>

    <div class="breakdown-container mb-4">
        <h6 class="fw-bold text-muted mb-3"><i class="fas fa-info-circle me-1"></i> ملخص الاستحقاقات والخصومات</h6>
        <div class="row g-3">
             <div class="col-lg-2 col-md-4">
                <div class="summary-card main border-gross">
                    <div class="card-label">إجمالي الاستحقاق</div>
                    <div class="card-value text-primary"><?= number_format($total_gross, 2) ?></div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="summary-card main border-deduct">
                    <div class="card-label">إجمالي الخصومات</div>
                    <div class="card-value text-danger"><?= number_format($total_deductions, 2) ?></div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="summary-card main border-net">
                    <div class="card-label">صافي الرواتب</div>
                    <div class="card-value text-success"><?= number_format($total_net, 2) ?></div>
                </div>
            </div>
            <div class="col-lg-6">
                 <div class="row text-center mt-2 bg-white rounded p-2 shadow-sm">
                    <div class="col"><small>أساسي</small><br><b class="text-dark"><?= number_format($total_basic) ?></b></div>
                    <div class="col"><small>سكن</small><br><b class="text-dark"><?= number_format($total_housing) ?></b></div>
                    <div class="col"><small>نقل</small><br><b class="text-dark"><?= number_format($total_transport) ?></b></div>
                    <div class="col"><small>أخرى</small><br><b class="text-dark"><?= number_format($total_other) ?></b></div>
                 </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
             <form method="GET" action="<?= site_url('users1/processed_payroll_report') ?>" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold mb-1">الشهر</label>
                    <select name="month" class="form-select form-select-sm">
                        <option value="">-- عرض الكل --</option>
                        <?php foreach($month_list as $m): ?>
                            <option value="<?= $m['n13'] ?>" <?= ($filters['month'] == $m['n13']) ? 'selected' : '' ?>><?= $m['n13'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold mb-1">الشركة</label>
                    <select name="company_code" class="form-select form-select-sm">
                        <option value="">-- الجميع --</option>
                        <?php foreach($company_list as $code => $name): ?>
                            <option value="<?= $code ?>" <?= ($filters['company_code'] == $code) ? 'selected' : '' ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold mb-1">بحث</label>
                    <input type="text" name="employee_search" class="form-control form-select-sm" value="<?= html_escape($filters['employee_search']) ?>" placeholder="بحث سريع...">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">تطبيق الفلتر</button>
                </div>
            </form>
        </div>
    </div>

    <div class="main-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="fullPayrollTable" class="table table-hover table-bordered m-0" style="width:100%">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width:40px;">طباعة</th>
                            <th rowspan="2" style="width:50px;">#</th>
                            <th rowspan="2" style="min-width: 220px;">الموظف</th> <th colspan="5" style="background-color: #e3f2fd !important;">تفاصيل الراتب (Earnings)</th>
                            <th colspan="4" style="background-color: #fff3cd !important;">التسويات (Adjustments)</th>
                            <th colspan="7" style="background-color: #f8d7da !important;">الخصومات (Deductions)</th>
                            
                            <th rowspan="2" style="background-color: var(--primary-color);">الصافي</th>
                        </tr>
                        <tr>
                            <th class="small">أساسي</th> 
                            <th class="small">سكن</th> 
                            <th class="small">نقل</th> 
                            <th class="small">أخرى</th> 
                            <th class="small fw-bold">الإجمالي</th>
                            
                            <th class="small">نقص</th> 
                            <th class="small">متبقي</th> 
                            <th class="small">تعويض</th>
                            <th class="small text-success">سبب التعويض</th> <th class="small">غياب</th> 
                            <th class="small">تأخير</th> 
                            <th class="small">بصمة ناقصة</th>
                            <th class="small">تأمينات</th> 
                            <th class="small">جزاءات</th> 
                            <th class="small text-danger">سبب الخصم</th> <th class="small fw-bold">م. الخصم</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($payroll_data as $row): ?>
                        <?php 
                            $gross = (float)($row['n6'] ?? 0);
                            $net = (float)($row['n12'] ?? 0);
                            $is_stopped = ($gross > 0 && $net == 0);
                            $tr_class = $is_stopped ? 'table-danger' : '';
                            $row_json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            
                            // Lookup Notes
                            $emp_id = trim($row['n1']); 
                            $rep_note = isset($descriptions_map[$emp_id]['reparation_note']) ? $descriptions_map[$emp_id]['reparation_note'] : '-';
                            $disc_note = isset($descriptions_map[$emp_id]['discount_note']) ? $descriptions_map[$emp_id]['discount_note'] : '-';
                        ?>
                        <tr class="<?= $tr_class ?>">
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-dark border-0" onclick="printPayslip(<?= $row_json ?>)">
                                    <i class="fas fa-print"></i>
                                </button>
                            </td>
                            <td class="text-center small"><?= $row['n1'] ?></td>
                            <td>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="fw-bold"><?= $row['n2'] ?></div>
                                    <button type="button" 
                                            class="btn btn-sm btn-info text-white ms-2" 
                                            onclick="openAttendancePopup('<?= $row['n1'] ?>')" 
                                            title="عرض سجل الحضور">
                                        <i class="fa fa-list-alt"></i>
                                    </button>
                                </div>
                                <?php if($is_stopped): ?><span class="badge badge-stopped">راتب موقف</span><?php endif; ?>
                            </td>
                            
                            <td class="val-pos"><?= number_format((float)($row['n21']??0), 2) ?></td>
                            <td class="val-pos"><?= number_format((float)($row['n22']??0), 2) ?></td>
                            <td class="val-pos"><?= number_format((float)($row['n23']??0), 2) ?></td>
                            <td class="val-pos bg-light"><?= number_format((float)($row['n24']??0), 2) ?></td>
                            <td class="val-pos fw-bold"><?= number_format($gross, 2) ?></td>

                            <td class="text-muted"><?= (float)($row['n20']??0) != 0 ? number_format((float)$row['n20'],2) : '-' ?></td>
                            <td class="text-muted"><?= (float)($row['n15']??0) > 0 ? number_format((float)$row['n15'],2) : '-' ?></td>
                            <td class="text-muted"><?= (float)($row['n16']??0) > 0 ? number_format((float)$row['n16'],2) : '-' ?></td>
                            
                            <td style="font-size: 0.8rem; color: #198754; font-weight:bold;"><?= $rep_note ?></td>

                            <?php $abs_tot = (float)($row['n9']??0) + (float)($row['n29']??0); ?>
                            <?php $late_tot = (float)($row['n7']??0) + (float)($row['n8']??0); ?>
                            <td class="val-neg"><?= $abs_tot > 0 ? number_format($abs_tot, 2) : '-' ?></td>
                            <td class="val-neg"><?= $late_tot > 0 ? number_format($late_tot, 2) : '-' ?></td>
                            
<?php $n31_clean = (float)str_replace(',', '', $row['n31'] ?? 0); ?>
<td class="val-neg"><?= $n31_clean > 0 ? number_format($n31_clean, 2) : '-' ?></td>
                            <td class="val-neg"><?= (float)($row['n10']??0) > 0 ? number_format((float)$row['n10'], 2) : '-' ?></td>
                            <td class="val-neg"><?= (float)($row['n17']??0) > 0 ? number_format((float)$row['n17'], 2) : '-' ?></td>
                            
                            <td style="font-size: 0.8rem; color: #dc3545; font-weight:bold;"><?= $disc_note ?></td>
                            
                            <td class="val-neg fw-bold"><?= number_format((float)($row['n11']??0), 2) ?></td>

                            <td class="bg-net"><?= number_format($net, 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="payslip-template">
    <div class="print-header-container">
        <img src="<?= base_url('assets/imeges/m1.png') ?>" alt="Logo" class="print-logo">
        <div class="company-name">شركة مرسوم لتحصيل الديون</div>
        <div style="font-size: 14px; color: #555;">المملكة العربية السعودية - الرياض - العليا</div>
        <div class="print-title">تفصيل الراتب</div>
        <div class="print-month" id="print-month"></div>
        <div style="position: absolute; left: 0; bottom: 0; font-size: 12px; text-align: left;">
            <div>أنشئ بواسطة نظام الموارد البشرية</div>
            <div id="print-date"><?= date('Y-m-d') ?></div>
        </div>
    </div>
    <div class="emp-info-box">
        <table class="emp-info-table">
            <tr>
                <td class="label-cell">اسم الموظف :</td>
                <td class="value-cell" id="print-name"></td>
                <td class="label-cell">المسمى الوظيفي :</td>
                <td class="value-cell" id="print-job">موظف</td>
            </tr>
            <tr>
                <td class="label-cell">رقم الموظف :</td>
                <td class="value-cell" id="print-id"></td>
                <td class="label-cell">الشركة :</td>
                <td class="value-cell" id="print-company">مرسوم</td>
            </tr>
        </table>
    </div>
    <div class="details-container">
        <div class="details-column">
            <div class="details-header header-earnings">الإيــرادات (Earnings)</div>
            <div class="details-row"><span>الراتب الأساسي</span> <span id="print-basic"></span></div>
            <div class="details-row"><span>بدل سكن</span> <span id="print-housing"></span></div>
            <div class="details-row"><span>بدل مواصلات</span> <span id="print-transport"></span></div>
            <div class="details-row"><span>بدلات أخرى</span> <span id="print-other"></span></div>
            <div class="details-row"><span>تعويضات / فروقات</span> <span id="print-diff"></span></div>
            <div class="details-row total-row"><span>مجموع الإيرادات</span> <span id="print-total-earn"></span></div>
        </div>
        <div class="details-column">
            <div class="details-header header-deductions">الخصومـات (Deductions)</div>
            <div class="details-row"><span>تأمينات اجتماعية (GOSI)</span> <span id="print-gosi"></span></div>
            <div class="details-row"><span>غياب</span> <span id="print-abs"></span></div>
            <div class="details-row"><span>تأخير / خروج مبكر</span> <span id="print-late"></span></div>
            <div class="details-row"><span>خصم بصمة ناقصة</span> <span id="print-single-punch"></span></div>
            <div class="details-row"><span>جزاءات وعقوبات</span> <span id="print-sanction"></span></div>
            <div class="details-row"><span>سلف / قروض</span> <span id="print-loan"></span></div>
            <div class="details-row total-row"><span>مجموع الخصومات</span> <span id="print-total-deduct"></span></div>
        </div>
    </div>
    <div class="net-salary-box">
        <div class="net-label">صافي الراتب (Net Salary)</div>
        <div class="net-value" id="print-net" dir="ltr"></div>
    </div>
    <div class="bank-info-box">
        <div class="bank-row">
            <div style="width: 50%;"><strong>اسم البنك :</strong> <span id="print-bank"></span></div>
            <div style="width: 50%;"><strong>طريقة الدفع :</strong> تحويل بنكي</div>
        </div>
        <div class="bank-row">
            <div style="width: 100%;"><strong>IBAN :</strong> <span id="print-iban" style="font-family:monospace; font-weight:bold;"></span></div>
        </div>
    </div>
</div>

<div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="height: 90vh;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="attendanceModalLabel">تفاصيل الحضور والانصراف (Attendance Logs)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="attendanceFrame" src="" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" onclick="printIframe()">طباعة التقرير</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        var table = $('#fullPayrollTable').DataTable({
            dom: '<"d-flex justify-content-between align-items-center px-3 pt-3"f>rt<"d-flex justify-content-between px-3 pb-3"ip>',
            language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json" },
            pageLength: 50,
            scrollX: true,
            columnDefs: [ { orderable: false, targets: 0 } ]
        });
    });

    // --- Payslip Printing Logic ---
    // --- Payslip Printing Logic ---
    function printPayslip(data) {
        $('#print-month').text(data.n13);
        $('#print-name').text(data.n2);
        $('#print-id').text(data.n1);
        $('#print-company').text(data.n14 || 'مرسوم');
        
        $('#print-basic').text(formatMoney(data.n21));
        $('#print-housing').text(formatMoney(data.n22));
        $('#print-transport').text(formatMoney(data.n23));
        $('#print-other').text(formatMoney(data.n24));
        
        let adjustments = parseFloat(data.n15||0) + parseFloat(data.n16||0) + parseFloat(data.n20||0);
        $('#print-diff').text(formatMoney(adjustments));
        $('#print-total-earn').text(formatMoney(data.n6));

        $('#print-gosi').text(formatMoney(data.n10));
        let abs_total = parseFloat(data.n9||0) + parseFloat(data.n29||0);
        $('#print-abs').text(formatMoney(abs_total));
        let late_total = parseFloat(data.n7||0) + parseFloat(data.n8||0);
        $('#print-late').text(formatMoney(late_total));
        
        // Load n31 data into the printable view dynamically
        $('#print-single-punch').text(formatMoney(data.n31));
        
        $('#print-sanction').text(formatMoney(data.n17));
        $('#print-loan').text(formatMoney(data.n18));
        
        // --- UPDATED TOTAL DEDUCT LOGIC (n8 + n9 + n10) ---
        let n7_clean = parseFloat(String(data.n7 || 0).replace(/,/g, ''));
        let n8_clean = parseFloat(String(data.n8 || 0).replace(/,/g, ''));
        let n9_clean = parseFloat(String(data.n9 || 0).replace(/,/g, ''));
        let n10_clean = parseFloat(String(data.n10 || 0).replace(/,/g, ''));
        let total_deduct_calculated = n7_clean + n8_clean + n9_clean + n10_clean;
        
        $('#print-total-deduct').text(formatMoney(total_deduct_calculated));
        // --------------------------------------------------

        $('#print-net').text('SAR ' + formatMoney(data.n12));
        $('#print-bank').text(data.n5 || '-');
        $('#print-iban').text(data.n4 || '-');

        window.print();
    }

    function formatMoney(amount) {
    // Remove commas before parsing to prevent cutting off at the thousands separator
    let cleanAmount = String(amount).replace(/,/g, '').trim();
    let val = parseFloat(cleanAmount);
    
    if(isNaN(val) || val === 0) return '0.00';
    return val.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

    // --- Attendance Log Modal Logic ---
    
    // Get the PHP sheet_id into JS
  var currentSheetId = "<?= $current_sheet_id ?>"; 
    var currentMonthStr = "<?= $clean_date ?>";

    function openAttendancePopup(employeeId) {
        // Validation: If no Sheet ID found
        if(currentSheetId == 0 || currentSheetId == "") {
            Swal.fire({
                icon: 'warning',
                title: 'تنبيه',
                text: 'لم يتم العثور على مسير رواتب مرتبط بهذا التاريخ (' + currentMonthStr + '). الرجاء التأكد من اختيار الشهر من الفلتر بالأعلى.',
                confirmButtonText: 'حسناً'
            });
            return;
        }

        // Construct the URL: users1/employee_daily_log/EMPLOYEE_ID/SHEET_ID
        var url = "<?= base_url('users1/employee_daily_log_ramadan/') ?>" + employeeId + "/" + currentSheetId;

        // 1. Set the iframe src
        var frame = document.getElementById('attendanceFrame');
        frame.src = url;

        // 2. Show the modal
        var myModal = new bootstrap.Modal(document.getElementById('attendanceModal'));
        myModal.show();
    }

    // Print content inside the iframe
    function printIframe() {
        var frame = document.getElementById('attendanceFrame');
        if (frame.contentWindow) {
            frame.contentWindow.focus();
            frame.contentWindow.print();
        }
    }

    // Clear iframe when closed to save memory
    document.getElementById('attendanceModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('attendanceFrame').src = "";
    });
</script>

</body>
</html>