<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة الانتداب #<?= $req['id'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    
    <style>
        :root { --primary: #001f3f; --border: #dee2e6; }
        * { box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        body { font-family: 'Tajawal', sans-serif; background: #525659; margin: 0; padding: 20px; }
        .page { background: white; width: 210mm; min-height: 297mm; margin: 0 auto; padding: 15mm; box-shadow: 0 0 10px rgba(0,0,0,0.3); position: relative; border-top: 5px solid var(--primary); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--primary); padding-bottom: 20px; margin-bottom: 30px; }
        .company-info h2 { color: var(--primary); font-weight: 800; margin: 0; font-size: 1.6rem; }
        .company-info h4 { color: #555; font-weight: 500; margin: 5px 0 0; font-size: 1rem; }
        .doc-meta { text-align: left; }
        .doc-meta h1 { font-size: 1.8rem; margin: 0; color: #333; }
        .doc-meta .badge { background: #eee; padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; display: inline-block; margin-top: 5px; border: 1px solid #ddd; }
        .section-title { background: #f8f9fa; border-right: 4px solid var(--primary); padding: 8px 15px; font-weight: 700; color: var(--primary); margin: 20px 0 15px; font-size: 1.1rem; border: 1px solid #eee; border-right-width: 4px; }
        .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .info-box { border: 1px solid var(--border); padding: 10px; border-radius: 6px; }
        .info-label { display: block; font-size: 0.8rem; color: #666; margin-bottom: 3px; }
        .info-value { display: block; font-weight: 700; font-size: 1rem; color: #000; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 0.9rem; }
        .data-table th { background: var(--primary); color: white; padding: 10px; text-align: right; }
        .data-table td { border: 1px solid var(--border); padding: 10px; }
        .data-table tr:nth-child(even) { background: #fdfdfd; }
        .finance-box { background: #f8fbfd; border: 1px dashed var(--primary); padding: 15px; border-radius: 8px; margin-top: 10px; }
        .total-row { display: flex; justify-content: space-between; align-items: center; font-size: 1.2rem; font-weight: 800; color: var(--primary); border-top: 1px solid #ccc; padding-top: 10px; margin-top: 10px; }
        .approvals-container { display: flex; gap: 10px; margin-top: 30px; flex-wrap: wrap; justify-content: center; }
        .stamp-box { flex: 1; border: 2px solid #ddd; border-radius: 8px; padding: 10px; text-align: center; min-width: 140px; max-width: 180px; }
        .stamp-box.approved { border-color: #198754; background: #f0fff4; }
        .stamp-box.rejected { border-color: #dc3545; background: #fff5f5; }
        .stamp-role { font-size: 0.75rem; color: #666; text-transform: uppercase; font-weight: bold; margin-bottom: 5px; }
        .stamp-name { font-weight: 700; font-size: 0.9rem; margin-bottom: 3px; color: #000; }
        .stamp-date { font-size: 0.75rem; color: #888; }
        .stamp-status { display: inline-block; padding: 2px 8px; border-radius: 15px; font-size: 0.7rem; font-weight: bold; margin-top: 5px; border: 2px solid; }
        .stamp-box.approved .stamp-status { color: #198754; border-color: #198754; transform: rotate(-5deg); }
        .stamp-box.rejected .stamp-status { color: #dc3545; border-color: #dc3545; transform: rotate(-5deg); }
        .footer { margin-top: 50px; border-top: 1px solid #eee; padding-top: 20px; text-align: center; font-size: 0.8rem; color: #999; }
        .fab-print { position: fixed; bottom: 30px; left: 30px; background: var(--primary); color: white; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.3); cursor: pointer; z-index: 1000; border: none; font-size: 1.5rem; }
        @media print { body { background: white; padding: 0; } .page { width: 100%; box-shadow: none; margin: 0; border: none; } .fab-print { display: none; } }
    </style>
</head>
<body>

    <button onclick="window.print()" class="fab-print"><i class="fas fa-print"></i></button>

    <div class="page">
        <header class="header">
           <div class="company-info">
                <?php 
                    // Determine Company Name based on n13
                    $company_code = isset($req['n13']) ? trim($req['n13']) : '1';
                    
                    if ($company_code == '2') {
                        // Company 2
                        $company_ar = "مكتب الدكتور صالح الجربوع";
                        $company_en = "Dr. Saleh Al-Jarboa Office";
                    } else {
                        // Company 1 (Default)
                        $company_ar = "شركة مرسوم للتحصيل للديون";
                        $company_en = "Marsom Debt Collection Co.";
                    }
                ?>
                
                <h2><?= $company_ar ?></h2>
            
            </div>
            <div class="doc-meta">
                <h1>نموذج انتداب</h1>
                <h1 style="font-size:1.2rem; opacity:0.7;">Mandate Request</h1>
                <div class="badge">Reference: #<?= $req['id'] ?></div>
            </div>
        </header>

        <div class="section-title">بيانات الموظف (Employee Details)</div>
        <div class="info-grid">
            <div class="info-box"><span class="info-label">الاسم الكامل</span><span class="info-value"><?= $req['subscriber_name'] ?></span></div>
            <div class="info-box"><span class="info-label">الرقم الوظيفي (ID)</span><span class="info-value"><?= $req['emp_code'] ?? $req['employee_id'] ?></span></div>
            <div class="info-box"><span class="info-label">القسم / الإدارة</span><span class="info-value"><?= $req['department'] ?></span></div>
            <div class="info-box"><span class="info-label">المسمى الوظيفي</span><span class="info-value"><?= $req['job_tag'] ?></span></div>
        </div>

        <div class="section-title">تفاصيل المهمة (Trip Details)</div>
        <div class="info-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="info-box"><span class="info-label">تاريخ البداية</span><span class="info-value"><?= $req['start_date'] ?></span></div>
            <div class="info-box"><span class="info-label">تاريخ النهاية</span><span class="info-value"><?= $req['end_date'] ?></span></div>
            <div class="info-box"><span class="info-label">المدة</span><span class="info-value"><?= $req['duration_days'] ?> أيام</span></div>
        </div>

        <table class="data-table">
            <thead>
                <tr><th>من (From)</th><th>إلى (To)</th><th>المسافة</th><th>وسيلة النقل</th></tr>
            </thead>
            <tbody>
                <?php if(!empty($destinations)): foreach($destinations as $d): ?>
                <tr>
                    <td><?= $d['from_city'] ?></td>
                    <td><?= $d['to_city'] ?></td>
                    <td><?= $d['distance_km'] ?> كم</td>
                    <td>
                        <?php 
                        $mode = $d['leg_mode'] ?? 'road';
                        echo ($mode == 'air') ? 'طيران (Air)' : 'سيارة (Car)'; 
                        ?>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="4" style="text-align: center;">لا توجد وجهات مسجلة</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="info-box mt-3" style="background: #f9f9f9;">
            <span class="info-label">الغرض من المهمة</span>
            <span class="info-value" style="font-weight: 500;"><?= $req['reason'] ?></span>
        </div>

        <div class="section-title">المستحقات المالية (Financial Entitlements)</div>
        <div class="finance-box">
            <div class="row" style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span>بدل الانتداب (Allowance):</span>
                <strong><?= number_format($req['allowance_amount'], 2) ?> SAR</strong>
            </div>
            <div class="row" style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span>تعويض الوقود (Fuel):</span>
                <strong><?= number_format($req['road_fuel_amount'], 2) ?> SAR</strong>
            </div>
            <?php if($req['ticket_amount'] > 0): ?>
            <div class="row" style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span>تذاكر الطيران (Tickets):</span>
                <strong><?= number_format($req['ticket_amount'], 2) ?> SAR</strong>
            </div>
            <?php endif; ?>
            <div class="total-row">
                <span>الإجمالي المستحق (Total):</span>
                <span><?= number_format($req['total_amount'], 2) ?> SAR</span>
            </div>
        </div>

        <div class="section-title">سجل الاعتمادات (Approval Workflow)</div>
        <div class="approvals-container">
            
            <div class="stamp-box approved">
                <div class="stamp-role">مقدم الطلب</div>
                <div class="stamp-name"><?= $req['subscriber_name'] ?></div>
                <div class="stamp-date"><?= $req['request_date'] ?></div>
                <div class="stamp-status">SUBMITTED</div>
            </div>

            <?php foreach($timeline as $log): 
                $statusClass = '';
                $statusText = $log['status'];
                if($log['status'] == 'Approved') { $statusClass = 'approved'; $statusText = 'APPROVED'; }
                if($log['status'] == 'Rejected') { $statusClass = 'rejected'; $statusText = 'REJECTED'; }
                
                // --- NEW ROLE MAPPING ---
                $roleName = "Direct Manager";
                if(in_array($log['approver_id'], ['2784', '2774'])) $roleName = "HR Specialist";
                elseif(in_array($log['approver_id'], ['2833'])) $roleName = "HR Manager"; // Example Title for 2833
                elseif(in_array($log['approver_id'], ['1693', '2909'])) $roleName = "Finance Manager";
            ?>
            <div class="stamp-box <?= $statusClass ?>">
                <div class="stamp-role"><?= $roleName ?></div>
                <div class="stamp-name"><?= $log['approver_name'] ?: 'Approver' ?></div>
                <?php if($log['action_date']): ?>
                    <div class="stamp-date"><?= date('Y-m-d', strtotime($log['action_date'])) ?></div>
                    <div class="stamp-status"><?= $statusText ?></div>
                <?php else: ?>
                    <div class="stamp-status" style="border-color:orange; color:orange;">PENDING</div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

        </div>
        
        <?php if($req['status'] == 'Rejected' && !empty($req['rejection_reason'])): ?>
        <div style="border: 2px solid #dc3545; background:#fff5f5; padding:15px; border-radius:8px; margin-top:20px; color:#dc3545;">
            <strong>سبب الرفض (Rejection Reason):</strong> <?= $req['rejection_reason'] ?>
        </div>
        <?php endif; ?>

        <div class="footer">
            <p>تم استخراج هذا المستند آلياً من نظام شركة الابتكار المتقدمة. لا يحتاج إلى توقيع يدوي.</p>
            <p>Generated on <?= date('Y-m-d H:i:s') ?></p>
            <div style="font-family: 'Courier New', Courier, monospace; letter-spacing: 5px; margin-top: 10px;">
                ||| || ||| || ||| |||| || |||
            </div>
        </div>
    </div>

</body>
</html>