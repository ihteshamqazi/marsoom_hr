<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>استبيان رضا الموظفين</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    
    <style>
        :root { --marsom-blue: #001f3f; --marsom-orange: #FF8C00; }
        body { font-family: 'Tajawal'; background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%); min-height: 100vh; padding-bottom: 80px; }
        
        .main-container { max-width: 850px; margin: 40px auto; padding: 0 15px; }
        
        /* Hero Section */
        .hero-card {
            background: var(--marsom-blue); color: white; border-radius: 20px; padding: 40px;
            text-align: center; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0, 31, 63, 0.2);
            position: relative; overflow: hidden;
        }
        .hero-card::after {
            content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(45deg); pointer-events: none;
        }

        /* Question Cards */
        .q-card {
            background: white; border-radius: 16px; padding: 30px; margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #fff;
            transition: 0.3s;
        }
        .q-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        
        .q-title { font-weight: 700; color: #333; margin-bottom: 20px; font-size: 1.1rem; }
        
        /* Stars Styling */
        .star-rating { direction: ltr; display: flex; justify-content: center; gap: 10px; }
        .star-rating input { display: none; }
        .star-rating label { font-size: 2.5rem; color: #e0e0e0; cursor: pointer; transition: 0.2s; }
        .star-rating input:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #ffc107; }

        /* NPS Scale (0-10) */
        .nps-row { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; }
        .nps-btn { display: none; }
        .nps-label {
            width: 45px; height: 45px; border-radius: 12px; border: 2px solid #eee; background: white;
            display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer;
            transition: 0.2s; color: #555;
        }
        .nps-btn:checked + .nps-label { background: var(--marsom-blue); color: white; border-color: var(--marsom-blue); transform: scale(1.1); }
        .nps-label:hover { border-color: var(--marsom-orange); }

        /* Chips for "Values" */
        .chip-grid { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; }
        .chip-input { display: none; }
        .chip-label {
            padding: 10px 20px; background: #f8f9fa; border: 2px solid #eee; border-radius: 50px;
            cursor: pointer; font-weight: 600; transition: 0.3s; color: #555;
        }
        .chip-input:checked + .chip-label { background: #e0f0ff; border-color: var(--marsom-blue); color: var(--marsom-blue); }
        
        .btn-submit {
            background: var(--marsom-orange); color: white; border: none; padding: 15px 50px;
            border-radius: 50px; font-size: 1.2rem; font-weight: bold; width: 100%;
            box-shadow: 0 10px 20px rgba(255, 140, 0, 0.3); transition: 0.3s;
        }
        .btn-submit:hover { background: #e07b00; transform: translateY(-3px); }
    </style>
</head>
<body>

<div class="main-container">
    
    <?php if(isset($already_submitted) && $already_submitted): ?>
        <div class="text-center mt-5">
            <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
            <h2 class="mt-4 fw-bold">شكراً لك!</h2>
            <p class="text-muted fs-5">لقد قمت بإرسال الاستبيان لهذا الشهر بالفعل.</p>
            <a href="<?= site_url('users1/main_emp') ?>" class="btn btn-outline-dark mt-3">عودة للرئيسية</a>
        </div>
    <?php else: ?>

    <div class="hero-card">
        <h1 class="fw-bold mb-2">رأيك يصنع الفرق!</h1>
        <p class="opacity-75 fs-5">استبيان رضا الموظفين الدوري - مشاركتك سرية وتساعدنا على التطور</p>
    </div>

    <form id="surveyForm">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

        <div class="q-card">
            <div class="row g-4">
                <div class="col-md-6 text-center">
                    <div class="q-title"><i class="fas fa-briefcase text-primary me-2"></i> الرضا الوظيفي العام</div>
                    <div class="star-rating">
                        <?php for($i=5; $i>=1; $i--): ?>
                            <input type="radio" id="job_<?= $i ?>" name="job_satisfaction" value="<?= $i ?>" required>
                            <label for="job_<?= $i ?>">★</label>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="col-md-6 text-center border-start-md">
                    <div class="q-title"><i class="fas fa-balance-scale text-success me-2"></i> التوازن بين العمل والحياة</div>
                    <div class="star-rating">
                        <?php for($i=5; $i>=1; $i--): ?>
                            <input type="radio" id="life_<?= $i ?>" name="work_life_balance" value="<?= $i ?>" required>
                            <label for="life_<?= $i ?>">★</label>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="col-md-6 text-center mt-4">
                    <div class="q-title"><i class="fas fa-user-tie text-info me-2"></i> التعاون مع الإدارة</div>
                    <div class="star-rating">
                        <?php for($i=5; $i>=1; $i--): ?>
                            <input type="radio" id="mng_<?= $i ?>" name="management_rating" value="<?= $i ?>" required>
                            <label for="mng_<?= $i ?>">★</label>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="col-md-6 text-center border-start-md mt-4">
                    <div class="q-title"><i class="fas fa-comments text-warning me-2"></i> التواصل والبيئة</div>
                    <div class="star-rating">
                        <?php for($i=5; $i>=1; $i--): ?>
                            <input type="radio" id="comm_<?= $i ?>" name="communication_rating" value="<?= $i ?>" required>
                            <label for="comm_<?= $i ?>">★</label>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="q-card text-center">
            <div class="q-title">ما مدى احتمالية أن توصي بالعمل في شركتنا لصديق؟</div>
            <div class="d-flex justify-content-between px-5 mb-2 small text-muted">
                <span>لا أرجح إطلاقاً</span>
                <span>أرجح بشدة</span>
            </div>
            <div class="nps-row">
                <?php for($i=0; $i<=10; $i++): ?>
                    <input type="radio" name="nps_score" id="nps_<?= $i ?>" value="<?= $i ?>" class="nps-btn" required>
                    <label for="nps_<?= $i ?>" class="nps-label"><?= $i ?></label>
                <?php endfor; ?>
            </div>
        </div>

        <div class="q-card text-center">
            <div class="q-title">ما هو الجانب الأكثر أهمية بالنسبة لك للبقاء في الشركة؟</div>
            <div class="chip-grid">
                <?php 
                $opts = ['الراتب والمزايا', 'التطور الوظيفي', 'بيئة العمل', 'المدير المباشر', 'الاستقرار الوظيفي', 'المرونة في العمل'];
                foreach($opts as $opt): ?>
                    <input type="radio" name="most_valued" id="val_<?= md5($opt) ?>" value="<?= $opt ?>" class="chip-input" required>
                    <label for="val_<?= md5($opt) ?>" class="chip-label"><?= $opt ?></label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="q-card">
            <div class="q-title"><i class="fas fa-lightbulb text-warning me-2"></i> هل لديك مقترحات للتحسين؟</div>
            <textarea name="suggestions" class="form-control bg-light border-0" rows="4" placeholder="مساحة حرة للتعبير عن رأيك..."></textarea>
        </div>

        <button type="submit" class="btn-submit">إرسال الاستبيان <i class="fas fa-paper-plane ms-2"></i></button>
    </form>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$('#surveyForm').on('submit', function(e){
    e.preventDefault();
    $.post('<?= site_url("users1/submit_general_survey_ajax") ?>', $(this).serialize(), function(res) {
        if(res.status === 'success') {
            Swal.fire({
                icon: 'success', title: 'شكراً لك', text: res.message, 
                confirmButtonColor: '#001f3f'
            }).then(() => window.location.href='<?= site_url("users1/main_emp") ?>');
        } else {
            Swal.fire('خطأ', res.message, 'error');
        }
    }, 'json').fail(function(){ Swal.fire('خطأ', 'فشل الاتصال', 'error'); });
});
</script>

</body>
</html>