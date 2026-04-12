    <?php /* application/views/templateo/request_new.php */ ?>
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
    <meta charset="UTF-8">
    <title>طلب جديد</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Fonts + Icons + Bootstrap -->
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
      :root{
        --marsom-blue:#001f3f; --marsom-orange:#FF8C00; --glass-bg:rgba(255,255,255,.08);
        --glass-border:rgba(255,255,255,.2); --glass-shadow:rgba(0,0,0,.5);
      }
      html,body{font-family:'Tajawal',system-ui,-apple-system,"Segoe UI",Arial,sans-serif; background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%); background-size:400% 400%; animation:grad 20s ease infinite; color:#111; overflow-x:hidden}
      @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}

      .particles{position:fixed;inset:0;z-index:-1;overflow:hidden}
      .particle{position:absolute;background:rgba(255,140,0,.12);clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%); animation:float 25s ease-in-out infinite;opacity:0;filter:blur(2px)}
      .particle:nth-child(even){background:rgba(0,31,63,.12)}
      .particle:nth-child(1){width:40px;height:40px;left:8%;top:20%;animation-duration:18s}
      .particle:nth-child(2){width:70px;height:70px;left:25%;top:50%;animation-duration:22s;animation-delay:2s}
      .particle:nth-child(3){width:55px;height:55px;left:40%;top:12%;animation-duration:25s;animation-delay:5s}
      .particle:nth-child(4){width:80px;height:80px;left:60%;top:70%;animation-duration:20s;animation-delay:8s}
      .particle:nth-child(5){width:60px;height:60px;left:80%;top:30%;animation-duration:23s;animation-delay:10s}
      .particle:nth-child(6){width:45px;height:45px;left:5%;top:85%;animation-duration:19s;animation-delay:3s}
      .particle:nth-child(7){width:90px;height:90px;left:70%;top:5%;animation-duration:28s;animation-delay:6s}
      .particle:nth-child(8){width:35px;height:35px;left:90%;top:40%;animation-duration:17s;animation-delay:12s}
      .particle:nth-child(9){width:75px;height:75px;left:20%;top:75%;animation-duration:21s;animation-delay:1s}
      .particle:nth-child(10){width:65px;height:65px;left:50%;top:90%;animation-duration:24s;animation-delay:4s}
      @keyframes float{0%{transform:translate(0,0) rotate(0);opacity:0}20%{opacity:1}80%{opacity:1}100%{transform:translate(50px,-100vh) rotate(360deg);opacity:0}}

      .main-container{padding:28px 14px}
      .page-title{font-family:'El Messiri',sans-serif;font-weight:700;color:#fff;text-align:center;margin-bottom:8px;font-size:2.4rem;text-shadow:0 4px 14px rgba(0,0,0,.35)}
      .page-sub{color:#f8fafc;text-align:center;margin-bottom:24px;opacity:.85}

      .card-glass{background:rgba(255,255,255,.92); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,.25); box-shadow:0 12px 36px rgba(0,0,0,.18); border-radius:16px }
      .section-title{font-weight:800;border-right:4px solid var(--marsom-orange);padding:.4rem .75rem;background:#f7f9fc;margin:18px 0 12px}

      .btn-brand{background:var(--marsom-orange);border-color:var(--marsom-orange);color:#fff}
      .btn-brand:hover{opacity:.95}
      .form-text.muted{color:#6b7280}

      .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
      .top-actions a{background:rgba(255,255,255,.14);border:1px solid var(--glass-border);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
      .top-actions a:hover{background:rgba(255,255,255,.22);color:#ffd7a3}

      .req-type-pill{display:flex;flex-wrap:wrap;gap:.5rem}
      .req-type-pill .form-check{background:#f3f6fb;border:1px solid #e3e8f0;border-radius:999px;padding:.35rem .8rem;margin:0}
      .req-type-pill .form-check-input{margin-left:.45rem;margin-right:0}
      .req-panel{display:none}
      .req-panel.active{display:block;animation:fadeIn .35s ease}
      @keyframes fadeIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}

      .mini-hint{font-size:.86rem;color:#6b7280}
      .input-group-text{min-width:42px;justify-content:center}
.flatpickr-time {
          font-size: 1rem !important; /* Increase font size */
      }
      
      .flatpickr-time .numInputWrapper {
          height: 40px; /* Make the input boxes taller */
      }
      
      .flatpickr-time .numInput {
          font-size: 1.1rem !important; /* Increase number font size */
          height: 100%;
      }
      
      .flatpickr-time .flatpickr-am-pm {
          height: 40px; /* Make AM/PM button taller */
          line-height: 40px; /* Center the text vertically */
          font-size: 1.1rem !important;
      }
      
      .flatpickr-time .arrowUp,
      .flatpickr-time .arrowDown {
          height: 20px; /* Make arrows taller */
      }
    </style>
    </head>
    <body>

    <!-- خلفية متحرّكة -->
    <div class="particles">
      <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
      <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
    </div>

    <!-- أزرار أعلى -->
    <div class="top-actions">
      <button class="btn btn-secondary" onclick="location.href='<?= site_url('users/logout'); ?>'"><i class="fas fa-right-from-bracket me-2"></i> خروج</button>
      <a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
      <a href="<?php echo site_url('users1/main_emp'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
    </div>

    <div class="main-container container-lg">
      <h1 class="page-title">طلب جديد</h1>
      <div class="page-sub">اختر نوع الطلب ثم عبّئ الحقول الظاهرة أدناه بحسب نوعه</div>

      <div class="card card-glass">
         <div class="card-body">
       <?php if($this->session->flashdata('error_message')): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <?php echo $this->session->flashdata('error_message'); ?>
            </div>
        <?php endif; ?>
        <div id="formErrorAlert" class="alert alert-danger d-none"></div>

  <form method="post" novalidate="novalidate"  id="requestForm" action="<?php echo site_url('users1/add_new_order'); ?>" enctype="multipart/form-data">  

     


        

          <!-- اختيار نوع الطلب -->
          <div class="section-title">نوع الطلب</div>
          <div class="req-type-pill mb-3">
             <div class="form-check form-check-inline">
      <?php if(in_array(1, $allowed_codes)): ?>
      <input class="form-check-input" type="radio" name="request_type" id="type_resign" value="resign">
      <label class="form-check-label" for="type_resign"><i class="fa-solid fa-person-walking-arrow-right ms-1"></i> استقالة</label>
      <?php endif; ?>
    </div>

  <!--  <div class="form-check form-check-inline">
      <?php if(in_array(2, $allowed_codes)): ?>
      <input class="form-check-input" type="radio" name="request_type" id="type_fp" value="fingerprint">
      <label class="form-check-label" for="type_fp"><i class="fa-solid fa-fingerprint ms-1"></i> تصحيح بصمة</label>
      <?php endif; ?>
    </div>
-->
    <div class="form-check form-check-inline">
      <?php if(in_array(3, $allowed_codes)): ?>
      <input class="form-check-input" type="radio" name="request_type" id="type_ot" value="overtime">
      <label class="form-check-label" for="type_ot"><i class="fa-solid fa-clock-rotate-left ms-1"></i> عمل إضافي</label>
      <?php endif; ?>
    </div>
   

    <div class="form-check form-check-inline">
      <?php if(in_array(4, $allowed_codes)): ?>
      <input class="form-check-input" type="radio" name="request_type" id="type_exp" value="expenses">
      <label class="form-check-label" for="type_exp"><i class="fa-solid fa-file-invoice-dollar ms-1"></i> مصاريف مالية</label>
      <?php endif; ?>
    </div>

      <div class="form-check form-check-inline">
      <?php if(in_array(5, $allowed_codes)): ?>
      <input class="form-check-input" type="radio" name="request_type" id="type_vac" value="vacation">
      <label class="form-check-label" for="type_vac"><i class="fa-solid fa-umbrella-beach ms-1"></i> إجازة</label>
      <?php endif; ?>
    </div>

    <div class="form-check form-check-inline">
      <?php if(in_array(6, $allowed_codes)): ?>
      <input class="form-check-input" type="radio" name="request_type" id="type_asset" value="asset">
      <label class="form-check-label" for="type_asset"><i class="fa-solid fa-box-archive ms-1"></i> طلب عُهدة</label>
      <?php endif; ?>
    </div>

    <div class="form-check form-check-inline">
      <?php if(in_array(7, $allowed_codes)): ?>
      <input class="form-check-input" type="radio" name="request_type" id="type_letter" value="letter">
      <label class="form-check-label" for="type_letter"><i class="fa-solid fa-file-signature ms-1"></i> طلب خطاب</label>
      <?php endif; ?>
    </div>
   <div class="form-check form-check-inline">
  <?php if(in_array(9, $allowed_codes)): ?>
  <input class="form-check-input" type="radio" name="request_type" id="type_work_mission" value="work_mission">
  <label class="form-check-label" for="type_work_mission"><i class="fa-solid fa-person-running ms-1"></i>مهمة عمل</label>
  <?php endif; ?>
</div> 
<?php if (isset($show_permission_request) && $show_permission_request): ?>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="request_type" id="type_permission" value="permission">
        <label class="form-check-label" for="type_permission"><i class="fa-solid fa-clock ms-1"></i> استئذان</label>
    </div>
<?php endif; ?>
          </div>

          <!-- بيانات عامة بسيطة (اختياري) -->
          <div class="row g-3">
             
            <div class="col-sm-6 col-md-8">
              <label class="form-label">ملاحظات عامة</label>
              <input type="text" name="note" class="form-control" placeholder="اختياري">
            </div>
          </div>
           <?php if (isset($is_hr_user) && $is_hr_user): ?>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="employee_id" class="form-label fw-bold text-primary">تقديم الطلب بالنيابة عن الموظف</label>
               <select name="employee_id" id="employee_id" class="form-select">
    <option value="">-- اختر الموظف (أو اتركه فارغاً لتقديم طلب لنفسك) --</option>
    <?php if (!empty($employees)): ?>
        <?php foreach ($employees as $emp): ?>
            <option value="<?php echo htmlspecialchars($emp['username']); ?>" 
                    data-join-date="<?php echo htmlspecialchars($emp['joining_date']); ?>"> <?php echo htmlspecialchars($emp['name']) . ' (' . htmlspecialchars($emp['username']) . ')'; ?>
            </option>
        <?php endforeach; ?>
    <?php endif; ?>
</select>
                <div class="form-text">هذا الخيار متاح لك لأنك من مسؤولي الموارد البشرية.</div>
            </div>
          </div>
          <hr class="mb-4">
          <?php endif; ?>
          <!-- ====== استقالة ====== -->
          <div class="req-panel mt-3" id="panel_resign">
            <div class="section-title">استقالة</div>
            <div class="row g-3">
              <div class="col-sm-6">
                <label class="form-label">تاريخ آخر يوم عمل</label>
<input type="date" name="resign[last_day]" id="resignDate" class="form-control">              </div>
             <div class="col-sm-6">
    <label class="form-label">سبب الاستقالة</label>
    <select name="resign[reason]" class="form-select">
        <option value="">اختر</option>

        <option value="الحصول على وظيفة جديدة">الحصول على وظيفة جديدة</option>
        <option value="بيئة العمل غير مريحة">بيئة العمل غير مريحة</option>
        <option value="إكمال الدراسة">إكمال الدراسة</option>
        <option value="الدوامات وساعات العمل">الدوامات وساعات العمل</option>
        <option value="تغيير المسار المهني">تغيير المسار المهني</option>
        <option value="مشاكل إدارية">مشاكل إدارية</option>
        <option value="استقالة الموظف">استقالة الموظف</option>

        <option value="إنهاء العقد خلال فترة التجربة (مادة 53)"
                title="إنهاء عقد بموجب فترة التجربة بموجب المادة 53 من قانون العمل.">
                إنهاء العقد خلال فترة التجربة (مادة 53)
        </option>
        
        <option value="إنهاء العقد غير محدد المدة (مادة 75)"
                title="بإرادة أحد الطرفين المنفردة في العقود غير المحددة المدة وفقا لما ورد في المادة 75 من نظام العمل.">
                إنهاء العقد غير محدد المدة (مادة 75)
        </option>

        <option value="إنهاء العقد بسبب غير مشروع (مادة 77)"
                title="انهاء العقد من أحد الطرفين لسبب غير مشروع، ويتضمن العقد تعويضا محددا بموجب المادة 77 من قانون العمل.">
                إنهاء العقد بسبب غير مشروع (مادة 77)
        </option>

        <option value="وفاة أو عجز الموظف (مادة 79)"
                title="وفاة الموظف بموجب المادة 79 من قانون العمل.">
                وفاة الموظف (مادة 79)
        </option>
        
        <option value="عجز العامل عن العمل - مهني (مادة 79)"
                title="عجز العامل عن العمل (عجز مهني) بموجب المادة 79 من قانون العمل.">
                عجز العامل عن العمل - مهني (مادة 79)
        </option>

        <option value="عجز العامل عن العمل - غير مهني (مادة 79)"
                title="عجز العامل عن العمل (عجز غير مهني) بموجب المادة 79 من قانون العمل.">
                عجز العامل عن العمل - غير مهني (مادة 79)
        </option>
        
        <option value="فسخ العقد من صاحب العمل (مادة 80)"
                title="فسخ العقد من قبل صاحب العمل لحالة في المادة 80.">
                فسخ العقد من صاحب العمل (مادة 80)
        </option>

        <option value="عدم تأدية العامل لالتزاماته الجوهرية"
                title="لم يؤدِّ العامل التزاماته الجوهرية المترتبة على عقد العمل أو لم يطع الأوامر المشروعة أو لم يراعِ عمداً التعليمات – التي أعلن عنها صاحب العمل في مكان ظاهر – الخاصة بسلامة العمل والعمال رغم إنذاره كتابة.">
                عدم تأدية الالتزامات الجوهرية
        </option>
        
        <option value="الغياب دون سبب مشروع"
                title="تغيب العامل دون سبب مشروع أكثر من ثلاثين يوماً خلال السنة العقدية الواحدة أو أكثر من خمسة عشر يوماً متتالية، على أن يسبق الفصل إنذار كتابي من صاحب العمل للعامل بعد غيابه عشرين يوماً في الحالة الأولى وانقطاعه عشرة أيام في الحالة الثانية.">
                الغياب دون سبب مشروع
        </option>

        <option value="فسخ العقد من العامل (خارج حالات مادة 81)"
                title="فسخ العقد من قبل العامل لغير الحالات الواردة في المادة 81.">
                فسخ العقد من العامل (خارج حالات مادة 81)
        </option>
        
        <option value="إنهاء العقد بسبب الزواج - للمرأة (مادة 87)"
                title="إنهاء المرأة العاملة العقد بسبب الزواج وفقاً للمادة 87 من نظام العمل.">
                إنهاء العقد بسبب الزواج - للمرأة (مادة 87)
        </option>

        <option value="انتهاء العقد أو باتفاق الطرفين"
                title="إنتهاء العقد أو باتفاق الطرفين على إنهاء العقد.">
                انتهاء العقد أو باتفاق الطرفين
        </option>

        <option value="ترك العمل لقوة قاهرة"
                title="ترك العامل العمل نتيجة لقوة قاهرة.">
                ترك العمل لقوة قاهرة
        </option>
        
    </select>
</div>
              <div class="col-12">
                <label class="form-label">تفاصيل إضافية (اختياري)</label>
                <textarea name="resign[details]" rows="3" class="form-control" placeholder="يمكنك توضيح السبب بتفصيل أكثر"></textarea>
              </div>
              <div class="col-12">
                <label class="form-label">مرفق الاستقالة (PDF/صورة)</label>

                 


                <input type="file" name="resign[file]" id="resign_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
              </div>
            </div>
          </div>
<div class="req-panel mt-3" id="panel_work_mission">
      <div class="section-title">طلب مهمة عمل</div>
      <div class="row g-3">

                <div class="col-md-6">
          <label for="mission_date" class="form-label required">تاريخ المهمة</label>
          <input type="date" class="form-control" id="mission_date" name="mission[date]" required>
        </div>

                <div class="col-md-6">
          <label for="mission_type" class="form-label required">نوع المهمة</label>
          <select name="mission[type]" id="mission_type" class="form-select" required>
            <option value="">-- اختر نوع المهمة --</option>
            <option value="Official">عمل رسمي (Official)</option>
            <option value="Personal">عمل شخصي (Personal)</option>
          </select>
        </div>
        
                <div class="col-md-6">
          <label for="mission_start_time" class="form-label required">وقت الخروج</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-regular fa-clock"></i></span>
            <input type="text" class="form-control time-picker-24" id="mission_start_time" name="mission[start_time]" placeholder="HH:MM (مثال: 10:00)" required>
          </div>
        </div>

                <div class="col-md-6">
          <label for="mission_end_time" class="form-label required">وقت العودة المتوقع</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-regular fa-clock"></i></span>
            <input type="text" class="form-control time-picker-24" id="mission_end_time" name="mission[end_time]" placeholder="HH:MM (مثال: 12:30)" required>
          </div>
        </div>

                <div class="col-12">
          <label for="mission_note" class="form-label required">السبب/ملاحظات المهمة</label>
          <textarea name="mission[note]" id="mission_note" rows="3" class="form-control" placeholder="اشرح سبب طلب المهمة بالتفصيل (مثل: زيارة العميل س لتوقيع العقد)" required></textarea>
        </div>
      </div>
     </div>
          <!-- ====== تصحيح بصمة ====== -->
          <div class="req-panel mt-3" id="panel_fingerprint">
            <div class="section-title">تصحيح بصمة</div>
            <div class="row g-3">
              <div class="col-sm-4">
                <label class="form-label">تاريخ التصحيح</label>
<input type="date" name="fp[date]" id="fpDate" class="form-control">              </div>
              <div class="col-sm-8 d-flex align-items-end">
                <div class="mini-hint">حدّد ما تريد تصحيحه من أوقات الحضور والانصراف المسجّلة.</div>
              </div>

              <div class="col-12">
                <div class="border rounded p-3">
                  <div class="fw-bold mb-2"><i class="fa-regular fa-circle-check ms-1"></i> تصحيح الحضور</div>
                  <div class="row g-2">
                    <div class="col-sm-3">
                     <div class="input-group">
            <span class="input-group-text"><i class="fa-regular fa-clock"></i></span>
                        <input type="text" class="form-control time-picker-24" name="fp[in_time]" placeholder="HH:MM (مثال: 08:30)">
           </div>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="fp[in_note]" placeholder="ملاحظة على الدخول (اختياري)">
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <div class="border rounded p-3">
                  <div class="fw-bold mb-2"><i class="fa-regular fa-circle-check ms-1"></i> تصحيح الانصراف</div>
                  <div class="row g-2">
                    <div class="col-sm-3">
                      <div class="input-group">
            <span class="input-group-text"><i class="fa-regular fa-clock"></i></span>
                        <input type="text" class="form-control time-picker-24" name="fp[out_time]" placeholder="HH:MM (مثال: 17:00)">
           </div>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="fp[out_note]" placeholder="ملاحظة على الانصراف (اختياري)">
                    </div>
                  </div>
                  <div class="form-text mt-2 muted"><i class="fa-solid fa-triangle-exclamation ms-1"></i> إن لم يوجد سجل، يرجى توضيح السبب في الملاحظات.</div>
                </div>
              </div>

              <div class="col-md-6">
  <label class="form-label">سبب التصحيح</label>
  <select name="fp[reason]" id="fpReason" class="form-select">
    <option value="">اختر السبب</option>
    <option>نسيان بصمة</option>
    <option>مشكلة في تطبيق الجوال</option>
    <option>مشكلة في جهاز البصمة</option>
    <option>مشكلة في الاتصال بالانترنت</option>
    <option>العمل عن بعد</option>
    <option>تبديل الدوام مع موظف اخر</option>
    <option>عمل اضافي بعد فترة بصمة الخروج</option>
    <option>زيارة موقع خارجي</option>
    <option>اخرى</option>
  </select>
</div>
<div class="col-md-6">
  <label class="form-label">تفاصيل السبب</label>
  <input type="text" name="fp[details]" id="fpDetails" class="form-control" placeholder="اشرح باختصار سبب التصحيح">
</div>



              <div class="col-12">
                <label class="form-label">مرفق داعم (اختياري)</label>
                <input type="file" name="fp[file]" id="fp_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
              </div>
            </div>
          </div>

          <!-- ====== عمل إضافي ====== -->
          <div class="req-panel mt-3" id="panel_overtime">
    <div class="section-title">طلب عمل إضافي</div>
    <div class="row g-3">
        <div class="col-sm-4">
            <label class="form-label">النوع</label>
            <select name="ot[type]" class="form-select" id="otType">
                <option value="single">يوم واحد</option>
                <option value="range">فترة</option>
            </select>
        </div>

        <div class="col-sm-4 ot-date-group" id="group_single">
            <label class="form-label">تاريخ</label>
            <input type="date" name="ot[date]" id="ot_date_single" class="form-control ot-calc-trigger">
        </div>

        <div class="col-sm-4 ot-date-group d-none" id="group_range_from">
            <label class="form-label">من تاريخ</label>
            <input type="date" name="ot[from]" id="ot_date_from" class="form-control ot-calc-trigger">
        </div>
        <div class="col-sm-4 ot-date-group d-none" id="group_range_to">
            <label class="form-label">إلى تاريخ</label>
            <input type="date" name="ot[to]" id="ot_date_to" class="form-control ot-calc-trigger">
        </div>

       <div class="col-sm-4">
    <label class="form-label">عدد الساعات</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-regular fa-clock"></i></span>
        <!-- Change type from "number" to "text" -->
        <input type="text" name="ot[hours]" id="ot_hours" class="form-control" placeholder="00:00" readonly>
    </div>
    <div id="ot_calc_hint" class="form-text text-primary" style="font-size: 0.85rem;">
        <i class="fa-solid fa-calculator"></i> سيتم احتساب الساعات تلقائياً بناءً على البصمة
    </div>
</div>

        <div class="col-sm-4">
            <label class="form-label">هل مدفوع؟</label>
            <select name="ot[paid]" class="form-select">
                <option value="1">عمل إضافي مدفوع</option>
                <option value="0">غير مدفوع</option>
            </select>
        </div>
        <div class="col-12 d-none" id="ot_details_container">
    <label class="form-label text-primary"><i class="fa-solid fa-list-check"></i> تفاصيل الحضور اليومية</label>
    <div class="table-responsive">
        <table class="table table-bordered table-sm text-center" style="font-size:0.9rem;">
            <thead class="table-light">
                <tr>
                    <th>التاريخ</th>
                    <th>دخول</th>
                    <th>خروج</th>
                    <th>ساعات العمل</th>
                    <th>المطلوب</th>
                    <th>الإضافي (ساعات)</th>
                    <th>الإضافي (دقيقة)</th>
                </tr>
            </thead>
            <tbody id="ot_details_body">
                </tbody>
        </table>
    </div>
</div>
        <div class="col-12">
            <label class="form-label">السبب</label>
            <input type="text" name="ot[reason]" class="form-control" placeholder="أدخل السبب">
        </div>

        <div class="col-12">
            <label class="form-label">مرفق (اختياري)</label>
            <input type="file" name="ot[file]" id="ot_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        </div>
    </div>
</div>

          <!-- ====== مصاريف مالية ====== -->
          <div class="req-panel mt-3" id="panel_expenses">
            <div class="section-title">طلب مصاريف مالية</div>

            <div id="expItems">
                  <div class="row g-2 align-items-end exp-row">
  <div class="col-12 col-md-2">
    <label class="form-label">اسم العنصر</label>
    <input type="text" class="form-control" name="exp[0][item]" placeholder="مثال: طابعة">
  </div>
  <div class="col-12 col-md-2">
    <label class="form-label">المبلغ</label>
    <div class="input-group">
      <span class="input-group-text">﷼</span>
      <input type="number" step="0.01" min="0" class="form-control" name="exp[0][amount]" placeholder="0.00">
    </div>
  </div>
  <div class="col-6 col-md-2">
    <label class="form-label">التاريخ</label>
    <input type="date" class="form-control" name="exp[0][date]">
  </div>
  <div class="col-6 col-md-4">
    <label class="form-label">الوصف</label>
    <input type="text" class="form-control" name="exp[0][desc]" placeholder="أضف الوصف">
  </div>
  <div class="col-10 col-md-2">
  <label class="form-label">مرفق</label>
  <input type="file" class="form-control" name="exp[file]" id="exp_file" accept=".pdf,.jpg,.jpeg,.png">
</div>
  <div class="col-2 text-center">
    <button type="button" class="btn btn-outline-danger btn-sm remove-exp d-none"><i class="fa fa-times"></i></button>
  </div>
</div>

               
            </div>

            <div class="mt-2">
              <button type="button" class="btn btn-outline-primary btn-sm" id="addExp"><i class="fa fa-plus"></i> إضافة عنصر</button>
            </div>

            <div class="mt-3">
              <label class="form-label">السبب</label>
              <input type="text" class="form-control" name="exp_reason" placeholder="سبب الطلب">
            </div>
          </div>

          <!-- ====== إجازة ====== -->
        <div class="req-panel mt-3" id="panel_vacation">
    <div class="section-title">طلب إجازة</div>

    <div class="row g-3">
        <div class="col-md-6">
<label for="delegation_employee_id" class="form-label fw-bold">تفويض المهام <span class="text-danger">*</span></label>
                    <select name="vac[delegation_employee_id]" id="delegation_employee_id" class="form-select">
                        <option value="">-- اختر موظف لتفويضه اثناء اجازتك --</option>
                        <?php if (!empty($employees)): ?>
                            <?php foreach ($employees as $emp): ?>
                                <?php // Prevent user from delegating to themselves
                                if ($emp['username'] == $this->session->userdata('username')) continue; 
                                ?>
                                <option value="<?php echo htmlspecialchars($emp['username']); ?>">
                                    <?php echo htmlspecialchars($emp['name']) . ' (' . htmlspecialchars($emp['username']) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <div class="form-text">سيتم منع الموظف المفوض من أخذ إجازة خلال هذه الفترة.</div>
                </div>
        <div class="col-md-4">
            <label class="form-label">نوع الإجازة</label>
            <select name="vac[main_type]" class="form-select" id="vacMainType" required>
                <option value="">-- اختر نوع الإجازة --</option>
                <?php if (!empty($leave_types)): ?>
                    <?php foreach ($leave_types as $type): ?>
                        <option value="<?php echo htmlspecialchars($type['slug']); ?>">
                            <?php echo htmlspecialchars($type['name_ar']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        
        <div class="col-md-8 d-flex align-items-end">
            <div class="alert alert-info w-100 p-2 mb-0" id="vacBalanceBox">
                <i class="fa-solid fa-wallet me-1"></i>
                رصيدك المتاح لهذا النوع: <b id="vacBalanceDisplay" class="mx-1">--</b> يوم
            </div>
        </div>
<div class="col-12" id="vacationDurationContainer"> 
        <div class="col-12">
            <label class="form-label">مدة الإجازة</label>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="vac[day_type]" id="vacDayTypeFull" value="full" checked>
                    <label class="form-check-label" for="vacDayTypeFull">
                        يوم كامل (فترة)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="vac[day_type]" id="vacDayTypeHalf" value="half">
                    <label class="form-check-label" for="vacDayTypeHalf">
                        نصف يوم
                    </label>
                </div>
            </div>
        </div>
        </div>
        <div class="row g-3" id="vacFullDayRange">
            <div class="col-sm-4">
                <label class="form-label">من تاريخ</label>
                <input type="date" name="vac[start]" class="form-control" id="vacFrom" required>
            </div>
            <div class="col-sm-4">
                <label class="form-label">إلى تاريخ</label>
                <input type="date" name="vac[end]" class="form-control" id="vacTo" required>
            </div>
            <div class="col-sm-4 d-flex align-items-end pb-2">
                <div id="vacDaysMsg" class="fw-bold"></div>
                <input type="hidden" name="vac[days_count]" id="vacDaysCount" value="0">
            </div>
        </div>

        <div class="row g-3 d-none" id="vacHalfDayFields">
            <div class="col-sm-4">
                <label class="form-label">في تاريخ</label>
                <input type="date" name="vac[half_date]" class="form-control" id="vacHalfDate">
            </div>
            <div class="col-sm-4">
                <label class="form-label">الفترة</label>
                <select name="vac[half_period]" class="form-select" id="vacHalfPeriod">
                    <option value="am">صباحي</option>
                    <option value="pm">مسائي</option>
                </select>
            </div>
        </div>
        
        <div class="col-12">
            <label class="form-label">السبب</label>
            <input type="text" name="vac[reason]" class="form-control" placeholder="أدخل سبب طلب الإجازة" required>
        </div>
       <div class="col-12">
    <label class="form-label">مرفق (اختياري) <span id="attachmentRequired" class="text-danger d-none">*</span></label>
    <input type="file" name="vac[file]" id="vacAttachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
</div>


    </div>
</div>
    <!-- ====== طلب عُهدة ====== -->
          <div class="req-panel mt-3" id="panel_asset">
            <div class="section-title">طلب عُهدة</div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">نوع العُهدة</label>
                <select name="asset[type]" class="form-select" required>
                  <option value="">اختر</option>
                  <option>حاسب محمول</option>
                  <option>جهاز جوال</option>
                  <option>بطاقة/هوية</option>
                  <option>سيارة</option>
                  <option>أخرى</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">وصف مختصر</label>
                <input type="text" name="asset[desc]" class="form-control" placeholder="اكتب وصفًا للعُهدة">
              </div>
              <div class="col-12">
                <label class="form-label">السبب</label>
                <input type="text" name="asset[reason]" class="form-control" placeholder="سبب طلب العُهدة">
              </div>
              <div class="col-12">
                <label class="form-label">مرفق (اختياري)</label>
                <input type="file" name="asset[file]" id="asset_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
              </div>
            </div>
          </div>
          <div id="panel_permission" class="req-panel">
    <div class="section-title">تفاصيل الاستئذان</div>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">تاريخ الاستئذان <span class="text-danger">*</span></label>
            <input type="date" name="perm_date" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">من الساعة <span class="text-danger">*</span></label>
            <input type="time" name="perm_start" id="perm_start" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">إلى الساعة <span class="text-danger">*</span></label>
            <input type="time" name="perm_end" id="perm_end" class="form-control" required>
        </div>
        <div class="col-md-12">
            <label class="form-label">سبب الاستئذان <span class="text-danger">*</span></label>
            <textarea name="perm_reason" class="form-control" rows="2" required></textarea>
        </div>
        <div class="col-md-12">
            <div id="perm_error" class="alert alert-danger mt-2" style="display:none;"></div>
        </div>
    </div>
</div>
          <!-- ====== طلب خطاب ====== -->
          <div class="req-panel mt-3" id="panel_letter">
            <div class="section-title">طلب خطاب</div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">نوع الخطاب</label>
                <select name="letter[type]" class="form-select" required>
                  <option value="">اختر</option>
                  <option>خطاب تعريف عربي إنجليزي</option>
                  <option>خطاب تعريف بدون راتب (عربي)</option>
                  <option>خطاب تعريف بدون راتب (إنجليزي)</option>
                  <option>خطاب تعريف مع الراتب (عربي)</option>
                  <option>خطاب تعريف مع الراتب (إنجليزي)</option>
                  <option>خطاب تحويل راتب (عربي)</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">موجّه إلى (إنجليزي)</label>
                <input type="text" name="letter[to_en]" class="form-control" placeholder="To: …">
              </div>
              <div class="col-md-6">
                <label class="form-label">موجّه إلى (عربي)</label>
                <input type="text" name="letter[to_ar]" class="form-control" placeholder="موجّه إلى: …">
              </div>
              <div class="col-12">
                <label class="form-label">السبب</label>
                <input type="text" name="letter[reason]" class="form-control" placeholder="أدخل السبب (اختياري)">
              </div>
              <div class="col-12">
                <label class="form-label">مرفق (اختياري)</label>
                <input type="file" name="letter[file]" id="letter_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-4">
                    <button type="submit" id="submitBtn" class="btn btn-brand">
                        <i class="fa fa-paper-plane ms-1"></i> إرسال
                    </button>
                    <a href="javascript:history.back()" class="btn btn-secondary"><i class="fa fa-times ms-1"></i> إلغاء</a>
                </div>

                <?php echo form_close(); ?>

            </div> </div> </div> </body>


    <!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>




<script>
document.addEventListener('DOMContentLoaded', function() {
    // =================================================================
    // SECTION 1: GLOBAL VARIABLES AND INITIALIZATION
    // =================================================================
    const PUBLIC_HOLIDAYS = <?php echo json_encode($public_holidays ?? []); ?>;
    const MANDATORY_SATURDAYS = <?php echo json_encode($saturday_assignments ?? []); ?>;
    const allEmployeeBalances = <?php echo json_encode($all_balances ?? []); ?>;
    const currentUserBalances = <?php echo json_encode($balances ?? []); ?>;
    const isHrUser = <?php echo json_encode($is_hr_user ?? false); ?>;
    const leaveTypes = <?php echo json_encode($leave_types ?? []); ?>;
    const CURRENT_USER_JOIN_DATE = "<?php echo $current_user_joining_date ?? ''; ?>";
 
    let LAST_WORKING_DAY = <?php echo json_encode($last_working_day ?? null); ?>;

    // Panel management
    const panels = {
        resign: document.getElementById('panel_resign'),
        fingerprint: document.getElementById('panel_fingerprint'),
        overtime: document.getElementById('panel_overtime'),
        expenses: document.getElementById('panel_expenses'),
        vacation: document.getElementById('panel_vacation'),
        asset: document.getElementById('panel_asset'),
        letter: document.getElementById('panel_letter'),
        work_mission: document.getElementById('panel_work_mission')
    };

    // =================================================================
    // SECTION 2: CORE FUNCTIONS
    // =================================================================

    // Helper function to select elements
    const q = (selector) => document.querySelector(selector);
    const qa = (selector) => document.querySelectorAll(selector);
    
    function getMonthsDifference(date1, date2) {
        let months;
        months = (date2.getFullYear() - date1.getFullYear()) * 12;
        months -= date1.getMonth();
        months += date2.getMonth();
        return months <= 0 ? 0 : months;
    }
    
    // =================================================================
    // NEW: PAYROLL PERIOD LOCK (Day 22 Rule)
    // =================================================================
    
    function validatePayrollPeriod(inputElement) {
        if (!inputElement.value) return true; // No date selected yet

        const selectedDate = new Date(inputElement.value);
        const today = new Date();
        const currentDayOfMonth = today.getDate(); // 1-31

        // RULE: If today is after the 22nd (e.g., 23rd onwards)
        if (currentDayOfMonth > 30) {
            
            // Calculate the Locked Period (16th Previous Month -> 15th Current Month)
            const currentYear = today.getFullYear();
            const currentMonth = today.getMonth(); // 0 = Jan, 11 = Dec

            // Start: 16th of Previous Month
            const lockStart = new Date(currentYear, currentMonth - 1, 16);
            
            // End: 15th of Current Month
            const lockEnd = new Date(currentYear, currentMonth, 15);

            // Normalize times to avoid timezone issues
            lockStart.setHours(0, 0, 0, 0);
            lockEnd.setHours(23, 59, 59, 999);
            selectedDate.setHours(12, 0, 0, 0); 

            // Check Overlap
            if (selectedDate >= lockStart && selectedDate <= lockEnd) {
                alert(`عفواً، لا يمكن اختيار هذا التاريخ.\n\nتم إغلاق مسير الرواتب للفترة من ${lockStart.toLocaleDateString('en-GB')} إلى ${lockEnd.toLocaleDateString('en-GB')} بتاريخ 30 من الشهر.`);
                
                // Clear the invalid input
                inputElement.value = ''; 
                
                // Reset calculations
                if(typeof calculateLeaveDays === 'function') {
                    calculateLeaveDays();
                }
                return false;
            }
        }
        return true;
    }

    // Apply listeners to Vacation Inputs
    const vacInputs = ['#vacFrom', '#vacTo', '#vacHalfDate'];
    
    vacInputs.forEach(selector => {
        const el = document.querySelector(selector);
        if (el) {
            el.addEventListener('change', function() {
                // 1. Check Payroll Lock
                validatePayrollPeriod(this);
                
                // 2. If valid, proceed to standard day calculation
                if(this.value && typeof calculateLeaveDays === 'function'){
                    calculateLeaveDays();
                }
            });
        }
    });
    
    // Panel management
    function showPanel(key){
        for (const k in panels){ 
            if(panels[k]) panels[k].classList.remove('active'); 
        }
        if (panels[key]) panels[key].classList.add('active');
    }

    // Employee management
    function getCurrentEmployeeId() {
        if (isHrUser && q('#employee_id') && q('#employee_id').value) {
            return q('#employee_id').value;
        }
        return '<?php echo $this->session->userdata("username"); ?>';
    }
    
    flatpickr(".time-picker-24", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        minuteIncrement: 1
    });
    
    function getBalancesForCurrentEmployee() {
        const employeeId = getCurrentEmployeeId();
        
        if (employeeId === '<?php echo $this->session->userdata("username"); ?>') {
            return currentUserBalances;
        }
        
        return allEmployeeBalances[employeeId] || {};
    }
    
    function updateLastWorkingDayForSelectedEmployee() {
        if (!isHrUser) return; // Only run for HR users

        const employeeId = getCurrentEmployeeId();
        console.log("Last working day (might be stale if HR changed employee):", LAST_WORKING_DAY);
    }
    
    // Leave type helper functions
    function getMaxAllowedDays(leaveTypeSlug) {
        console.log('Looking for leave type:', leaveTypeSlug);
        
        if (!leaveTypes || !Array.isArray(leaveTypes)) {
            console.error('leaveTypes is not defined or not an array');
            return null;
        }
        
        const leaveType = leaveTypes.find(lt => {
            console.log('Checking:', lt.slug, 'against:', leaveTypeSlug);
            return lt.slug === leaveTypeSlug;
        });
        
        console.log('Found leave type:', leaveType);
        
        if (leaveType && leaveType.default_balance) {
            const maxDays = parseInt(leaveType.default_balance, 10);
            console.log('Max allowed days:', maxDays);
            return maxDays;
        }
        
        console.log('No default balance found for:', leaveTypeSlug);
        return null;
    }

    function parseYMD(s){
        const [y,m,d] = (s||'').split('-').map(Number);
        if(!y||!m||!d) return null;
        return new Date(y, m-1, d, 12, 0, 0);
    }

    // =================================================================
    // SECTION 3: VACATION SPECIFIC FUNCTIONS
    // =================================================================

    function calculateLeaveDays() {
        const leaveType = q('#vacMainType').value;
        const fromDateStr = q('#vacFrom')?.value;
        const toDateStr = q('#vacTo')?.value;
        const vacDaysMsg = q('#vacDaysMsg');
        const vacDaysCount = q('#vacDaysCount');
        
        if (!fromDateStr || !toDateStr || toDateStr < fromDateStr) {
            if (vacDaysMsg) vacDaysMsg.textContent = '';
            if (vacDaysCount) vacDaysCount.value = 0;
            return;
        }
        
        const start = parseYMD(fromDateStr);
        const end = parseYMD(toDateStr);
        let count = 0;

        const employeeId = getCurrentEmployeeId();
        const employeeSaturdays = MANDATORY_SATURDAYS[employeeId] || [];

        let cur = new Date(start);
        while (cur <= end) {
            const day = cur.getDay(); // 0=Sun, 5=Fri, 6=Sat
            const currentDateStr = cur.toISOString().slice(0, 10);

            const isWeekend = (day === 5 || day === 6);
            const isMandatorySaturday = (day === 6) && employeeSaturdays.includes(currentDateStr);
            
            // Skip the day if it's a public holiday OR a regular weekend (but NOT a mandatory Saturday)
            if (PUBLIC_HOLIDAYS.includes(currentDateStr) || (isWeekend && !isMandatorySaturday)) {
                // Do nothing, skip this day
            } else {
                // Count the day if it's a weekday OR a mandatory Saturday
                count++;
            }
            cur.setDate(cur.getDate() + 1);
        }
        
        vacDaysMsg.innerHTML = `مجموع أيام العمل: <b class="text-primary">${count}</b> يوم`;
        if (vacDaysCount) vacDaysCount.value = count;
    }

    function updateBalanceDisplay() {
        const selectedType = q('#vacMainType').value;
        const currentBalances = getBalancesForCurrentEmployee();
        
        if (selectedType && currentBalances[selectedType]) {
            q('#vacBalanceDisplay').textContent = currentBalances[selectedType].remaining;
            
            const employeeId = getCurrentEmployeeId();
            if (employeeId === '<?php echo $this->session->userdata("username"); ?>') {
                q('#vacBalanceBox i').nextSibling.textContent = ' رصيدك المتاح لهذا النوع: ';
            } else {
                const selectedOption = q('#employee_id').options[q('#employee_id').selectedIndex];
                const employeeName = selectedOption.text.split(' (')[0];
                q('#vacBalanceBox i').nextSibling.textContent = ` رصيد ${employeeName} المتاح لهذا النوع: `;
            }
        } else {
            q('#vacBalanceDisplay').textContent = '--';
        }
        calculateLeaveDays();
    }

    function toggleVacationFields() {
        const selectedType = q('input[name="vac[day_type]"]:checked').value;
        const fullDayDiv = q('#vacFullDayRange');
        const halfDayDiv = q('#vacHalfDayFields');
        const vacDaysCountInput = q('#vacDaysCount');
        const vacDaysMsg = q('#vacDaysMsg');
        const vacAttachment = q('#vacAttachment');
        
        if (selectedType === 'half') {
            fullDayDiv.classList.add('d-none');
            halfDayDiv.classList.remove('d-none');
            
            // Set day count to 0.5
            vacDaysCountInput.value = '0.5';
            vacDaysMsg.innerHTML = `مجموع الأيام: <b class="text-danger">0.5</b> يوم`;
            
            // Make half-day fields required, remove from full-day
            q('#vacHalfDate').setAttribute('required', 'required');
            q('#vacHalfPeriod').setAttribute('required', 'required');
            q('#vacFrom').removeAttribute('required');
            q('#vacTo').removeAttribute('required');
        } else { // 'full'
            halfDayDiv.classList.add('d-none');
            fullDayDiv.classList.remove('d-none');
            
            // Make full-day fields required, remove from half-day
            q('#vacFrom').setAttribute('required', 'required');
            q('#vacTo').setAttribute('required', 'required');
            q('#vacHalfDate').removeAttribute('required');
            q('#vacHalfPeriod').removeAttribute('required');
            
            // Recalculate full days
            calculateLeaveDays();
        }
        
        // Handle attachment requirement for sick leave
        const vacationType = q('#vacMainType').value;
        if (vacationType === 'sick') {
            vacAttachment.setAttribute('required', 'required');
            q('#attachmentRequired').classList.remove('d-none');
        } else {
            vacAttachment.removeAttribute('required');
            q('#attachmentRequired').classList.add('d-none');
        }
        
        // Trigger validation after toggling
        if (typeof runValidation === 'function') {
            runValidation();
        }
    }

    function handleLeaveTypeChange() {
        const selectedType = q('#vacMainType').value;
        const vacDurationContainer = q('#vacationDurationContainer');
        const fullDayRadio = q('#vacDayTypeFull');

        // Feature 2: Show half-day option ONLY for annual leave
        if (selectedType === 'annual') {
            vacDurationContainer.classList.remove('d-none');
        } else {
            vacDurationContainer.classList.add('d-none');
            // If it's not annual, always default back to full day
            if(fullDayRadio) fullDayRadio.checked = true;
            // Trigger the change event to update field visibility
            const changeEvent = new Event('change');
            qa('input[name="vac[day_type]"]').forEach(r => r.dispatchEvent(changeEvent));
        }

        // Update balance display
        updateBalanceDisplay();
    }
    
    // =================================================================
    // SECTION 4: FORM VALIDATION FUNCTIONS - WITH REQUIRED DELEGATION VALIDATION
    // =================================================================

    function showFormErrors(errors) {
        const errorAlertBox = q('#formErrorAlert');
        if (!errorAlertBox) return;

        if (errors.length > 0) {
            const errorList = '<b>الرجاء استكمال الحقول الإلزامية التالية:</b><ul class="mb-0 mt-2"><li>' + errors.join('</li><li>') + '</li></ul>';
            errorAlertBox.innerHTML = errorList;
            errorAlertBox.classList.remove('d-none');
            errorAlertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            errorAlertBox.innerHTML = '';
            errorAlertBox.classList.add('d-none');
        }
    }

    function getSelectedRequestType() {
        const selectedRadio = q('input[name="request_type"]:checked');
        return selectedRadio ? selectedRadio.value : null;
    }

    // VALIDATION FUNCTION FOR DELEGATION EMPLOYEE FIELD - NOW REQUIRED
    function validateDelegation(errors) {
        const delegationSelect = q('#delegation_employee_id');
        const delegationValue = delegationSelect ? delegationSelect.value : '';
        
        // Check if delegation field is empty
        if (!delegationValue) {
            errors.push('حقل تفويض المهام مطلوب (يجب اختيار موظف لتفويض المهام إليه)');
            return;
        }
        
        // Validate that it's not the same as the employee making the request
        const employeeId = getCurrentEmployeeId();
        
        if (delegationValue === employeeId) {
            errors.push('لا يمكن تفويض المهام لنفس الموظف (يجب اختيار موظف آخر)');
        }
    }

    // Validation functions for each request type
    function validateResign(errors) {
        if (!q('input[name="resign[last_day]"]').value) {
            errors.push('تاريخ آخر يوم عمل (استقالة)');
        }
        if (!q('select[name="resign[reason]"]').value) {
            errors.push('سبب الاستقالة');
        }
    }

    function validateFingerprint(errors) {
        if (!q('input[name="fp[date]"]').value) {
            errors.push('تاريخ التصحيح (بصمة)');
        }
        const inTime = q('input[name="fp[in_time]"]').value;
        const outTime = q('input[name="fp[out_time]"]').value;
        if (!inTime && !outTime) {
            errors.push('يجب إدخال وقت الحضور أو الانصراف على الأقل (بصمة)');
        }
        if (!q('select[name="fp[reason]"]').value) {
            errors.push('سبب التصحيح (بصمة)');
        }
        if (!q('input[name="fp[details]"]').value.trim()) {
            errors.push('تفاصيل السبب (بصمة)');
        }
    }

    function validateOvertime(errors) {
        const otType = q('select[name="ot[type]"]').value;
        if (otType === 'single' && !q('input[name="ot[date]"]').value) {
            errors.push('تاريخ العمل الإضافي');
        }
        if (otType === 'range') {
            const from = q('input[name="ot[from]"]').value;
            const to = q('input[name="ot[to]"]').value;
            if (!from) errors.push('تاريخ بداية الفترة (عمل إضافي)');
            if (!to) errors.push('تاريخ نهاية الفترة (عمل إضافي)');
            if (from && to && to < from) {
                errors.push('تاريخ النهاية يجب أن يكون بعد تاريخ البداية (عمل إضافي)');
            }
        }
        const hours = q('input[name="ot[hours]"]').value;
        if (!hours || parseFloat(hours) <= 0) {
            errors.push('عدد الساعات يجب أن يكون رقماً صحيحاً (عمل إضافي)');
        }
        if (!q('input[name="ot[reason]"]').value.trim()) {
            errors.push('سبب العمل الإضافي');
        }
    }

    function validateExpenses(errors) {
        const expRows = qa('.exp-row');
        if (expRows.length === 0) {
            errors.push('يجب إضافة عنصر واحد على الأقل (مصاريف)');
            return;
        }

        expRows.forEach((row, index) => {
            const item = row.querySelector(`input[name="exp[${index}][item]"]`).value.trim();
            const amount = row.querySelector(`input[name="exp[${index}][amount]"]`).value;
            const date = row.querySelector(`input[name="exp[${index}][date]"]`).value;
            const file = row.querySelector('input[type="file"]').files.length;

            if (!item) errors.push(`اسم العنصر رقم ${index + 1} مطلوب (مصاريف)`);
            if (!amount || parseFloat(amount) <= 0) errors.push(`مبلغ العنصر رقم ${index + 1} يجب أن يكون أكبر من صفر (مصاريف)`);
            if (!date) errors.push(`تاريخ العنصر رقم ${index + 1} مطلوب (مصاريف)`);
            if (file === 0) errors.push(`مرفق الفاتورة للعنصر رقم ${index + 1} مطلوب (مصاريف)`);
        });

        if (!q('input[name="exp_reason"]').value.trim()) {
            errors.push('سبب طلب المصاريف');
        }
    }

    function validateAsset(errors) {
        if (!q('select[name="asset[type]"]').value) {
            errors.push('نوع العُهدة');
        }
        if (!q('input[name="asset[desc]"]').value.trim()) {
            errors.push('وصف مختصر للعُهدة');
        }
        if (!q('input[name="asset[reason]"]').value.trim()) {
            errors.push('سبب طلب العُهدة');
        }
    }

    function validateLetter(errors) {
        if (!q('select[name="letter[type]"]').value) {
            errors.push('نوع الخطاب');
        }
        const toAr = q('input[name="letter[to_ar]"]').value.trim();
        const toEn = q('input[name="letter[to_en]"]').value.trim();
        if (!toAr && !toEn) {
            errors.push('يجب تحديد الجهة الموجّه إليها الخطاب (بالعربية أو الإنجليزية)');
        }
    }
function validateWorkMission(errors) {
        const missionDate = q('input[name="mission[date]"]').value;
        const missionType = q('select[name="mission[type]"]').value;
        const startTime = q('input[name="mission[start_time]"]').value;
        const endTime = q('input[name="mission[end_time]"]').value;
        const note = q('textarea[name="mission[note]"]').value.trim();

        if (!missionDate) {
            errors.push('تاريخ المهمة مطلوب.');
        }
        if (!missionType) {
            errors.push('نوع المهمة مطلوب (رسمي أو شخصي).');
        }
        if (!startTime) {
            errors.push('وقت الخروج مطلوب.');
        }
        if (!endTime) {
            errors.push('وقت العودة المتوقع مطلوب.');
        }
        if (!note) {
            errors.push('سبب/ملاحظات المهمة مطلوبة.');
        }

        // Check if End Time is greater than Start Time (basic check)
        if (startTime && endTime) {
            // Note: This relies on browser parsing, which works for HH:MM format
            const startTimestamp = new Date(`2000/01/01 ${startTime}`).getTime();
            const endTimestamp = new Date(`2000/01/01 ${endTime}`).getTime();
            
            if (startTimestamp >= endTimestamp) {
                errors.push("يجب أن يكون وقت العودة المتوقع بعد وقت الخروج.");
            }
        }
    }
    function validateVacation(errors) {
        const vacationType = q('#vacMainType').value;
        const dayType = q('input[name="vac[day_type]"]:checked')?.value;
        
        console.log('Validating vacation - Type:', vacationType, 'Day Type:', dayType);
        
        if (!vacationType) {
            errors.push('نوع الإجازة مطلوب');
            return;
        }

        // ============================================================
        // START: 6-Month Service Rule Check
        // ============================================================
        let targetJoinDateStr = (typeof CURRENT_USER_JOIN_DATE !== 'undefined') ? CURRENT_USER_JOIN_DATE : '';

        // If HR is using the "Apply for Employee" dropdown, get that employee's date instead
        if (isHrUser && q('#employee_id') && q('#employee_id').value) {
            const selectedOption = q('#employee_id').options[q('#employee_id').selectedIndex];
            const attrDate = selectedOption.getAttribute('data-join-date');
            if (attrDate) {
                targetJoinDateStr = attrDate;
            }
        }

        if (targetJoinDateStr) {
            const joinDate = new Date(targetJoinDateStr);
            const today = new Date();
            // Normalize times to avoid issues
            today.setHours(0,0,0,0);
            joinDate.setHours(0,0,0,0);

            // Calculate exactly 6 months from the joining date
            const sixMonthsFromJoin = new Date(joinDate);
            sixMonthsFromJoin.setMonth(sixMonthsFromJoin.getMonth() + 6);

            // Logic: If today is BEFORE the 6-month mark AND the user is NOT HR -> Block
            if (today < sixMonthsFromJoin && !isHrUser && vacationType === 'annual') {
                errors.push(`عفواً، لا يمكنك تقديم طلب إجازة سنوية لأن مدة خدمتك أقل من 6 أشهر (تاريخ التعيين: ${targetJoinDateStr}).`);
            }
        }
        // ============================================================
        // END: 6-Month Service Rule Check
        // ============================================================

        // 1. Calculate requested days
        let requestedDays = 0;
        let requestedStartDateStr = null;
        
        if (dayType === 'half') {
            requestedDays = 0.5;
            requestedStartDateStr = q('#vacHalfDate').value;
            if (!requestedStartDateStr) {
                errors.push('تاريخ نصف الإجازة مطلوب');
            }
        } else {
            const fromDate = q('#vacFrom').value;
            const toDate = q('#vacTo').value;
            requestedStartDateStr = fromDate;
            
            if (!fromDate) errors.push('تاريخ بداية الإجازة مطلوب');
            if (!toDate) errors.push('تاريخ نهاية الإجازة مطلوب');
            if (fromDate && toDate && toDate < fromDate) {
                errors.push('تاريخ النهاية يجب أن يكون بعد البداية');
            }
            requestedDays = parseFloat(q('#vacDaysCount').value) || 0;
        }

        console.log('Requested days:', requestedDays);

        // Check last working day restriction for resignation cases
        if (LAST_WORKING_DAY && requestedStartDateStr && requestedStartDateStr > LAST_WORKING_DAY) {
            errors.push(`لا يمكن طلب إجازة تبدأ (${requestedStartDateStr}) بعد تاريخ آخر يوم عمل (${LAST_WORKING_DAY}).`);
        }

        // 2. Apply the correct validation rule based on leave type
        
        if (vacationType === 'annual') {
            const currentBalances = getBalancesForCurrentEmployee();
            const availableBalance = currentBalances.annual?.remaining ?? 0;
            console.log('Annual balance check - Available:', availableBalance, 'Requested:', requestedDays);
            
            if (requestedDays > availableBalance) {
                // Show appropriate message based on who is making the request
                const employeeId = getCurrentEmployeeId();
                if (isHrUser && employeeId !== '<?php echo $this->session->userdata("username"); ?>') {
                    const selectedOption = q('#employee_id').options[q('#employee_id').selectedIndex];
                    const employeeName = selectedOption.text.split(' (')[0];
                    errors.push(`رصيد الإجازة السنوية للموظف ${employeeName} غير كافٍ. المتاح: ${availableBalance} يوم.`);
                } else {
                    errors.push(`رصيد الإجازة السنوية غير كافٍ. المتاح: ${availableBalance} يوم.`);
                }
            }
        } else {
            // For all other types, check the default limit
            const maxAllowed = getMaxAllowedDays(vacationType);
            console.log('Non-annual leave - Type:', vacationType, 'Max Allowed:', maxAllowed, 'Requested:', requestedDays);
            
            if (maxAllowed !== null && requestedDays > maxAllowed) {
                errors.push(`عدد الأيام (${requestedDays}) يتجاوز الحد المسموح به (${maxAllowed} يوم) لهذا النوع من الإجازة.`);
            } else if (maxAllowed === null) {
                console.log('No default balance limit found for:', vacationType);
            }
        }

        // Validate delegation field - NOW REQUIRED
        validateDelegation(errors);

        if (!q('input[name="vac[reason]"]').value.trim()) {
            errors.push('سبب الإجازة مطلوب');
        }
        
        // Validate attachment for sick leave
        if (vacationType === 'sick' && !q('#vacAttachment').files.length) {
            errors.push('يجب إرفاق تقرير طبي للإجازة المرضية');
        }

        console.log('Validation errors:', errors);
    }

    function runValidation() {
        const selectedType = getSelectedRequestType();
        const errors = [];
        showFormErrors([]);

        if (!selectedType) {
            errors.push('اختر نوع الطلب');
        } else {
            switch (selectedType) {
                case 'resign':      validateResign(errors);     break;
                case 'fingerprint': validateFingerprint(errors);break;
                case 'overtime':    validateOvertime(errors);   break;
                case 'expenses':    validateExpenses(errors);   break;
                case 'asset':       validateAsset(errors);      break;
                case 'letter':      validateLetter(errors);     break;
                case 'vacation':    validateVacation(errors);   break;
                case 'work_mission': validateWorkMission(errors); break;
            }
        }

        showFormErrors(errors);

        const submitBtn = q('#submitBtn');
        if (errors.length) {
            submitBtn?.setAttribute('disabled', 'disabled');
            return false; // Validation failed
        } else {
            submitBtn?.removeAttribute('disabled');
            return true; // Validation passed
        }
    }
// Toggle the panel visibility based on selected request type
    $('input[name="request_type"]').on('change', function() {
        $('.req-panel').removeClass('active');
        if ($(this).val() === 'permission') {
            $('#panel_permission').addClass('active');
        }
    });

    // Front-end hours validation on form submit
    $('#requestForm').on('submit', function(e) {
        if ($('input[name="request_type"]:checked').val() === 'permission') {
            let start = $('#perm_start').val();
            let end = $('#perm_end').val();
            
            if(start && end) {
                let startTime = new Date("1970-01-01 " + start);
                let endTime = new Date("1970-01-01 " + end);
                let diffHours = (endTime - startTime) / (1000 * 60 * 60);
                
                if (diffHours < 1 || diffHours > 2) {
                    e.preventDefault();
                    $('#perm_error').text('يجب أن تكون مدة الاستئذان بين ساعة وساعتين فقط.').show();
                    return false;
                } else {
                    $('#perm_error').hide();
                }
            }
        }
    });
    // =================================================================
    // SECTION 5: EVENT LISTENERS AND INITIALIZATION - CORRECTED
    // =================================================================

    // Panel switching
    qa('input[name="request_type"]').forEach(r => {
        r.addEventListener('change', e => showPanel(e.target.value));
    });

    // Vacation event listeners
    if (q('#vacMainType')) {
        q('#vacMainType').addEventListener('change', handleLeaveTypeChange);
    }

    if (q('#vacFrom')) {
        q('#vacFrom').addEventListener('change', calculateLeaveDays);
    }

    if (q('#vacTo')) {
        q('#vacTo').addEventListener('change', calculateLeaveDays);
    }

    qa('input[name="vac[day_type]"]').forEach(radio => {
        radio.addEventListener('change', toggleVacationFields);
    });

    // Employee selection for HR users
    if (isHrUser && q('#employee_id')) {
        q('#employee_id').addEventListener('change', updateBalanceDisplay);
    }

    // DELEGATION EMPLOYEE FIELD - add change listener for validation
    if (q('#delegation_employee_id')) {
        q('#delegation_employee_id').addEventListener('change', runValidation);
    }

    // Form validation
    const form = q('#requestForm');
    if (form) {
        form.addEventListener('input', runValidation);
        form.addEventListener('change', runValidation);
        
        form.addEventListener('submit', function(e) {
            if (!runValidation()) {
                e.preventDefault(); // Stop form submission only when validation fails
                console.log('Form validation failed - submission prevented');
            } else {
                console.log('Form validation passed - allowing submission');
                // Allow the form to submit normally
            }
        });
    }
    
    // =================================================================
    // SECTION 6: INITIALIZATION CALLS
    // =================================================================

    // Initialize vacation functionality
    handleLeaveTypeChange();
    updateBalanceDisplay();
    
    // Run initial validation
    runValidation();

    // Initialize Select2 for employee dropdown
    if (typeof $ !== 'undefined' && $('#employee_id').length) {
        $('#employee_id').select2({
            theme: 'bootstrap-5',
            placeholder: $(this).data('placeholder'),
        });
    }
    if ($('#delegation_employee_id').length) {
            $('#delegation_employee_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'اختر موظف للتفويض (اختياري)',
                allowClear: true
            });
        }
});
</script>

<script>
$(document).ready(function() {
    
    // 1. Toggle Range vs Single fields
    $('#otType').on('change', function() {
        var type = $(this).val();
        
        // Reset values
        $('.ot-calc-trigger').val(''); 
        $('#ot_hours').val('');
        $('#ot_calc_hint').html('<i class="fa-solid fa-calculator"></i> سيتم احتساب الساعات تلقائياً بناءً على البصمة');

        if(type === 'range') {
            $('#group_single').addClass('d-none');
            $('#group_range_from').removeClass('d-none');
            $('#group_range_to').removeClass('d-none');
        } else {
            $('#group_single').removeClass('d-none');
            $('#group_range_from').addClass('d-none');
            $('#group_range_to').addClass('d-none');
        }
    });

    // 2. Trigger Calculation when dates change
    $('.ot-calc-trigger').on('change', function() {
        var type = $('#otType').val();
        var dateFrom = (type === 'single') ? $('#ot_date_single').val() : $('#ot_date_from').val();
        var dateTo = (type === 'single') ? $('#ot_date_single').val() : $('#ot_date_to').val();

        // Only proceed if we have necessary dates
        if(!dateFrom) return;
        if(type === 'range' && !dateTo) return;

        // UI Loading State
        $('#ot_hours').val('').prop('readonly', true);
        $('#ot_calc_hint').html('<i class="fa-solid fa-spinner fa-spin"></i> جاري جلب سجلات الحضور...');

        // Get Employee ID (Current User or HR selection)
        var empId = $('#employee_id').length && $('#employee_id').val() ? $('#employee_id').val() : '<?php echo $this->session->userdata("username"); ?>';

        $.ajax({
            url: '<?php echo site_url("users1/ajax_calculate_ot_hours"); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                emp_id: empId,
                date_from: dateFrom,
                date_to: dateTo,
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>' // Initial token
            },
            success: function(response) {
                // Update CSRF for next request
                $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(response.csrf_hash);
                
                if(response.status === 'success') {
                    // Convert decimal hours to HH:MM format for display
                    var decimalHours = parseFloat(response.hours);
                    var hours = Math.floor(decimalHours);
                    var minutes = Math.round((decimalHours - hours) * 60);
                    
                    // Handle edge case where minutes might be 60
                    if(minutes >= 60) {
                        hours += Math.floor(minutes / 60);
                        minutes = minutes % 60;
                    }
                    
                    var formattedHours = hours.toString().padStart(2, '0');
                    var formattedMinutes = minutes.toString().padStart(2, '0');
                    var displayTime = formattedHours + ':' + formattedMinutes;
                    
                    // Show formatted time (9:50) in the input field
                    $('#ot_hours').val(displayTime);
                    
                    // Also update hint with formatted time
                    if(response.hours > 0) {
                        $('#ot_calc_hint').html('<span class="text-success"><i class="fa-solid fa-check"></i> تم الاحتساب: '+displayTime+' ساعة عمل إضافي</span>');
                    } else {
                        $('#ot_calc_hint').html('<span class="text-danger"><i class="fa-solid fa-times"></i> لم يتم تسجيل ساعات إضافية في سجلات الحضور</span>');
                    }

                    // === BUILD DAILY TABLE ===
                    var tbody = '';
                    if (response.details && response.details.length > 0) {
                        
                        // 1. Update Table Header
                        $('#ot_details_container thead tr').html(`
                            <th>التاريخ</th>
                            <th>دخول</th>
                            <th>خروج</th>
                            <th>ساعات العمل</th>
                            <th>المطلوب</th>
                            <th>الإضافي (HH:MM)</th>
                        `);

                        // 2. Build Rows
                        $.each(response.details, function(index, day) {
                            tbody += '<tr class="' + day.class + '">';
                            
                            // Date & Day
                            tbody += '<td>' + day.date + ' <small class="text-muted">(' + day.day_name + ')</small></td>';
                            
                            // In / Out
                            tbody += '<td>' + day.in + '</td>';
                            tbody += '<td>' + day.out + '</td>';
                            
                            // Worked Time (HH:MM)
                            tbody += '<td class="fw-bold" dir="ltr">' + day.worked + '</td>';
                            
                            // Required Hours
                            tbody += '<td>' + day.required + '</td>';
                            
                            // Overtime (HH:MM)
                            if(day.class === 'table-success') {
                                tbody += '<td class="fw-bold text-success" dir="ltr">' + day.ot_display + '</td>';
                            } else {
                                tbody += '<td>-</td>';
                            }
                            
                            tbody += '</tr>';
                        });
                        
                        $('#ot_details_body').html(tbody);
                        $('#ot_details_container').removeClass('d-none'); // Show the table
                    } else {
                        $('#ot_details_container').addClass('d-none');
                    }

                } else {
                    $('#ot_calc_hint').html('<span class="text-danger">'+response.message+'</span>');
                    $('#ot_details_container').addClass('d-none');
                }
                $('#ot_hours').prop('readonly', false); // Allow manual edit if needed
            },
            error: function(xhr, status, error) {
                $('#ot_calc_hint').html('<span class="text-danger">خطأ في الاتصال بالخادم</span>');
                $('#ot_hours').prop('readonly', false);
                $('#ot_details_container').addClass('d-none');
            }
        });
    });
    
    // Add a function to convert decimal hours to HH:MM format
    function decimalToHHMM(decimalHours) {
        if (!decimalHours || isNaN(decimalHours)) return '00:00';
        
        var hours = Math.floor(decimalHours);
        var minutes = Math.round((decimalHours - hours) * 60);
        
        // Handle edge case where minutes might be 60
        if(minutes >= 60) {
            hours += Math.floor(minutes / 60);
            minutes = minutes % 60;
        }
        
        return hours.toString().padStart(2, '0') + ':' + minutes.toString().padStart(2, '0');
    }
});
</script>
<script>
$(document).ready(function() {
    // Isolated Date Restriction Logic
    function applyVacationDateRules() {
        var todayStr = new Date().toISOString().split('T')[0];
        var selectedType = $('#vacMainType').val();

        // Check if the type is 'sick' OR 'maternity'
        if (selectedType === 'sick' || selectedType === 'maternity' || selectedType === 'death'|| selectedType === 'death_brother') {
            // Allow past dates for sick and maternity leave
            $('#vacFrom, #vacTo, #vacHalfDate').removeAttr('min');
        } else {
            // Block past dates for everything else
            $('#vacFrom, #vacTo, #vacHalfDate').attr('min', todayStr);
            
            // Loophole Fix: Clear fields if they already contain a past date
            $('#vacFrom, #vacTo, #vacHalfDate').each(function() {
                if ($(this).val() && $(this).val() < todayStr) {
                    $(this).val('');
                }
            });
        }
    }

    // Run on load and on change
    applyVacationDateRules();
    $('#vacMainType').on('change', applyVacationDateRules);

    // End date cannot be before start date
    $('#vacFrom').on('change', function() {
        var selectedType = $('#vacMainType').val();
        var todayStr = new Date().toISOString().split('T')[0];
        
        // Apply the same logic here: allow past dates for sick or maternity
        var minAllowed = (selectedType === 'sick' || selectedType === 'maternity' || selectedType === 'death' || selectedType === 'death_brother') ? $(this).val() : ($(this).val() || todayStr);
        
        $('#vacTo').attr('min', minAllowed);
        
        if ($('#vacTo').val() && $('#vacTo').val() < minAllowed) {
            $('#vacTo').val('');
        }
    });
});
</script>
    </body>
    </html>
