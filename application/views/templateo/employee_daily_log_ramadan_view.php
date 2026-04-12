<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تفاصيل الخصومات (رمضان)</title>
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
        // --- 1. SETUP VARIABLES ---
        $safe_salary_details = is_array($salary_details) ? $salary_details : [];
        $d_rate = $safe_salary_details['daily_salary'] ?? 0;
        
        $is_breastfeeding = (isset($working_hours) && (float)$working_hours == 8.0);
        $ramadan_hours = $is_breastfeeding ? 5.0 : 6.0;
        $ramadan_mins = $ramadan_hours * 60; 
        $m_rate = $d_rate > 0 ? ($d_rate / $ramadan_mins) : 0;
        
        $safe_daily_log = is_array($daily_log) ? $daily_log : [];

        // --- 2. CALCULATE EXACT TOTALS FROM THE ROWS ---
        $s_late_early_mins = 0;
        $s_absent_days = 0;
        $s_single_days = 0;
        
        $cost_minutes = 0;
        $cost_absent = 0;
        $cost_single = 0;

        $processed_log = []; // We will store only the valid deduction rows here

        foreach($safe_daily_log as $row) {
            $c_in = $row['check_in'] ?? '--';
            $c_out = $row['check_out'] ?? '--';
            $r_date = $row['date'] ?? date('Y-m-d');
            
            $line_cost = 0;
            $violation_text = '';
            
            $has_valid_punches = (strpos($c_in, ':') !== false && strpos($c_out, ':') !== false);
            
            if (!empty($row['is_absent'])) {
                $line_cost = $d_rate;
                $violation_text = 'غياب يوم كامل';
                $s_absent_days++;
                $cost_absent += $line_cost;
            } 
            elseif (!empty($row['is_single'])) {
                $line_cost = $d_rate;
                $violation_text = 'بصمة منفردة';
                $s_single_days++;
                $cost_single += $line_cost;
            } 
            elseif ($has_valid_punches) {
                $in_ts = strtotime($r_date . ' ' . $c_in);
                $out_ts = strtotime($r_date . ' ' . $c_out);
                
                if ($out_ts < $in_ts) { $out_ts += 86400; } // Cross Midnight Fix
                
                $worked_hours = ($out_ts - $in_ts) / 3600;
                $shortage_hours = $ramadan_hours - $worked_hours;
                
                if ($shortage_hours > 0.033) { 
                    $shortage_mins = round($shortage_hours * 60);
                    $line_cost = ($shortage_mins * $m_rate);
                    $violation_text = "نقص {$shortage_mins} دقيقة";
                    
                    $s_late_early_mins += $shortage_mins;
                    $cost_minutes += $line_cost;
                }
            }

            // Only keep rows that have actual deductions
            if ($line_cost > 0.001 || !empty($row['is_single']) || !empty($row['is_absent'])) {
                $row['calculated_cost'] = $line_cost;
                $row['violation_text'] = $violation_text;
                $processed_log[] = $row;
            }
        }

        $OFFICIAL_TOTAL = $cost_minutes + $cost_absent + $cost_single;
    ?>

    <h4 class="text-center fw-bold mb-4">كشف الخصومات التفصيلي (شهر رمضان): <?php echo html_escape($employee_name ?? 'غير معروف'); ?></h4>

    <div class="row g-3 mb-4">
        <div class="col-3">
            <div class="stat-card">
                <div class="small text-muted">إجمالي نقص الساعات</div>
                <div class="stat-val"><?php echo $s_late_early_mins; ?> <small style="font-size:0.5em">دقيقة</small></div>
            </div>
        </div>
        <div class="col-3">
            <div class="stat-card">
                <div class="small text-muted">إجمالي الغياب</div>
                <div class="stat-val"><?php echo $s_absent_days; ?> <small style="font-size:0.5em">يوم</small></div>
            </div>
        </div>
        <div class="col-3">
            <div class="stat-card border-warning">
                <div class="small text-muted">بصمة منفردة</div>
                <div class="stat-val text-warning"><?php echo $s_single_days; ?> <small style="font-size:0.5em">يوم</small></div>
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
            <i class="fas fa-file-invoice-dollar me-2"></i>ملخص الخصومات المالية (مبني على <?php echo $ramadan_hours; ?> ساعات دوام)
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
                        <td>نقص ساعات الدوام (تأخير أو مبكر)</td>
                        <td><?php echo $s_late_early_mins; ?> دقيقة</td>
                        <td class="text-danger fw-bold"><?php echo number_format($cost_minutes, 2); ?> ر.س</td>
                    </tr>
                    <tr>
                        <td>الغياب</td>
                        <td><?php echo $s_absent_days; ?> يوم</td>
                        <td class="text-danger fw-bold"><?php echo number_format($cost_absent, 2); ?> ر.س</td>
                    </tr>
                    <tr class="table-warning">
                        <td>بصمة منفردة</td>
                        <td><?php echo $s_single_days; ?> يوم</td>
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
                <?php if(!empty($processed_log)): ?>
                    <?php foreach($processed_log as $row): ?>
                    <tr class="<?php echo !empty($row['is_single']) ? 'row-single-punch' : ''; ?>">
                        <td class="text-start">
                            <strong><?php echo $row['day_name'] ?? ''; ?></strong><br>
                            <small class="text-muted"><?php echo $row['date']; ?></small>
                        </td>
                        <td>
                            <?php if(!empty($row['is_absent'])): ?>
                                <span class="badge bg-danger">غياب</span>
                            <?php elseif(!empty($row['is_single'])): ?>
                                <span class="badge bg-warning text-dark">بصمة منفردة</span>
                            <?php else: ?>
                                <span class="badge bg-info text-dark">نقص ساعات</span>
                            <?php endif; ?>
                        </td>
                        
                        <td dir="ltr">
                            <?php if(!empty($row['is_absent'])): ?>
                                <span class="text-muted">—</span>
                            <?php else: ?>
                                <span class="time-badge"><?php echo $row['check_in']; ?></span> 
                                <i class="fas fa-arrow-left text-muted mx-1" style="font-size:0.8em"></i>
                                <span class="time-badge"><?php echo $row['check_out']; ?></span>
                            <?php endif; ?>
                        </td>

                        <td class="text-start small text-danger">
                            <?php echo $row['violation_text']; ?>
                        </td>
                        <td class="fw-bold text-danger">
                            -<?php echo number_format($row['calculated_cost'], 2); ?> ر.س
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
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