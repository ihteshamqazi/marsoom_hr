<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلب انتداب ومهمة عمل</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        :root {
            --marsom-blue: #001f3f;
            --marsom-orange: #FF8C00;
            --glass-bg: rgba(255, 255, 255, 0.95);
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, var(--marsom-blue) 0%, #34495e 50%, var(--marsom-orange) 100%);
            background-size: 400% 400%;
            animation: gradientAnimation 20s ease infinite;
            min-height: 100vh;
            color: #333;
            overflow-x: hidden;
            padding-bottom: 50px;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .particles { position: fixed; inset: 0; z-index: -1; pointer-events: none; }
        .particle { position: absolute; background: rgba(255, 140, 0, 0.15); clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%); animation: float 25s infinite ease-in-out; opacity: 0; }
        
        .main-container { max-width: 1000px; margin: 40px auto; padding: 0 15px; position: relative; z-index: 10; }

        .page-title {
            font-family: 'El Messiri', sans-serif; font-weight: 700; color: white; text-align: center;
            margin-bottom: 30px; font-size: 2.2rem; text-shadow: 0 4px 10px rgba(0,0,0,0.3);
            position: relative; padding-bottom: 15px;
        }
        .page-title::after {
            content: ''; position: absolute; bottom: 0; left: 50%; transform: translateX(-50%);
            width: 80px; height: 4px; background: linear-gradient(90deg, var(--marsom-blue), var(--marsom-orange)); border-radius: 2px;
        }

        .glass-card {
            background: var(--glass-bg); backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 16px;
            padding: 25px; margin-bottom: 25px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .section-header {
            border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px; color: var(--marsom-blue);
        }
        .section-header i { color: var(--marsom-orange); font-size: 1.4rem; }
        .section-header h4 { margin: 0; font-weight: 700; font-family: 'El Messiri', sans-serif; }

        .form-label { font-weight: 600; color: #444; margin-bottom: 5px; font-size: 0.9rem; }
        .form-control, .form-select { border-radius: 8px; padding: 10px; border: 1px solid #ddd; transition: 0.3s; }
        .form-control:focus, .form-select:focus { border-color: var(--marsom-orange); box-shadow: 0 0 0 3px rgba(255, 140, 0, 0.15); }

        .btn-submit {
            background: var(--marsom-orange); border: none; color: white;
            padding: 15px; border-radius: 10px; font-weight: 700; width: 100%;
            transition: all 0.3s; font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(255, 140, 0, 0.3);
        }
        .btn-submit:hover { background: #e07b00; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(224, 123, 0, 0.4); }

        /* System Decision Box */
        .system-decision-box {
            background: #e7f1ff; border: 2px dashed #0d6efd; padding: 20px;
            border-radius: 12px; text-align: center; margin: 20px 0; display: none;
        }
        
        .city-row { background: #fdfdfd; padding: 15px; border-radius: 10px; border: 1px solid #e9ecef; margin-bottom: 10px; position: relative; }
        
        /* Select2 Fix */
        .select2-container--bootstrap-5 .select2-selection { border-radius: 8px; padding: 8px; }
        
    </style>
</head>
<body>

<div class="particles">
    <div class="particle" style="width:40px; height:40px; left:10%; top:20%;"></div>
    <div class="particle" style="width:70px; height:70px; left:80%; top:60%;"></div>
</div>

<div class="main-container">
    <h1 class="page-title">طلب انتداب ومهمة عمل</h1>

    <form id="mandateForm" enctype="multipart/form-data">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

        <div class="glass-card">
            <div class="section-header">
                <i class="fas fa-calendar-alt"></i>
                <h4>البيانات الأساسية</h4>
            </div>
            
            <?php if(isset($is_hr) && $is_hr == true): ?>
            <div class="p-3 mb-4 rounded border border-2 border-primary" style="background: rgba(13, 110, 253, 0.05);">
                <label class="form-label text-primary fw-bold">
                    <i class="fas fa-user-shield me-2"></i> خاص بمسؤولي الموارد البشرية (HR Only)
                </label>
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <label class="small text-muted mb-1">تقديم الطلب نيابة عن موظف:</label>
                        <select name="on_behalf_emp_id" id="emp_select" class="form-select select2-emp" onchange="checkPolicy()">
                            <option value="">-- انا صاحب الطلب (Myself) --</option>
                            <?php if(isset($all_employees)): ?>
                                <?php foreach($all_employees as $emp): ?>
                                    <option value="<?= $emp['employee_id'] ?>">
                                        <?= $emp['subscriber_name'] ?> (<?= $emp['employee_id'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <small class="text-danger d-block mt-3">
                            <i class="fas fa-info-circle"></i> سيتم احتساب البدلات بناءً على مستوى الموظف المختار.
                        </small>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">القسم</label>
                    <select name="department" class="form-select" required>
                        <option value="">-- القسم المختار --</option>
                        <?php if(isset($departments_list) && !empty($departments_list)): ?>
                            <?php foreach($departments_list as $dept): ?>
                                <option value="<?= $dept['n1'] ?>"><?= $dept['n1'] ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="General">General Department</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">تاريخ البداية</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" required onchange="calcDuration()">
                </div>
                <div class="col-md-6">
                    <label class="form-label">تاريخ العودة</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" required onchange="calcDuration()">
                </div>
                <div class="col-md-12 text-center mt-2">
                     <span class="badge bg-light text-dark border p-2 fs-6">المدة: <span id="duration_span" class="text-primary fw-bold">0</span> يوم</span>
                </div>
                <div class="col-md-12">
                    <label class="form-label">الغرض من الزيارة</label>
                    <textarea name="reason" class="form-control" rows="3" required placeholder="يرجى كتابة شرح مفصل لمهمة العمل..."></textarea>
                </div>
            </div>
        </div>

        <div class="glass-card">
            <div class="section-header">
                <i class="fas fa-route"></i>
                <h4>المسافة والقرار الآلي</h4>
            </div>

            <label class="mb-3 fw-bold d-block text-primary">حدد خط السير لحساب المسافة:</label>
            
            <div id="destinations_container"></div>
            
            <button type="button" class="btn btn-outline-primary w-100 dashed-btn mt-2" onclick="addCityRow()">
                <i class="fas fa-plus"></i> إضافة وجهة أخرى
            </button>

            <div id="ticket_input_group" style="display:none;" class="mt-4 p-3 border border-danger border-2 rounded bg-light">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <label class="form-label text-danger fw-bold mb-0"><i class="fas fa-ticket-alt me-2"></i>قيمة التذكرة (ذهاب وعودة)</label>
                        <small class="text-muted d-block mb-2">المسافة تتجاوز 250 كم، يرجى إدخال سعر التذكرة (اختياري).</small>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="number" step="0.01" name="ticket_amount" id="ticket_amount" class="form-control border-danger text-center fw-bold fs-5" placeholder="0.00" onkeyup="checkPolicy()" onchange="checkPolicy()">
                            <span class="input-group-text">SAR</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                <span class="text-muted">إجمالي المسافة المحسوبة:</span>
                <strong class="fs-4 text-primary"><span id="total_km_display">0</span> كم</strong>
                <input type="hidden" name="total_km_calc" id="total_km_calc">
            </div>

            <div id="decision_box" class="system-decision-box">
                <div id="decision_icon" class="decision-icon"></div>
                <h4 id="decision_title" class="fw-bold mb-2"></h4>
                <div id="decision_desc" class="mb-2 text-muted"></div>
                <div id="allowance_preview" class="mt-3"></div>
            </div>
        </div>

        <div class="glass-card">
            <div class="section-header">
                <i class="fas fa-bullseye"></i>
                <h4>الأهداف والمرفقات</h4>
            </div>
            <div class="mb-3">
                <label class="form-label">أهداف الزيارة</label>
                <div id="goals_container">
                    <input type="text" name="goals[]" class="form-control mb-2" placeholder="الهدف الأول" required>
                </div>
                <button type="button" class="btn btn-sm btn-secondary" onclick="addGoal()">+ هدف إضافي</button>
            </div>
            
            <div class="mb-2">
                <label class="form-label text-danger fw-bold">المرفقات (Attachments) *</label>
                <div class="alert alert-light border small">
                    <i class="fas fa-info-circle"></i> يرجى إرفاق ملف واحد على الأقل في الحقل الأول.
                </div>

                <div class="row g-2">
                    <div class="col-md-12">
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light fw-bold text-primary">1</span>
                            <input type="file" name="attachment1" id="att1" class="form-control file-input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light">2</span>
                            <input type="file" name="attachment2" class="form-control file-input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light">3</span>
                            <input type="file" name="attachment3" class="form-control file-input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fas fa-paper-plane me-2"></i> إرسال الطلب للمدير المباشر
        </button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// --- DATA: CITIES & DISTANCE ---
const cities = [
    "الرياض", "جدة", "مكة المكرمة", "المدينة المنورة", "الدمام", "الخبر", "الظهران", "الجبيل", 
    "الهفوف", "المبرز", "الطائف", "أبها", "خميس مشيط", "بريدة", "عنيزة", "حائل", "تبوك", 
    "جازان", "نجران", "الباحة", "سكاكا", "عرعر", "ينبع", "بيشة", "الرس", "حفر الباطن", "الخرج"
];

const distMatrix = {
    "الرياض-جدة": 950,
    "الرياض-مكة المكرمة": 870,
    "الرياض-المدينة المنورة": 840,
    "الرياض-الدمام": 400,
    "الرياض-الخبر": 415,
    "الرياض-الظهران": 405,
    "الرياض-الجبيل": 470,
    "الرياض-الهفوف": 330,
    "الرياض-الطائف": 780,
    "الرياض-أبها": 1060,
    "الرياض-خميس مشيط": 1040,
    "الرياض-بريدة": 340,
    "الرياض-عنيزة": 320,
    "الرياض-الرس": 400,
    "الرياض-حائل": 640,
    "الرياض-تبوك": 1300,
    "الرياض-جازان": 1150,
    "الرياض-نجران": 950,
    "الرياض-الباحة": 890,
    "الرياض-سكاكا": 1050,
    "الرياض-عرعر": 1000,
    "الرياض-بيشة": 670,
    "الرياض-حفر الباطن": 490,
    "الرياض-الخرج": 80,
    "جدة-مكة المكرمة": 80,
    "جدة-المدينة المنورة": 420,
    "جدة-الطائف": 170,
    "جدة-أبها": 650,
    "جدة-خميس مشيط": 680,
    "جدة-جازان": 710,
    "جدة-ينبع": 330,
    "جدة-تبوك": 1000,
    "جدة-الباحة": 400,
    "جدة-بيشة": 550,
    "جدة-نجران": 900,
    "جدة-الدمام": 1350,
    "مكة المكرمة-المدينة المنورة": 450,
    "مكة المكرمة-الطائف": 90,
    "مكة المكرمة-أبها": 620,
    "مكة المكرمة-الباحة": 300,
    "مكة المكرمة-ينبع": 400,
    "المدينة المنورة-ينبع": 230,
    "المدينة المنورة-تبوك": 680,
    "المدينة المنورة-حائل": 430,
    "المدينة المنورة-بريدة": 520,
    "الدمام-الخبر": 25,
    "الدمام-الظهران": 15,
    "الدمام-الجبيل": 95,
    "الدمام-الهفوف": 150,
    "الدمام-المبرز": 145,
    "الدمام-حفر الباطن": 480,
    "الخبر-الجبيل": 110,
    "الخبر-الهفوف": 140,
    "الهفوف-المبرز": 5,
    "أبها-خميس مشيط": 30,
    "أبها-جازان": 200,
    "أبها-نجران": 260,
    "أبها-الباحة": 330,
    "أبها-بيشة": 270,
    "خميس مشيط-نجران": 230,
    "خميس مشيط-جازان": 230,
    "جازان-نجران": 470,
    "الباحة-الطائف": 220,
    "الباحة-بيشة": 250,
    "بريدة-عنيزة": 30,
    "بريدة-الرس": 80,
    "بريدة-حائل": 280,
    "بريدة-حفر الباطن": 350,
    "بريدة-المدينة المنورة": 520,
    "حائل-تبوك": 650,
    "حائل-سكاكا": 380,
    "حائل-حفر الباطن": 580,
    "تبوك-سكاكا": 470,
    "تبوك-عرعر": 570,
    "سكاكا-عرعر": 190,
    "حفر الباطن-عرعر": 550,
    "حفر الباطن-الجبيل": 380
};

$(document).ready(function() { 
    $('.select2-emp').select2({ theme: 'bootstrap-5', placeholder: 'Select Employee', dir: 'rtl' });
    addCityRow(); // Add first row on load
});

function getDist(c1, c2) {
    if(!c1 || !c2 || c1 === c2) return 0;
    let k1 = c1 + "-" + c2;
    if(distMatrix[k1]) return distMatrix[k1];
    let k2 = c2 + "-" + c1;
    if(distMatrix[k2]) return distMatrix[k2];
    return 0;
}

function calcDuration() {
    const d1 = $('#start_date').val();
    const d2 = $('#end_date').val();
    
    if(d1 && d2) {
        const startDate = new Date(d1);
        const endDate = new Date(d2);
        const diffTime = Math.abs(endDate - startDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        $('#duration_span').text(diffDays);
        checkPolicy();
    }
}

function addCityRow() {
    let opts = '<option value="">-- اختر --</option>';
    cities.forEach(c => opts += `<option value="${c}">${c}</option>`);
    
    $('#destinations_container').append(`
        <div class="city-row card p-3 mb-2 shadow-sm border-light">
            <div class="d-flex justify-content-between mb-2">
                 <h6 class="fw-bold text-primary mb-0" style="font-size: 0.9rem;">تفاصيل الرحلة</h6>
                 <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" onchange="toggleCustomCity(this)">
                    <label class="form-check-label small fw-bold">إدخال يدوي (مدينة غير موجودة)</label>
                </div>
            </div>

            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="small text-muted">من</label>
                    <select name="from_city[]" class="form-select select-city select-standard" onchange="autoCalcRow(this)">${opts}</select>
                    <input type="text" name="from_city_manual[]" class="form-control input-manual d-none" placeholder="اكتب المدينة">
                </div>
                <div class="col-md-3">
                    <label class="small text-muted">إلى</label>
                    <select name="to_city[]" class="form-select select-city select-standard" onchange="autoCalcRow(this)">${opts}</select>
                    <input type="text" name="to_city_manual[]" class="form-control input-manual d-none" placeholder="اكتب المدينة">
                </div>
                <div class="col-md-2">
                    <label class="small text-muted">المسافة (Km)</label>
                    <input type="number" name="dist_km[]" class="form-control km-input" placeholder="0" onchange="manualDistChange(this)">
                </div>
                <div class="col-md-3">
                    <label class="small text-muted">وسيلة النقل</label>
                    <select name="leg_mode[]" class="form-select mode-select" onchange="checkTicketVisibility(); checkPolicy();">
                        <option value="road">سيارة (Road)</option>
                        <option value="air">طائرة (Air)</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger btn-sm w-100 mb-1" onclick="$(this).closest('.city-row').remove(); updateAll();">
                         <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `);
    
    $('.select-city:not(.select2-hidden-accessible)').select2({ theme: 'bootstrap-5', dir: 'rtl', width: '100%' });
}

function toggleCustomCity(checkbox) {
    let row = $(checkbox).closest('.city-row');
    
    if($(checkbox).is(':checked')) {
        row.find('.select-standard').next('.select2-container').hide(); 
        row.find('.select-standard').addClass('d-none').val('').trigger('change'); 
        row.find('.input-manual').removeClass('d-none').prop('required', true).val('');
        row.find('.km-input').prop('readonly', false).val('').focus();
    } else {
        row.find('.select-standard').next('.select2-container').show();
        row.find('.select-standard').removeClass('d-none');
        row.find('.input-manual').addClass('d-none').prop('required', false).val('');
        row.find('.km-input').val('');
    }
}

function autoCalcRow(el) {
    const row = $(el).closest('.city-row');
    const from = row.find('.select-standard').eq(0).val();
    const to = row.find('.select-standard').eq(1).val();
    
    const km = getDist(from, to);
    const input = row.find('.km-input');

    if(km > 0) {
        input.val(km).prop('readonly', true);
    } else if(from && to) {
        input.prop('readonly', false).attr('placeholder', 'أدخل المسافة');
    }
    
    applyModeLogic(row);
    updateAll();
}

function manualDistChange(el) {
    const row = $(el).closest('.city-row');
    applyModeLogic(row);
    updateAll();
}

function applyModeLogic(row) {
    let km = parseFloat(row.find('.km-input').val()) || 0;
    let modeSelect = row.find('.mode-select');

    if(km > 250) {
        modeSelect.val('air');
    } else {
        modeSelect.val('road');
    }
}

function updateAll() {
    let total = 0;
    $('.km-input').each(function() { total += parseFloat($(this).val()) || 0; });
    $('#total_km_display').text(total);
    $('#total_km_calc').val(total);
    checkTicketVisibility();
    checkPolicy();
}

function checkTicketVisibility() {
    let showTicket = false;
    $('.mode-select').each(function() {
        if($(this).val() == 'air') showTicket = true;
    });

    if(showTicket) {
        if($('#ticket_input_group').is(':hidden')) $('#ticket_input_group').slideDown();
    } else {
        $('#ticket_input_group').slideUp();
        $('#ticket_amount').val(0);
    }
}

// --- FIXED BACKEND POLICY CHECK ---
function checkPolicy() {
    let legs = [];
    
    $('.city-row').each(function() {
        let row = $(this);
        let isManual = row.find('.input-manual').is(':visible');
        
        let from = isManual ? row.find('input[name="from_city_manual[]"]').val() : row.find('select[name="from_city[]"]').val();
        let to   = isManual ? row.find('input[name="to_city_manual[]"]').val()   : row.find('select[name="to_city[]"]').val();
        let km   = parseFloat(row.find('.km-input').val()) || 0;
        let mode = row.find('.mode-select').val();
        
        if((from || to) && km > 0) {
            legs.push({ from: from, to: to, dist: km, mode: mode });
        }
    });

    let ticketPrice = parseFloat($('#ticket_amount').val()) || 0;
    let empId = $('#emp_select').val();
    let days = parseInt($('#duration_span').text()) || 1;

    if(legs.length > 0) {
        $('#decision_box').hide();
        
        $.ajax({
            url: '<?= site_url("users1/check_policy_ajax") ?>',
            type: 'POST',
            data: {
                legs: legs,
                days: days,
                ticket_amount: ticketPrice,
                on_behalf_emp_id: empId,
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(data) {
                console.log('Server Response:', data);
                
                if(data.status !== 'success') {
                    Swal.fire('Error', data.message || 'Server returned error', 'error');
                    return;
                }
                
                $('#decision_box').fadeIn();
                
                let html = '<ul class="list-group list-group-flush small mb-2">';
                if(data.breakdown && data.breakdown.length > 0) {
                    data.breakdown.forEach(leg => {
                        let from = leg.from || 'undefined';
                        let to = leg.to || 'undefined';
                        let km = leg.km || leg.distance || 0;
                        let mode = leg.mode || 'road';
                        let badge = mode == 'air' ? '<span class="badge bg-info text-dark ms-2">Air</span>' : '<span class="badge bg-warning text-dark ms-2">Road</span>';
                        html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>${from} → ${to}</span>
                                    <span>${km} km ${badge}</span>
                                </li>`;
                    });
                } else {
                    html += '<li class="list-group-item text-muted">No leg details available</li>';
                }
                html += '</ul>';

                $('#decision_title').text('تفاصيل الاستحقاق (Breakdown)');
                $('#decision_desc').html(html);
                
                function parseNumber(str) {
                    if (!str) return 0;
                    return parseFloat(String(str).replace(/,/g, '').replace(/[^\d.-]/g, '')) || 0;
                }
                
                let dailyAllowance = parseNumber(data.daily_allowance);
                let totalAllowance = parseNumber(data.total_allowance);
                let ticketAmount = parseNumber(data.ticket_amount);
                let fuelCost = parseNumber(data.fuel_cost) || 0;
                let grandTotal = parseNumber(data.grand_total);
                let baseRate = parseNumber(data.base_rate);
                let multiplier = data.multiplier || 0;
                let totalKm = data.total_km || 0;
                let policyNote = data.policy_note || '';
                
                if (grandTotal === 0) {
                    grandTotal = totalAllowance + ticketAmount + fuelCost;
                }
                
                $('#allowance_preview').html(`
                    <div class="border-top pt-2 mt-2">
                        <div class="small text-muted mb-2 fw-bold text-primary">${policyNote}</div>
                        
                        ${multiplier > 0 ? `
                        <div class="d-flex justify-content-between mb-1">
                            <span>Base Rate:</span>
                            <strong>${baseRate.toFixed(2)} SAR</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Multiplier:</span>
                            <strong>× ${multiplier}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Daily Allowance:</span>
                            <strong>${dailyAllowance.toFixed(2)} SAR/day</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Duration:</span>
                            <strong>${data.days} day(s)</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 border-top pt-2 fw-bold">
                            <span>Total Allowance:</span>
                            <strong class="text-primary">${totalAllowance.toFixed(2)} SAR</strong>
                        </div>
                        ` : `
                        <div class="d-flex justify-content-between mb-2">
                            <span>Allowance:</span>
                            <strong class="text-muted">لا يوجد بدل انتداب</strong>
                        </div>
                        `}
                        
                        ${fuelCost > 0 ? `
                        <div class="d-flex justify-content-between mb-2 ${multiplier > 0 ? 'border-top pt-2' : ''}">
                            <span>Fuel Cost (70 SAR/100km):</span>
                            <strong class="text-warning">${fuelCost.toFixed(2)} SAR</strong>
                        </div>
                        ` : ''}
                        
                        ${ticketAmount > 0 ? `
                        <div class="d-flex justify-content-between mb-2 ${fuelCost > 0 ? '' : (multiplier > 0 ? 'border-top pt-2' : '')}">
                            <span>Air Ticket:</span>
                            <strong class="text-danger">${ticketAmount.toFixed(2)} SAR</strong>
                        </div>
                        ` : ''}
                        
                        <div class="d-flex justify-content-between text-success fs-5 fw-bold mt-2 pt-2 border-top">
                            <span>GRAND TOTAL:</span>
                            <strong>${grandTotal.toFixed(2)} SAR</strong>
                        </div>
                    </div>
                `);
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error:", xhr.responseText);
                Swal.fire('Error', 'Could not connect to server. Please try again.', 'error');
            }
        });
    } else {
        $('#decision_box').hide();
    }
}

function addGoal() {
    $('#goals_container').append('<div class="input-group mb-2"><input type="text" name="goals[]" class="form-control" placeholder="هدف إضافي"><button class="btn btn-outline-danger" onclick="$(this).parent().remove()">X</button></div>');
}

// --- SUBMISSION LOGIC ---
$('#mandateForm').on('submit', function(e){
    e.preventDefault();

    var sDate = $('#start_date').val();
    var eDate = $('#end_date').val();
    
    // DEBUG: Log date values
    console.log('Start Date:', sDate);
    console.log('End Date:', eDate);
    console.log('Form Data:', $(this).serialize());

    if(!sDate || !eDate) {
        Swal.fire('تنبيه', 'يرجى اختيار تواريخ البداية والنهاية', 'warning');
        return;
    }

    var hasFile = false;
    $('.file-input').each(function() { 
        if(this.files.length > 0) hasFile = true; 
    });

    if (!hasFile) {
        Swal.fire({
            icon: 'warning',
            title: 'مرفقات مطلوبة',
            text: 'يرجى إرفاق ملف واحد على الأقل في الحقل الأول',
            confirmButtonColor: '#001f3f'
        });
        return;
    }

    var formData = new FormData(this);
    formData.append('total_km_calc', $('#total_km_calc').val());
    formData.append('ticket_amount', $('#ticket_amount').val() || 0);
    
    // DEBUG: Check FormData contents
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    $.ajax({
        url: '<?= site_url("users1/submit_mandate_ajax") ?>',
        type: 'POST',
        data: formData,
        processData: false, 
        contentType: false, 
        dataType: 'json',
        beforeSend: function() {
            Swal.fire({
                title: 'جاري الإرسال...',
                text: 'يرجى الانتظار بينما يتم رفع الملفات',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        },
        success: function(res) {
            console.log('Server Response:', res);
            if(res.status === 'success') {
                Swal.fire({
                    icon: 'success', 
                    title: 'تم بنجاح', 
                    text: res.message, 
                    confirmButtonText: 'حسناً', 
                    confirmButtonColor: '#001f3f'
                }).then(() => { 
                    window.location.href = '<?= site_url("users1/my_mandates") ?>'; 
                });
            } else {
                Swal.fire('خطأ', res.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.log("AJAX Error:", xhr.responseText);
            Swal.fire('خطأ تقني', 'فشل الاتصال: ' + xhr.status + ' - ' + error, 'error');
        }
    });
});
</script>

</body>
</html>