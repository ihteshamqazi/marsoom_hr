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

    <div class="form-check form-check-inline">
      <?php if(in_array(2, $allowed_codes)): ?>
      <input class="form-check-input" type="radio" name="request_type" id="type_fp" value="fingerprint">
      <label class="form-check-label" for="type_fp"><i class="fa-solid fa-fingerprint ms-1"></i> تصحيح بصمة</label>
      <?php endif; ?>
    </div>

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
                            <option value="<?php echo htmlspecialchars($emp['username']); ?>">
                                <?php echo htmlspecialchars($emp['name']) . ' (' . htmlspecialchars($emp['username']) . ')'; ?>
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
                        <input type="time" class="form-control" name="fp[in_time]" placeholder="وقت الدخول المقترح">
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
                        <input type="time" class="form-control" name="fp[out_time]" placeholder="وقت الانصراف المقترح">
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
              <div class="col-sm-4 ot-single">
                <label class="form-label">تاريخ</label>
                <input type="date" name="ot[date]" class="form-control">
              </div>
              <div class="col-sm-4 ot-range d-none">
                <label class="form-label">من تاريخ</label>
                <input type="date" name="ot[from]" class="form-control">
              </div>
              <div class="col-sm-4 ot-range d-none">
                <label class="form-label">إلى تاريخ</label>
                <input type="date" name="ot[to]" class="form-control">
              </div>

              <div class="col-sm-4">
  <label class="form-label">عدد الساعات</label>
  <div class="input-group">
    <span class="input-group-text"><i class="fa-regular fa-clock"></i></span>
    <input type="number"
           name="ot[hours]"
           class="form-control"
           min="0.25"
           step="0.25"
           placeholder="مثال: 1.5 = ساعة ونصف"
           inputmode="decimal">
  </div>
  <div class="form-text muted">يمكن إدخال كسور ربع ساعة: 0.25 ، 0.5 ، 0.75 ، 1 ، 1.25 …</div>
</div>


              <div class="col-sm-4">
                <label class="form-label">هل مدفوع؟</label>
                <select name="ot[paid]" class="form-select">
                  <option value="1">عمل إضافي مدفوع</option>
                  <option value="0">غير مدفوع</option>
                </select>
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
    <input type="file" class="form-control" name="exp[0][file]" id="exp_file" accept=".pdf,.jpg,.jpeg,.png">
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


<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

   $('#employee_id').select2({
        theme: 'bootstrap-5',
        placeholder: $(this).data('placeholder'),
    });
    
    // =================================================================
    // SECTION 1: GENERIC FORM LOGIC (UNCHANGED)
    // =================================================================
    const panels = {
        resign: document.getElementById('panel_resign'),
        fingerprint: document.getElementById('panel_fingerprint'),
        overtime: document.getElementById('panel_overtime'),
        expenses: document.getElementById('panel_expenses'),
        vacation: document.getElementById('panel_vacation'),
        asset: document.getElementById('panel_asset'),
        letter: document.getElementById('panel_letter')
    };
    function showPanel(key){
        for (const k in panels){ if(panels[k]) panels[k].classList.remove('active'); }
        if (panels[key]) panels[key].classList.add('active');
    }
    document.querySelectorAll('input[name="request_type"]').forEach(r => {
        r.addEventListener('change', e => showPanel(e.target.value));
    });

    // =================================================================
    // SECTION 2: UPDATED VACATION LOGIC FOR HR USERS
    // =================================================================
    let expIdx = 1;
      document.getElementById('addExp')?.addEventListener('click', ()=>{
        const wrap = document.getElementById('expItems');
        const row  = document.createElement('div');
        row.className = 'row g-2 align-items-end exp-row mt-2';
         row.innerHTML = `
  <div class="col-12 col-md-2">
    <label class="form-label">اسم العنصر</label>
    <input type="text" class="form-control" name="exp[${expIdx}][item]" placeholder="مثال: أقلام">
  </div>
  <div class="col-12 col-md-2">
    <label class="form-label">المبلغ</label>
    <div class="input-group">
      <span class="input-group-text">﷼</span>
      <input type="number" step="0.01" min="0" class="form-control" name="exp[${expIdx}][amount]">
    </div>
  </div>
  <div class="col-6 col-md-2">
    <label class="form-label">التاريخ</label>
    <input type="date" class="form-control" name="exp[${expIdx}][date]">
  </div>
  <div class="col-6 col-md-4">
    <label class="form-label">الوصف</label>
    <input type="text" class="form-control" name="exp[${expIdx}][desc]">
  </div>
  <div class="col-10 col-md-2">
    <label class="form-label">مرفق</label>
    <input type="file" class="form-control" name="exp[${expIdx}][file]" accept=".pdf,.jpg,.jpeg,.png">
  </div>
  <div class="col-2 text-center">
    <button type="button" class="btn btn-outline-danger btn-sm remove-exp"><i class="fa fa-times"></i></button>
  </div>`;

        wrap.appendChild(row);
        expIdx++;
        document.querySelectorAll('.remove-exp').forEach(btn=>btn.classList.remove('d-none'));
      });

      document.addEventListener('click', function(e){
        if (e.target.closest('.remove-exp')){
          const row = e.target.closest('.exp-row');
          row?.remove();
          if (document.querySelectorAll('.exp-row').length <= 1){
            document.querySelector('.remove-exp')?.classList.add('d-none');
          }
        }   
      });

      // منع اختيار تاريخ مستقبلي في "تصحيح بصمة"
(function(){
  const now = new Date();
  const options = { timeZone: "Asia/Riyadh", year: "numeric", month: "2-digit", day: "2-digit" };
  const parts = new Intl.DateTimeFormat("en-CA", options).formatToParts(now);
  const today = `${parts.find(p=>p.type==='year').value}-${parts.find(p=>p.type==='month').value}-${parts.find(p=>p.type==='day').value}`;

  const fpDate = document.querySelector('input[name="fp[date]"]');
  if(fpDate){
    fpDate.setAttribute("max", today);

    fpDate.addEventListener("input", function(){
      if(this.value > today){
        alert("لا يمكن اختيار تاريخ مستقبلي، يجب أن يكون اليوم أو قبله (حسب توقيت الرياض).");
        this.value = today;
      }
    });
  }
})();

// عمل إضافي: تحقق من ساعات العمل الإضافي قبل الإرسال
(function(){
  const form = document.getElementById('requestForm');
  if(!form) return;

  const hoursInput = document.querySelector('input[name="ot[hours]"]');
  hoursInput?.addEventListener('input', () => {
    hoursInput.value = hoursInput.value.replace(',', '.');
  });

  form.addEventListener('submit', function(e){
    const isOvertime = document.getElementById('type_ot')?.checked;
    if(!isOvertime) return;

    const otTypeSel = document.getElementById('otType');
    const selType   = otTypeSel ? otTypeSel.value : 'single';

    if(selType === 'single'){
      const d = document.querySelector('input[name="ot[date]"]')?.value;
      if(!d){
        e.preventDefault();
        alert('يرجى تحديد تاريخ العمل الإضافي.');
        return;
      }
    } else if(selType === 'range'){
      const f = document.querySelector('input[name="ot[from]"]')?.value;
      const t = document.querySelector('input[name="ot[to]"]')?.value;
      if(!f || !t || f > t){
        e.preventDefault();
        alert('يرجى تحديد فترة صحيحة (من .. إلى) للعمل الإضافي.');
        return;
      }
    }

    const hoursVal = parseFloat(hoursInput?.value || '0');
    if(!(hoursVal > 0) || hoursVal > 24){
      e.preventDefault();
      alert('الرجاء إدخال عدد الساعات بشكل صحيح (أكبر من 0 وحتى 24 ساعة).');
      hoursInput?.focus();
      return;
    }
  });
})();
    document.getElementById('fpDate').max = new Date().toISOString().split("T")[0];

// =================================================================
// UPDATED VACATION BALANCE LOGIC FOR ALL LEAVE TYPES
// =================================================================
const PUBLIC_HOLIDAYS = <?php echo json_encode($public_holidays ?? []); ?>;
const allEmployeeBalances = <?php echo json_encode($all_balances ?? []); ?>;
const currentUserBalances = <?php echo json_encode($balances ?? []); ?>;
const isHrUser = <?php echo json_encode($is_hr_user ?? false); ?>;
// ADD THIS LINE - Get leave types with their default balances
const leaveTypes = <?php echo json_encode($leave_types ?? []); ?>;

const vacMainTypeSelect = document.getElementById('vacMainType');
const vacBalanceDisplay = document.getElementById('vacBalanceDisplay');
const vacFromInput = document.getElementById('vacFrom');
const vacToInput = document.getElementById('vacTo');
const vacDaysMsg = document.getElementById('vacDaysMsg');
const vacDaysCount = document.getElementById('vacDaysCount');
const employeeSelect = document.getElementById('employee_id');

function getCurrentEmployeeId() {
    if (isHrUser && employeeSelect && employeeSelect.value) {
        return employeeSelect.value;
    }
    return '<?php echo $this->session->userdata("username"); ?>';
}

function getBalancesForCurrentEmployee() {
    const employeeId = getCurrentEmployeeId();
    
    if (employeeId === '<?php echo $this->session->userdata("username"); ?>') {
        return currentUserBalances;
    }
    
    return allEmployeeBalances[employeeId] || {};
}

// NEW FUNCTION: Get maximum allowed days for a leave type
function getMaxAllowedDays(leaveTypeSlug) {
    const leaveType = leaveTypes.find(lt => lt.slug === leaveTypeSlug);
    if (leaveType && leaveType.default_balance) {
        return parseInt(leaveType.default_balance);
    }
    return null; // No limit
}

function updateBalanceDisplay() {
    const selectedType = vacMainTypeSelect.value;
    const currentBalances = getBalancesForCurrentEmployee();
    const maxAllowedDays = getMaxAllowedDays(selectedType);
    
    if (selectedType === 'annual' && currentBalances[selectedType]) {
        // Annual leave - show remaining balance
        vacBalanceDisplay.textContent = currentBalances[selectedType].remaining;
        
        const employeeId = getCurrentEmployeeId();
        if (employeeId === '<?php echo $this->session->userdata("username"); ?>') {
            document.querySelector('#vacBalanceBox i').nextSibling.textContent = ' رصيدك المتاح لهذا النوع: ';
        } else {
            const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
            const employeeName = selectedOption.text.split(' (')[0];
            document.querySelector('#vacBalanceBox i').nextSibling.textContent = ` رصيد ${employeeName} المتاح لهذا النوع: `;
        }
    } else if (maxAllowedDays) {
        // Other leave types with default balance limits
        vacBalanceDisplay.textContent = maxAllowedDays;
        document.querySelector('#vacBalanceBox i').nextSibling.textContent = ' الحد الأقصى المسموح به: ';
    } else {
        // Leave types without limits
        vacBalanceDisplay.textContent = '--';
        document.querySelector('#vacBalanceBox i').nextSibling.textContent = ' لا يوجد حد أقصى محدد ';
    }
    calculateLeaveDays();
}

if (isHrUser && employeeSelect) {
    employeeSelect.addEventListener('change', updateBalanceDisplay);
}

function parseYMD(s){
    const [y,m,d] = (s||'').split('-').map(Number);
    if(!y||!m||!d) return null;
    return new Date(y, m-1, d, 12, 0, 0);
}

function calculateLeaveDays() {
    const fromDateStr = vacFromInput.value;
    const toDateStr = vacToInput.value;
    
    if (!fromDateStr || !toDateStr || toDateStr < fromDateStr) {
        vacDaysMsg.textContent = '';
        vacDaysCount.value = 0;
        return;
    }

    const start = parseYMD(fromDateStr);
    const end = parseYMD(toDateStr);
    let count = 0;

    let cur = new Date(start);
    while(cur <= end){
        const day = cur.getDay();
        const currentDateStr = cur.toISOString().slice(0, 10);
        
        if(day !== 5 && day !== 6 && !PUBLIC_HOLIDAYS.includes(currentDateStr)){ 
            count++;
        }
        cur.setDate(cur.getDate() + 1);
    }
    
    // Check against limits for ALL leave types
    const selectedType = vacMainTypeSelect.value;
    const maxAllowedDays = getMaxAllowedDays(selectedType);
    const currentBalances = getBalancesForCurrentEmployee();
    
    let warningMessage = '';
    
    if (selectedType === 'annual') {
        const availableBalance = (currentBalances[selectedType]) ? parseFloat(currentBalances[selectedType].remaining) : 0;
        if (count > availableBalance) {
            warningMessage = ` - <span class="text-danger">يتجاوز الرصيد المتاح (${availableBalance} يوم)</span>`;
        }
    } else if (maxAllowedDays && count > maxAllowedDays) {
        warningMessage = ` - <span class="text-danger">يتجاوز الحد الأقصى المسموح (${maxAllowedDays} يوم)</span>`;
    }
    
    vacDaysMsg.innerHTML = `مجموع أيام العمل: <b class="text-danger">${count}</b> يوم${warningMessage}`;
    vacDaysCount.value = count;
}

if(vacMainTypeSelect) vacMainTypeSelect.addEventListener('change', updateBalanceDisplay);
if(vacFromInput) vacFromInput.addEventListener('change', calculateLeaveDays);
if(vacToInput) vacToInput.addEventListener('change', calculateLeaveDays);

updateBalanceDisplay();

// =================================================================
// SECTION 3: FIXED FORM VALIDATION WITH PROPER ANNUAL LEAVE BALANCE CHECK
// =================================================================

const form = document.getElementById('requestForm');
const submitBtn = document.getElementById('submitBtn');

function showFormErrors(errors){
    const box = document.getElementById('formErrorAlert');
    if(!box) return;
    if(errors.length){
        box.classList.remove('d-none');
        box.innerHTML = '<b>الرجاء استكمال الحقول التالية:</b><ul class="mb-0"><li>' + errors.join('</li><li>') + '</li></ul>';
    } else {
        box.classList.add('d-none');
        box.innerHTML = '';
    }
}

// FIXED VALIDATION: Proper annual leave balance checking
function validateVacation(errors) {
    const vacationType = document.getElementById('vacMainType').value;
    const dayType = document.querySelector('input[name="vac[day_type]"]:checked')?.value;
    
    if (!vacationType) {
        errors.push('نوع الإجازة');
    }

    if (dayType === 'half') {
        const halfDate = document.getElementById('vacHalfDate').value;
        if (!halfDate) {
            errors.push('تاريخ نصف الإجازة');
        }
    } else {
        const fromDate = document.getElementById('vacFrom').value;
        const toDate = document.getElementById('vacTo').value;
        if (!fromDate) errors.push('تاريخ بداية الإجازة');
        if (!toDate) errors.push('تاريخ نهاية الإجازة');
        if (fromDate && toDate && toDate < fromDate) {
            errors.push('تاريخ النهاية يجب أن يكون بعد البداية');
        }
    }

    // Calculate requested days
    let requestedDays = 0;
    if (dayType === 'half') {
        requestedDays = 0.5;
    } else {
        requestedDays = parseFloat(document.getElementById('vacDaysCount').value) || 0;
        
        // Recalculate to ensure accuracy
        const fromDate = document.getElementById('vacFrom').value;
        const toDate = document.getElementById('vacTo').value;
        if (fromDate && toDate) {
            calculateLeaveDays();
            requestedDays = parseFloat(document.getElementById('vacDaysCount').value) || 0;
        }
    }
    
    // FIXED: PROPER ANNUAL LEAVE BALANCE VALIDATION
    if (vacationType === 'annual') {
        const currentBalances = getBalancesForCurrentEmployee();
        const annualBalance = currentBalances['annual'];
        
        // Check if we have valid balance data
        if (annualBalance && typeof annualBalance.remaining !== 'undefined') {
            const availableBalance = parseFloat(annualBalance.remaining);
            
            // Only apply validation for non-HR users or HR users creating for themselves
            const isCreatingForSelf = !isHrUser || (isHrUser && getCurrentEmployeeId() === '<?php echo $this->session->userdata("username"); ?>');
            
            if (isCreatingForSelf && requestedDays > availableBalance) {
                errors.push(`رصيد الإجازة السنوية غير كافٍ - الطلب ${requestedDays} يوم بينما الرصيد المتاح ${availableBalance} يوم فقط`);
            }
        } else {
            // If no balance data found, show error for self-requests
            const isCreatingForSelf = !isHrUser || (isHrUser && getCurrentEmployeeId() === '<?php echo $this->session->userdata("username"); ?>');
            if (isCreatingForSelf) {
                errors.push('لا يمكن التحقق من رصيد الإجازة السنوية - يرجى التواصل مع HR');
            }
        }
    }

    // Other leave type validations
    const maxAllowedDays = getMaxAllowedDays(vacationType);
    if (maxAllowedDays && requestedDays > maxAllowedDays) {
        errors.push(`تجاوز الحد المسموح - هذا النوع من الإجازة لا يمكن أن يتجاوز ${maxAllowedDays} يوم`);
    }

    if (!document.querySelector('input[name="vac[reason]"]').value.trim()) {
        errors.push('سبب الإجازة');
    }

    const attachmentInput = document.getElementById('vacAttachment');
    if (vacationType === 'sick' && (!attachmentInput.files || attachmentInput.files.length === 0)) {
        errors.push('يجب إرفاق تقرير طبي للإجازة المرضية');
    }
    
    return errors;
}

function getSelectedType(){
    const selectedRadio = document.querySelector('input[name="request_type"]:checked');
    return selectedRadio ? selectedRadio.value : null;
}

function runValidation() {
    const selectedType = getSelectedType();
    const errors = [];
    showFormErrors([]);

    if (!selectedType) {
        errors.push('اختر نوع الطلب');
    } else {
        if (selectedType === 'vacation') {
            validateVacation(errors);
        }
    }

    if (errors.length) {
        showFormErrors(errors);
        submitBtn?.setAttribute('disabled', 'disabled');
        return true; // Return true if there are errors
    } else {
        showFormErrors([]);
        submitBtn?.removeAttribute('disabled');
        return false; // Return false if no errors
    }
}

form.addEventListener('change', runValidation);
form.addEventListener('input', runValidation);
vacFromInput?.addEventListener('change', runValidation);
vacToInput?.addEventListener('change', runValidation);

// FIXED: PROPER FORM SUBMISSION BLOCKING
form.addEventListener('submit', function(e) {
    console.log('Form submission attempted...');
    
    const errors = [];
    const selectedType = getSelectedType();
    
    if (!selectedType) {
        errors.push('اختر نوع الطلب');
    } else {
        if (selectedType === 'vacation') {
            validateVacation(errors);
        }
    }
    
    console.log('Validation errors found:', errors);
    
    if (errors.length > 0) {
        e.preventDefault();
        e.stopPropagation();
        showFormErrors(errors);
        
        // Scroll to error message
        const errorBox = document.getElementById('formErrorAlert');
        if (errorBox) {
            errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        console.log('Form submission blocked due to validation errors');
        return false;
    }
    
    console.log('Form validation passed, submitting...');
    return true;
});

runValidation();
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle between full day and half day fields
    const dayTypeRadios = document.querySelectorAll('input[name="vac[day_type]"]');
    const fullDayDiv = document.getElementById('vacFullDayRange');
    const halfDayDiv = document.getElementById('vacHalfDayFields');
    const vacDaysCountInput = document.getElementById('vacDaysCount');
    const vacDaysMsg = document.getElementById('vacDaysMsg');
    const vacAttachment = document.getElementById('vacAttachment');

    function toggleVacationFields() {
        const selectedType = document.querySelector('input[name="vac[day_type]"]:checked').value;
        
        if (selectedType === 'half') {
            fullDayDiv.classList.add('d-none');
            halfDayDiv.classList.remove('d-none');
            
            // Set day count to 0.5
            vacDaysCountInput.value = '0.5';
            vacDaysMsg.innerHTML = `مجموع الأيام: <b class="text-danger">0.5</b> يوم`;
            
            // Make half-day fields required, remove from full-day
            document.getElementById('vacHalfDate').setAttribute('required', 'required');
            document.getElementById('vacHalfPeriod').setAttribute('required', 'required');
            document.getElementById('vacFrom').removeAttribute('required');
            document.getElementById('vacTo').removeAttribute('required');
        } else { // 'full'
            halfDayDiv.classList.add('d-none');
            fullDayDiv.classList.remove('d-none');
            
            // Make full-day fields required, remove from half-day
            document.getElementById('vacFrom').setAttribute('required', 'required');
            document.getElementById('vacTo').setAttribute('required', 'required');
            document.getElementById('vacHalfDate').removeAttribute('required');
            document.getElementById('vacHalfPeriod').removeAttribute('required');
            
            // Recalculate full days
            calculateLeaveDays();
        }
        
        // Handle attachment requirement for sick leave
        const vacationType = document.getElementById('vacMainType').value;
        if (vacationType === 'sick') {
            vacAttachment.setAttribute('required', 'required');
            document.getElementById('attachmentRequired').classList.remove('d-none');
        } else {
            vacAttachment.removeAttribute('required');
            document.getElementById('attachmentRequired').classList.add('d-none');
        }
        
        // Trigger validation after toggling
        if (typeof runValidation === 'function') {
            runValidation();
        }
    }

    // Attach event listener to radio buttons
    dayTypeRadios.forEach(radio => {
        radio.addEventListener('change', toggleVacationFields);
    });

    // Also update when vacation type changes
    const vacMainTypeSelect = document.getElementById('vacMainType');
    if (vacMainTypeSelect) {
        vacMainTypeSelect.addEventListener('change', toggleVacationFields);
    }

    // Initial call to set the correct state on page load
    if (dayTypeRadios.length > 0) {
        toggleVacationFields();
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get references to the elements
    const vacMainTypeSelect = document.getElementById('vacMainType');
    const vacDurationContainer = document.getElementById('vacationDurationContainer');
    const attachmentRequiredSpan = document.getElementById('attachmentRequired');
    const fullDayRadio = document.getElementById('vacDayTypeFull');

    // This function runs whenever the leave type is changed
    function handleLeaveTypeChange() {
        const selectedType = vacMainTypeSelect.value;

        // Feature 2: Show half-day option ONLY for annual leave
        if (selectedType === 'annual') {
            vacDurationContainer.classList.remove('d-none');
        } else {
            vacDurationContainer.classList.add('d-none');
            // If it's not annual, always default back to full day
            if(fullDayRadio) fullDayRadio.checked = true;
            // You might need to trigger the change event on your other script if it manages field visibility
            const changeEvent = new Event('change');
            document.querySelectorAll('input[name="vac[day_type]"]').forEach(r => r.dispatchEvent(changeEvent));
        }

        // Feature 1: Show the required asterisk ONLY for sick leave
        if (selectedType === 'sick') {
            attachmentRequiredSpan.classList.remove('d-none');
        } else {
            attachmentRequiredSpan.classList.add('d-none');
        }
    }

    // Attach the listener and run once on page load
    if (vacMainTypeSelect) {
        vacMainTypeSelect.addEventListener('change', handleLeaveTypeChange);
        handleLeaveTypeChange();
    }
});
</script>

    </body>
    </html>
