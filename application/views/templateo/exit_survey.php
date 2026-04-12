<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>استبيان نهاية الخدمة</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    
    <style>
        :root { --marsom-blue: #001f3f; --marsom-orange: #FF8C00; }
        body { font-family: 'Tajawal'; background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%); min-height: 100vh; padding-bottom: 50px; }
        
        .main-container { max-width: 900px; margin: 40px auto; }
        .survey-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 40px; margin-bottom: 30px; border-top: 5px solid var(--marsom-blue); }
        
        .section-title { font-weight: 800; color: var(--marsom-blue); margin-bottom: 25px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }
        
        /* Star Rating */
        .rating-group { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 10px; }
        .rating-label { font-weight: bold; font-size: 1.1rem; }
        .stars { direction: ltr; display: inline-flex; }
        .stars input { display: none; }
        .stars label { font-size: 2rem; color: #ddd; cursor: pointer; transition: 0.2s; padding: 0 5px; }
        .stars input:checked ~ label, .stars label:hover, .stars label:hover ~ label { color: #ffc107; }
        
        /* Reason Cards */
        .reason-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; }
        .reason-option {
            border: 2px solid #eee; border-radius: 12px; padding: 15px; text-align: center; cursor: pointer; transition: 0.3s;
        }
        .reason-option:hover, .reason-option.active { border-color: var(--marsom-orange); background: #fff8f0; transform: translateY(-3px); }
        .reason-option i { font-size: 1.5rem; color: #6c757d; display: block; margin-bottom: 10px; }
        .reason-option.active i { color: var(--marsom-orange); }

        /* NPS Scale */
        .nps-container { display: flex; justify-content: center; gap: 5px; margin-top: 20px; flex-wrap: wrap; }
        .nps-radio { display: none; }
        .nps-label {
            width: 45px; height: 45px; border-radius: 50%; border: 2px solid #eee; 
            display: flex; align-items: center; justify-content: center; 
            font-weight: bold; cursor: pointer; transition: 0.2s;
        }
        .nps-radio:checked + .nps-label { background: var(--marsom-blue); color: white; border-color: var(--marsom-blue); transform: scale(1.1); }
        /* Colors for NPS */
        .nps-label:hover { border-color: #aaa; }
        
        .btn-submit { background: var(--marsom-orange); color: white; padding: 15px 40px; border-radius: 50px; font-weight: bold; font-size: 1.2rem; border: none; width: 100%; box-shadow: 0 5px 15px rgba(255, 140, 0, 0.3); transition: 0.3s; }
        .btn-submit:hover { background: #e07b00; transform: translateY(-3px); }
    </style>
</head>
<body>

<div class="main-container">
    <div class="text-center mb-5">
        <h1 class="fw-bold" style="color: var(--marsom-blue);">استبيان نهاية الخدمة</h1>
        <p class="text-muted fs-5">رأيك يهمنا ويساعدنا على التطور المستمر</p>
    </div>

    <form id="surveyForm">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="req_id" value="<?= $req_id ?>">

        <div class="survey-card">
            <h4 class="section-title"><i class="fas fa-star text-warning me-2"></i> تقييم التجربة</h4>
            
            <?php 
                $criteria = [
                    'rate_manager' => 'العلاقة مع المدير المباشر',
                    'rate_environment' => 'بيئة العمل وثقافة الشركة',
                    'rate_salary' => 'الرواتب والمزايا',
                    'rate_growth' => 'فرص التطور الوظيفي',
                    'rate_colleagues' => 'التعاون مع الزملاء'
                ];
                foreach($criteria as $name => $label): 
            ?>
            <div class="rating-group">
                <div class="rating-label"><?= $label ?></div>
                <div class="stars">
                    <?php for($i=5; $i>=1; $i--): ?>
                        <input type="radio" id="<?= $name ?>_<?= $i ?>" name="<?= $name ?>" value="<?= $i ?>" required>
                        <label for="<?= $name ?>_<?= $i ?>" title="<?= $i ?> نجوم">★</label>
                    <?php endfor; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="survey-card">
            <h4 class="section-title"><i class="fas fa-door-open text-primary me-2"></i> السبب الرئيسي للمغادرة</h4>
            <input type="hidden" name="main_reason" id="main_reason" required>
            <div class="reason-grid">
                <div class="reason-option" onclick="selectReason('راتب أفضل', this)">
                    <i class="fas fa-money-bill-wave"></i> راتب أفضل
                </div>
                <div class="reason-option" onclick="selectReason('تطور وظيفي', this)">
                    <i class="fas fa-chart-line"></i> تطور وظيفي
                </div>
                <div class="reason-option" onclick="selectReason('ظروف خاصة', this)">
                    <i class="fas fa-home"></i> ظروف عائلية/خاصة
                </div>
                <div class="reason-option" onclick="selectReason('بيئة العمل', this)">
                    <i class="fas fa-users"></i> بيئة العمل
                </div>
                <div class="reason-option" onclick="selectReason('الإدارة', this)">
                    <i class="fas fa-user-tie"></i> الإدارة
                </div>
                <div class="reason-option" onclick="selectReason('دراسة', this)">
                    <i class="fas fa-graduation-cap"></i> إكمال دراسة
                </div>
            </div>
        </div>

        <div class="survey-card text-center">
            <h4 class="section-title">ما مدى احتمالية ترشيحك للشركة كبيئة عمل؟</h4>
            <small class="text-muted">(0 = لا أرشح إطلاقاً ، 10 = أرشح بشدة)</small>
            
            <div class="nps-container">
                <?php for($i=0; $i<=10; $i++): ?>
                    <input type="radio" name="nps_score" value="<?= $i ?>" id="nps_<?= $i ?>" class="nps-radio" required>
                    <label for="nps_<?= $i ?>" class="nps-label"><?= $i ?></label>
                <?php endfor; ?>
            </div>

            <div class="mt-4 pt-3 border-top">
                <label class="fw-bold mb-3 d-block">هل توصي أصدقاءك بالعمل لدينا؟</label>
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="recommend_company" id="rec_yes" value="Yes" autocomplete="off" required>
                    <label class="btn btn-outline-success px-4" for="rec_yes"><i class="fas fa-thumbs-up"></i> نعم</label>

                    <input type="radio" class="btn-check" name="recommend_company" id="rec_no" value="No" autocomplete="off">
                    <label class="btn btn-outline-danger px-4" for="rec_no"><i class="fas fa-thumbs-down"></i> لا</label>
                </div>
            </div>
        </div>

        <div class="survey-card">
            <h4 class="section-title"><i class="fas fa-comment-dots text-info me-2"></i> اقتراحات أو ملاحظات إضافية</h4>
            <textarea name="comments" class="form-control" rows="4" placeholder="اكتب بحرية، رأيك يهمنا..."></textarea>
        </div>

        <button type="submit" class="btn-submit">تسليم الاستبيان</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function selectReason(reason, el) {
    $('#main_reason').val(reason);
    $('.reason-option').removeClass('active');
    $(el).addClass('active');
}

$('#surveyForm').on('submit', function(e){
    e.preventDefault();
    if(!$('#main_reason').val()) { Swal.fire('تنبيه', 'يرجى اختيار سبب المغادرة', 'warning'); return; }

    $.post('<?= site_url("users1/submit_survey_ajax") ?>', $(this).serialize(), function(res) {
        if(res.status === 'success') {
            Swal.fire({
                icon: 'success', title: 'شكراً لك', text: res.message, 
                confirmButtonColor: '#001f3f'
            }).then(() => window.location.href='<?= site_url("users1/main_hr1") ?>');
        } else {
            Swal.fire('خطأ', 'حدث خطأ ما', 'error');
        }
    }, 'json');
});
</script>

</body>
</html>