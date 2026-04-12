<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلب تأمين طبي</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    
    <style>
        :root { --marsom-blue: #001f3f; --marsom-orange: #FF8C00; }
        body { font-family: 'Tajawal'; background: #f0f2f5; color: #333; padding-bottom: 80px; }
        
        .main-container { max-width: 950px; margin: 40px auto; }
        
        /* Selection Cards */
        .selector-card {
            background: white; border: 2px solid transparent; border-radius: 16px;
            padding: 30px; text-align: center; cursor: pointer; transition: 0.3s;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05); position: relative; overflow: hidden;
        }
        .selector-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .selector-card.active { border-color: var(--marsom-orange); background: #fffcf5; }
        .selector-card.active::after {
            content: '\f00c'; font-family: "Font Awesome 6 Free"; font-weight: 900;
            position: absolute; top: 10px; left: 10px; color: var(--marsom-orange); font-size: 1.2rem;
        }
        .sel-icon { font-size: 3rem; color: var(--marsom-blue); margin-bottom: 15px; transition:0.3s; }
        .selector-card.active .sel-icon { color: var(--marsom-orange); transform: scale(1.1); }

        /* Family Member Card Styling */
        .family-card {
            background: white; border-radius: 12px; padding: 25px; margin-bottom: 15px;
            border-left: 5px solid var(--marsom-blue); box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: relative; animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
        
        .btn-remove-fam {
            position: absolute; top: 15px; left: 15px; color: #dc3545; background: #fff0f0;
            border: none; width: 35px; height: 35px; border-radius: 50%; transition: 0.2s;
        }
        .btn-remove-fam:hover { background: #dc3545; color: white; }

        /* Submit Bar */
        .submit-bar {
            position: fixed; bottom: 0; left: 0; right: 0; background: white; padding: 15px;
            box-shadow: 0 -5px 20px rgba(0,0,0,0.1); z-index: 100; text-align: center;
        }
        .btn-marsom {
            background: var(--marsom-blue); color: white; padding: 12px 50px; border-radius: 50px;
            font-weight: 700; font-size: 1.1rem; border: none; transition: 0.3s;
        }
        .btn-marsom:hover { background: #003366; transform: scale(1.05); }
        .star { color: red; margin-right: 3px; }
    </style>
</head>
<body>

<div class="main-container">
    <div class="text-center mb-5">
        <h2 class="fw-bold" style="color:var(--marsom-blue)"><i class="fas fa-file-medical-alt"></i> طلب تأمين طبي</h2>
        <p class="text-muted">يرجى اختيار نوع التغطية المطلوبة وتعبئة البيانات بدقة</p>
    </div>

    <form id="insuranceForm" enctype="multipart/form-data">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="request_type" id="request_type" value="self">

        <h5 class="fw-bold mb-3 px-2">1. نوع التغطية <span class="star">*</span></h5>
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="selector-card active" onclick="setType('self', this)">
                    <div class="sel-icon"><i class="fas fa-user-shield"></i></div>
                    <h4>مـوظـف (Myself)</h4>
                    <small class="text-muted">تغطية طبية للموظف فقط</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="selector-card" onclick="setType('family', this)">
                    <div class="sel-icon"><i class="fas fa-users-medical"></i></div>
                    <h4>عـائـلـة (Family)</h4>
                    <small class="text-muted">إضافة الزوجة والأبناء</small>
                </div>
            </div>
        </div>

        <div id="family_section" style="display:none;">
            <div class="d-flex justify-content-between align-items-center mb-3 px-2">
                <h5 class="fw-bold m-0">2. بيانات التابعين</h5>
                <button type="button" class="btn btn-outline-primary rounded-pill px-4 fw-bold" onclick="addFamilyMember()">
                    <i class="fas fa-plus"></i> إضافة فرد
                </button>
            </div>
            
            <div id="family_container">
                </div>
            <div class="mb-4"></div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-paperclip"></i> المرفقات المطلوبة <span class="star">*</span></h5>
            <div class="alert alert-info py-2 small">
                <i class="fas fa-info-circle"></i> يرجى إرفاق صور الهوية/الإقامة للتابعين، أو كرت العائلة، وأي تقارير طبية ضرورية.
            </div>
            <div class="mb-2">
                <label class="form-label">تحميل الملفات (PDF, JPG, PNG)</label>
                <input type="file" name="attachments[]" id="attachments" class="form-control" multiple required accept=".pdf,.jpg,.png,.jpeg">
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 mb-5">
            <label class="form-label fw-bold"><i class="fas fa-pen"></i> ملاحظات إضافية (اختياري)</label>
            <textarea name="reason" class="form-control bg-light border-0" rows="3" placeholder="أذكر أي تفاصيل طبية هامة أو استفسارات..."></textarea>
        </div>

        <div class="submit-bar">
            <button type="submit" class="btn-marsom">
                إرسال الطلب <i class="fas fa-paper-plane ms-2"></i>
            </button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function setType(type, el) {
    $('#request_type').val(type);
    $('.selector-card').removeClass('active');
    $(el).addClass('active');
    
    if(type === 'family') {
        $('#family_section').slideDown();
        if($('#family_container').children().length === 0) addFamilyMember();
    } else {
        $('#family_section').slideUp();
    }
}

function addFamilyMember() {
    let html = `
    <div class="family-card">
        <button type="button" class="btn-remove-fam shadow-sm" onclick="$(this).parent().remove()"><i class="fas fa-times"></i></button>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="form-floating">
                    <input type="text" name="fam_name[]" class="form-control" placeholder="Name Ar" required>
                    <label>الاسم (عربي) <span class="star">*</span></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-floating">
                    <input type="text" name="fam_name_en[]" class="form-control" placeholder="Name En" required style="direction:ltr">
                    <label>الاسم (English) <span class="star">*</span></label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-floating">
                    <select name="fam_rel[]" class="form-select" required>
                        <option value="Wife">زوجة (Wife)</option>
                        <option value="Son">ابن (Son)</option>
                        <option value="Daughter">ابنة (Daughter)</option>
                    </select>
                    <label>صلة القرابة</label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-floating">
                    <input type="date" name="fam_dob[]" class="form-control" required>
                    <label>تاريخ الميلاد <span class="star">*</span></label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-floating">
                    <input type="number" name="fam_nid[]" class="form-control" placeholder="ID" required>
                    <label>رقم الهوية <span class="star">*</span></label>
                </div>
            </div>
        </div>
    </div>`;
    $('#family_container').append(html);
}

$('#insuranceForm').on('submit', function(e){
    e.preventDefault();
    
    // Validate Family Section
    if($('#request_type').val() === 'family' && $('#family_container').children().length === 0) {
        Swal.fire('تنبيه', 'الرجاء إضافة بيانات التابعين', 'warning'); 
        return;
    }

    // Validate Attachments (Explicit check)
    var fileInput = document.getElementById('attachments');
    if (fileInput.files.length === 0) {
        Swal.fire('تنبيه', 'المرفقات مطلوبة. الرجاء إرفاق الهوية/الإقامة.', 'warning');
        return;
    }

    // Use FormData for File Upload
    var formData = new FormData(this);

    // Show Loading
    Swal.fire({
        title: 'جاري الإرسال...',
        text: 'يرجى الانتظار بينما يتم رفع الملفات',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    $.ajax({
        url: '<?= site_url("users1/submit_insurance_ajax") ?>',
        type: 'POST',
        data: formData,
        processData: false, // Important for FormData
        contentType: false, // Important for FormData
        dataType: 'json',
        success: function(res) {
            if(res.status === 'success') {
                Swal.fire({
                    icon: 'success', 
                    title: 'تم الإرسال', 
                    text: res.message, 
                    confirmButtonColor: '#001f3f'
                }).then(() => window.location.href = '<?= site_url("users1/insurance_approvals") ?>');
            } else {
                Swal.fire('خطأ', res.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('خطأ', 'حدث خطأ أثناء الاتصال بالخادم: ' + error, 'error');
        }
    });
});
</script>
</body>
</html>