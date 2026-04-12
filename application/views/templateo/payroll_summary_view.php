<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($page_title ?? 'Payroll Summaries') ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f8f9fa; }
        .summary-card-link { text-decoration: none; color: inherit; }
        .summary-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            height: 100%;
        }
        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border-color: #007bff;
        }
        .card-period {
            font-size: 1.4rem;
            font-weight: 700;
            color: #001f3f;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        .card-company {
            font-size: 1.1rem;
            color: #FF8C00;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #007bff;
        }
        .info-row {
            display: flex; justify-content: space-between; font-size: 1rem; margin-bottom: 0.75rem;
        }
        .info-label { color: #6c757d; }
        .info-value { font-weight: 700; }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0 text-dark"><?= html_escape($page_title) ?></h1>
        <a href="<?= site_url('users1/main_hr1'); ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>العودة للرئيسية</a>
    </div>

    <?php if (empty($payroll_summaries)): ?>
        <div class="alert alert-info text-center">
            <h4>لا توجد بيانات مسير رواتب محفوظة حتى الآن.</h4>
            <p>يمكنك حفظ البيانات من صفحة مسير الرواتب أولاً.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php 
            // Define Company Names Mapping
            $company_map = [
                '1' => 'شركة مرسوم لتحصيل الديون',
                '2' => 'مكتب د. صالح الجربوع للمحاماة'
            ];
            ?>

            <?php foreach($payroll_summaries as $period): ?>
                <?php 
                    // 1. Get Company ID from DB
                    $companyId = $period['company_name']; // This contains '1', '2', or '0'
                    
                    // 2. Map ID to Name
                    if (isset($company_map[$companyId])) {
                        $compName = $company_map[$companyId];
                    } elseif ($companyId == '0' || empty($companyId)) {
                        $compName = 'موظفين غير مصنفين / أخرى';
                    } else {
                        $compName = $companyId; // Fallback if it's a name we don't know
                    }
                    
                    // 3. Determine Sheet Name Display
                    $sheetName = !empty($period['sheet_name']) ? $period['sheet_name'] : ('مسير رقم ' . $period['sheet_id']);
                    
                    // 4. Create the Combined Title
                    $combinedTitle = $compName; 
                    
                    // 5. Report URL
                    $reportUrl = site_url('users1/processed_payroll_report?month=' . urlencode($period['sheet_id']) . '&company_code=' . urlencode($companyId));
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="summary-card position-relative">
                        <a href="<?= $reportUrl ?>" class="summary-card-link">
                            <div class="card-company text-center">
                                <i class="fas fa-building ms-2"></i>
                                <?= html_escape($compName) ?>
                            </div>

                            <div class="card-period text-center" style="font-size: 1rem; color: #666; border-bottom: none; margin-bottom: 15px;">
                                <?= html_escape($sheetName) ?>
                            </div>
                            
                            <hr>

                            <div class="info-row">
                                <span class="info-label">فترة المسير</span>
                                <span class="info-value text-primary">مسير #<?= $period['sheet_id'] ?></span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">عدد الموظفين</span>
                                <span class="info-value"><?= $period['employee_count'] ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">صافي الرواتب</span>
                                <span class="info-value text-success fs-5"><?= number_format((float)$period['total_net_salary'], 2) ?></span>
                            </div>
                        </a>
                        
<?php 
$hr_publishers = ['1835', '2230', '2515', '2774', '2784', '2901'];
if(in_array($this->session->userdata('username'), $hr_publishers)): 
?>                            <?php 
                            $CI =& get_instance();
                            $CI->load->model('hr_model');
                            $is_published = $CI->hr_model->is_payroll_published($period['sheet_id']);
                            ?>
                            <div class="mt-3 border-top pt-3 text-center">
                                <?php if($is_published): ?>
                                    <button class="btn btn-success w-100" disabled><i class="fas fa-check-double me-1"></i> تم الإرسال للموظفين</button>
                                <?php else: ?>
                                    <button class="btn btn-primary w-100" onclick="publishPayroll('<?= $period['sheet_id'] ?>', this)">
                                        <i class="fas fa-paper-plane me-1"></i> إرسال للموظفين
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
function publishPayroll(monthKey, btn) {
    if(!confirm('هل أنت متأكد من إرسال مسيرات الرواتب لشهر ' + monthKey + ' لكافة الشركات؟')) return;
    
    $(btn).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...');

    $.post('<?= site_url("users1/publish_payroll_action") ?>', {
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>',
        'month_key': monthKey
    }, function(res) {
        const data = (typeof res === 'object') ? res : JSON.parse(res);
        if(data.status === 'success') {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
            $(btn).prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> إرسال للموظفين');
        }
    }).fail(function() {
        alert('فشل الاتصال بالخادم.');
        $(btn).prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> إرسال للموظفين');
    });
}
</script>
</body>
</html>