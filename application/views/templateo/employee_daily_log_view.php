<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تفاصيل الخصومات</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Tajawal', sans-serif; background: #f8f9fa; padding: 20px; font-size: 0.9rem; }
        .stat-card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center; height: 100%; border: 1px solid #eee;}
        .stat-val { font-size: 1.4rem; font-weight: 800; color: #0d3b66; }
        .total-card { border-bottom: 4px solid #dc3545; background: #fff5f5; }
        .total-val { color: #dc3545; font-size: 1.6rem; }
        .time-badge { font-family: monospace; background: #e9ecef; padding: 2px 6px; border-radius: 4px; direction: ltr; display: inline-block; }
        
        /* Highlight for Single Fingerprint Rows */
        .row-single-punch { background-color: #fff3cd !important; }

        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    
    <div class="d-flex justify-content-between mb-4 no-print">
        <button onclick="window.close()" class="btn btn-secondary">إغلاق</button>
        <button onclick="window.print()" class="btn btn-danger">طباعة</button>
    </div>

    <?php
        // ---------------------------------------------------------
        // 1. CALCULATE RATES & TOTALS
        // ---------------------------------------------------------
        $d_rate = $salary_details['daily_salary'] ?? 0;
        $m_rate = $salary_details['actual_minute_salary'] ?? ($d_rate / 480);
        
        // Official Totals
        $s_late   = $summary_data['minutes_late'] ?? 0;
        $s_early  = $summary_data['minutes_early'] ?? 0;
        $s_absent = $summary_data['absence'] ?? 0;
        $s_single = $summary_data['single_thing'] ?? 0;

        // Calculate Total Cost
        $cost_minutes = ($s_late + $s_early) * $m_rate;
        $cost_absent  = $s_absent * $d_rate;
        $cost_single  = $s_single * $d_rate;
        $OFFICIAL_TOTAL = $cost_minutes + $cost_absent + $cost_single;
    ?>

    <h4 class="text-center fw-bold mb-4">كشف الخصومات التفصيلي: <?php echo html_escape($employee_name); ?></h4>

    <div class="row g-3 mb-4">
        <div class="col-3">
            <div class="stat-card">
                <div class="small text-muted">إجمالي التأخير</div>
                <div class="stat-val"><?php echo $s_late + $s_early; ?> <small style="font-size:0.5em">دقيقة</small></div>
            </div>
        </div>
        <div class="col-3">
            <div class="stat-card">
                <div class="small text-muted">إجمالي الغياب</div>
                <div class="stat-val"><?php echo $s_absent; ?> <small style="font-size:0.5em">يوم</small></div>
            </div>
        </div>
        <div class="col-3">
            <div class="stat-card border-warning">
                <div class="small text-muted">بصمة منفردة</div>
                <div class="stat-val text-warning"><?php echo $s_single; ?> <small style="font-size:0.5em">يوم</small></div>
            </div>
        </div>
        <div class="col-3">
            <div class="stat-card total-card">
                <div class="text-danger fw-bold">الإجمالي المعتمد</div>
                <div class="stat-val total-val"><?php echo number_format($OFFICIAL_TOTAL, 2); ?></div>
                <div class="text-danger small">ريال سعودي</div>
            </div>
        </div>
    </div>
    
     <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white fw-bold text-primary">
            <i class="fas fa-file-invoice-dollar me-2"></i>ملخص الخصومات المالية
        </div>
        <div class="table-responsive">
            <table class="table table-bordered mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th>بند الخصم</th>
                        <th>العدد / المدة</th>
                        <th>القيمة المخصومة</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>التأخير والانصراف المبكر</td>
                        <td><?php echo $s_late + $s_early; ?> دقيقة</td>
                        <td class="text-danger fw-bold"><?php echo number_format($cost_minutes, 2); ?> ر.س</td>
                    </tr>
                    <tr>
                        <td>الغياب</td>
                        <td><?php echo $s_absent; ?> يوم</td>
                        <td class="text-danger fw-bold"><?php echo number_format($cost_absent, 2); ?> ر.س</td>
                    </tr>
                    <tr class="table-warning">
                        <td>بصمة منفردة</td>
                        <td><?php echo $s_single; ?> يوم</td>
                        <td class="text-danger fw-bold"><?php echo number_format($cost_single, 2); ?> ر.س</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-bold">التفاصيل اليومية (الأيام التي بها خصم)</div>
        <table class="table table-bordered mb-0 text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>التاريخ</th>
                    <th>الحالة</th>
                    <th>وقت البصمة</th>
                    <th>التفاصيل</th>
                    <th width="15%">قيمة الخصم</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $has_rows = false;

                if(!empty($daily_log)):
                foreach($daily_log as $row): 
                    // Calculate Cost for this specific day
                    $line_cost = 0;
                    
                    // 1. Late/Early Cost
                    if ($row['late_minutes'] > 0 || $row['early_minutes'] > 0) {
                        $line_cost += ($row['late_minutes'] + $row['early_minutes']) * $m_rate;
                    }
                    // 2. Absence Cost
                    if ($row['is_absent']) {
                        $line_cost += $d_rate;
                    }
                    // 3. Single Fingerprint Cost (Full Day Deduction)
                    if ($row['is_single']) {
                        $line_cost += $d_rate;
                    }

                    // --- VISIBILITY FIX ---
                    // Show row if it has money cost > 0 OR if it is a Single Punch (even if cost 0)
                    if ($line_cost > 0.001 || $row['is_single'] || $row['is_absent']):
                        $has_rows = true;
                        
                        // Add highlighting class for Single Punch
                        $row_class = $row['is_single'] ? 'row-single-punch' : '';
                ?>
                <tr class="<?php echo $row_class; ?>">
                    <td class="text-start">
                        <strong><?php echo $row['day_name']; ?></strong><br>
                        <small class="text-muted"><?php echo $row['date']; ?></small>
                    </td>
                    <td>
                        <?php if($row['is_absent']): ?>
                            <span class="badge bg-danger">غياب</span>
                        <?php elseif($row['is_single']): ?>
                            <span class="badge bg-warning text-dark">بصمة منفردة</span>
                        <?php else: ?>
                            <span class="badge bg-info text-dark">تأخير / انصراف مبكر</span>
                        <?php endif; ?>
                    </td>
                    
                    <td dir="ltr">
                        <?php if($row['is_absent']): ?>
                            <span class="text-muted">—</span>
                        <?php else: ?>
                            <span class="time-badge"><?php echo $row['check_in']; ?></span> 
                            <i class="fas fa-arrow-left text-muted mx-1" style="font-size:0.8em"></i>
                            <span class="time-badge"><?php echo $row['check_out']; ?></span>
                        <?php endif; ?>
                    </td>

                    <td class="text-start small text-danger">
                        <?php 
                        if (!empty($row['violation_details'])) {
                            echo implode('<br>', $row['violation_details']);
                        } else {
                            echo ($row['is_absent']) ? 'غياب يوم كامل' : '-';
                        }
                        ?>
                    </td>
                    <td class="fw-bold text-danger">
                        -<?php echo number_format($line_cost, 2); ?> ر.س
                    </td>
                </tr>
                <?php endif; endforeach; endif; ?>

                <?php if(!$has_rows): ?>
                    <tr><td colspan="5" class="py-4 text-muted">سجل نظيف! لا توجد خصومات.</td></tr>
                <?php endif; ?>
            </tbody>
            
            <tfoot>
                <tr class="table-light border-top">
                    <td colspan="4" class="text-end text-muted small py-3">
                        الإجمالي النهائي:
                    </td>
                    <td class="fw-bold text-danger fs-5 py-3">
                        <?php echo number_format($OFFICIAL_TOTAL, 2); ?> ر.س
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>