<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مؤشر السعادة الوظيفي</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    
    <style>
        :root { --happy-grad: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); --marsom-blue: #001f3f; }
        body { font-family: 'Tajawal'; background: #f0f4f8; min-height: 100vh; overflow-x:hidden; }
        
        /* Background Shapes */
        .bg-shape { position: fixed; border-radius: 50%; filter: blur(80px); z-index: -1; }
        .shape-1 { width: 400px; height: 400px; background: rgba(132, 250, 176, 0.4); top: -100px; left: -100px; }
        .shape-2 { width: 300px; height: 300px; background: rgba(143, 211, 244, 0.4); bottom: 50px; right: -50px; }

        .container-box { max-width: 800px; margin: 50px auto; position: relative; }

        /* Card Styling */
        .h-card {
            background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px);
            border-radius: 25px; padding: 50px; box-shadow: 0 20px 60px rgba(0,0,0,0.08);
            border: 1px solid rgba(255,255,255,0.8); text-align: center;
            opacity: 0; transform: translateY(20px); transition: 0.5s all ease; display: none;
        }
        .h-card.active { display: block; opacity: 1; transform: translateY(0); }

        .q-category { text-transform: uppercase; letter-spacing: 2px; color: #888; font-size: 0.9rem; margin-bottom: 10px; }
        .q-text { font-size: 1.8rem; font-weight: 800; color: var(--marsom-blue); margin-bottom: 40px; line-height: 1.4; }

        /* Emoji Scale */
        .emoji-scale { display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; }
        .emoji-opt { display: none; }
        .emoji-label {
            font-size: 3.5rem; cursor: pointer; transition: 0.3s; opacity: 0.5; filter: grayscale(100%);
            width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;
            border-radius: 50%; background: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .emoji-label:hover { transform: scale(1.2); opacity: 1; filter: grayscale(0%); }
        
        .emoji-opt:checked + .emoji-label {
            opacity: 1; filter: grayscale(0%); transform: scale(1.3);
            background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            animation: bounce 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes bounce { 0%{transform:scale(1);} 50%{transform:scale(1.4);} 100%{transform:scale(1.3);} }

        .scale-desc { margin-top: 15px; font-weight: bold; color: var(--marsom-blue); height: 25px; }

        /* Progress Bar */
        .progress-container { position: fixed; top: 0; left: 0; width: 100%; height: 6px; background: #eee; z-index: 100; }
        .progress-fill { height: 100%; background: var(--happy-grad); width: 0%; transition: 0.3s; }

        /* Navigation */
        .nav-btns { margin-top: 50px; display: flex; justify-content: space-between; }
        .btn-next {
            background: var(--marsom-blue); color: white; border: none; padding: 12px 40px;
            border-radius: 50px; font-weight: bold; font-size: 1.1rem; transition: 0.3s;
        }
        .btn-next:hover { transform: translateX(-5px); box-shadow: 0 5px 20px rgba(0,31,63,0.3); }
        .btn-prev { background: transparent; border: 2px solid #ddd; color: #888; padding: 10px 30px; border-radius: 50px; font-weight: bold; }
        .btn-prev:hover { border-color: var(--marsom-blue); color: var(--marsom-blue); }

        /* Final Result */
        .result-circle {
            width: 200px; height: 200px; border-radius: 50%; border: 10px solid #f0f0f0;
            margin: 0 auto 30px; display: flex; align-items: center; justify-content: center;
            font-size: 3rem; font-weight: 900; color: var(--marsom-blue);
            background: white; box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="bg-shape shape-1"></div>
<div class="bg-shape shape-2"></div>
<div class="progress-container"><div class="progress-fill" id="pBar"></div></div>

<div class="container-box">
    
    <?php if(isset($already_done) && $already_done): ?>
        <div class="h-card active">
            <div style="font-size: 5rem;">🎉</div>
            <h2 class="q-text mb-2">شكراً لك!</h2>
            <p class="text-muted fs-5">لقد قمت بقياس مؤشر سعادتك لهذا الشهر.</p>
            <a href="<?= site_url('users1/main_emp') ?>" class="btn-next text-decoration-none">العودة للرئيسية</a>
        </div>
    <?php else: ?>

    <form id="happyForm">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

        <div class="h-card active" id="step_0">
            <div style="font-size: 4rem; margin-bottom: 20px;">👋</div>
            <h1 class="q-text">مرحباً بك في مؤشر السعادة</h1>
            <p class="text-muted fs-5 mb-5">
                هدفنا هو خلق بيئة عمل تجعلك تشعر بالتقدير والراحة.<br>
                أجب بصدق، إجاباتك تساعدنا على التحسن.
            </p>
            <button type="button" class="btn-next" onclick="nextStep(1)">ابدأ التقييم 🚀</button>
        </div>

        <?php 
        $questions = [
            1 => ['cat'=>'بيئة العمل', 'key'=>'q_comfort', 'txt'=>'هل تشعر بالراحة الجسدية والنفسية في مكان عملك؟'],
            2 => ['cat'=>'الأدوات والموارد', 'key'=>'q_tools', 'txt'=>'هل لديك الأدوات والتقنية اللازمة لإنجاز عملك بكفاءة؟'],
            3 => ['cat'=>'الجو العام', 'key'=>'q_atmosphere', 'txt'=>'كيف تصف الطاقة الإيجابية والتعاون بين الزملاء؟'],
            
            4 => ['cat'=>'الدعم الإداري', 'key'=>'q_support', 'txt'=>'هل تشعر أن مديرك المباشر يدعمك ويستمع لمشاكلك؟'],
            5 => ['cat'=>'التقدير', 'key'=>'q_recognition', 'txt'=>'هل تتلقى تقديراً كافياً عند تحقيق إنجازات في العمل؟'],
            6 => ['cat'=>'الشفافية', 'key'=>'q_transparency', 'txt'=>'هل تشعر أن الإدارة تتواصل بوضوح وشفافية؟'],
            
            7 => ['cat'=>'التوازن', 'key'=>'q_balance', 'txt'=>'هل أنت راضٍ عن التوازن بين وقت العمل وحياتك الشخصية؟'],
            8 => ['cat'=>'الضغط', 'key'=>'q_stress', 'txt'=>'هل تشعر أن حجم العمل وضغوطه ضمن الحدود المقبولة؟'],
            9 => ['cat'=>'الأمان الوظيفي', 'key'=>'q_safety', 'txt'=>'إلى أي مدى تشعر بالأمان والاستقرار في وظيفتك؟'],
            
            10 => ['cat'=>'النمو والتطور', 'key'=>'q_learning', 'txt'=>'هل تشعر أنك تتعلم أشياء جديدة وتتطور مهنياً؟'],
            11 => ['cat'=>'المعنى', 'key'=>'q_purpose', 'txt'=>'هل تشعر أن عملك له قيمة ومعنى حقيقي؟'],
            12 => ['cat'=>'الفخر', 'key'=>'q_pride', 'txt'=>'هل تشعر بالفخر لكونك جزءاً من هذا الفريق؟'],
        ];
        
        $total_steps = count($questions);
        foreach($questions as $step => $q): 
        ?>
        <div class="h-card" id="step_<?= $step ?>">
            <div class="q-category"><?= $q['cat'] ?></div>
            <div class="q-text"><?= $q['txt'] ?></div>
            
            <div class="emoji-scale">
                <input type="radio" name="<?= $q['key'] ?>" value="1" id="<?= $q['key'] ?>_1" class="emoji-opt" onclick="setMood(this, 'محبط جداً 😞')">
                <label for="<?= $q['key'] ?>_1" class="emoji-label">😫</label>
                
                <input type="radio" name="<?= $q['key'] ?>" value="2" id="<?= $q['key'] ?>_2" class="emoji-opt" onclick="setMood(this, 'غير راضٍ 😐')">
                <label for="<?= $q['key'] ?>_2" class="emoji-label">🙁</label>
                
                <input type="radio" name="<?= $q['key'] ?>" value="3" id="<?= $q['key'] ?>_3" class="emoji-opt" onclick="setMood(this, 'عادي 🙂')">
                <label for="<?= $q['key'] ?>_3" class="emoji-label">😐</label>
                
                <input type="radio" name="<?= $q['key'] ?>" value="4" id="<?= $q['key'] ?>_4" class="emoji-opt" onclick="setMood(this, 'سعيد 😊')">
                <label for="<?= $q['key'] ?>_4" class="emoji-label">😊</label>
                
                <input type="radio" name="<?= $q['key'] ?>" value="5" id="<?= $q['key'] ?>_5" class="emoji-opt" onclick="setMood(this, 'رائع جداً 🤩')">
                <label for="<?= $q['key'] ?>_5" class="emoji-label">🤩</label>
            </div>
            
            <div class="scale-desc" id="desc_<?= $q['key'] ?>"></div>

            <div class="nav-btns">
                <button type="button" class="btn-prev" onclick="nextStep(<?= $step - 1 ?>)">سابق</button>
                <button type="button" class="btn-next" onclick="validateAndNext(<?= $step ?>, '<?= $q['key'] ?>')">
                    <?= ($step == $total_steps) ? 'إنهاء وحساب النتيجة' : 'التالي' ?>
                </button>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="h-card" id="step_final">
            <div class="q-category">اللمسات الأخيرة</div>
            <div class="q-text">هل لديك أي كلمات تود مشاركتها؟</div>
            <textarea name="feedback_text" class="form-control border-0 bg-white shadow-sm p-3 mb-4" rows="4" placeholder="مساحة حرة للتعبير (اختياري)..."></textarea>
            
            <div class="nav-btns justify-content-center">
                <button type="submit" class="btn-next px-5">إظهار نتيجتي ✨</button>
            </div>
        </div>

    </form>
    <?php endif; ?>

</div>

<div class="modal fade" id="resultModal" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:20px; text-align:center; padding:30px;">
            <div class="result-circle" id="finalScore">0%</div>
            <h2 class="fw-bold mb-2">مؤشر السعادة</h2>
            <p class="text-muted mb-4" id="finalMood">...</p>
            <a href="<?= site_url('users1/main_emp') ?>" class="btn-next text-decoration-none d-block">العودة للرئيسية</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let currentStep = 0;
const totalSteps = <?= isset($total_steps) ? $total_steps : 0 ?> + 1; // +1 for final feedback

function nextStep(n) {
    // Hide current
    $('#step_' + currentStep).removeClass('active');
    
    // Show new
    currentStep = n;
    $('#step_' + currentStep).addClass('active');
    
    // Update Progress
    let pct = (currentStep / totalSteps) * 100;
    $('#pBar').css('width', pct + '%');
}

function validateAndNext(step, key) {
    if (!$('input[name="' + key + '"]:checked').val()) {
        Swal.fire({
            toast: true, position: 'top', icon: 'warning', 
            title: 'الرجاء اختيار شعورك للمتابعة', showConfirmButton: false, timer: 1500
        });
        return;
    }
    
    if(step === <?= isset($total_steps) ? $total_steps : 0 ?>) {
        nextStep('final');
    } else {
        nextStep(step + 1);
    }
}

function setMood(el, text) {
    let key = $(el).attr('name');
    $('#desc_' + key).text(text).hide().fadeIn();
    // Auto advance after short delay for better UX? 
    // setTimeout(() => { $(el).closest('.h-card').find('.btn-next').click(); }, 600);
}

$('#happyForm').on('submit', function(e){
    e.preventDefault();
    
    // Show Loading
    Swal.fire({title: 'جاري تحليل إجاباتك...', didOpen:()=>{Swal.showLoading()}});

    $.post('<?= site_url("users1/submit_happiness_ajax") ?>', $(this).serialize(), function(res) {
        Swal.close();
        if(res.status === 'success') {
            $('#finalScore').text(res.score + '%');
            
            let score = parseFloat(res.score);
            let mood = '';
            let color = '';
            
            if(score >= 85) { mood = 'أنت تعيش في بيئة عمل رائعة! 🌟'; color='#84fab0'; }
            else if(score >= 70) { mood = 'وضعك جيد جداً 👍'; color='#8fd3f4'; }
            else if(score >= 50) { mood = 'وضع مستقر ولكن يحتاج تحسين 🤔'; color='#fccb90'; }
            else { mood = 'يبدو أنك تواجه صعوبات 😟'; color='#ff9a9e'; }
            
            $('#finalMood').text(mood);
            $('.result-circle').css('border-color', color);
            
            var myModal = new bootstrap.Modal(document.getElementById('resultModal'));
            myModal.show();
        } else {
            Swal.fire('خطأ', 'حدثت مشكلة في الحفظ', 'error');
        }
    }, 'json');
});
</script>

</body>
</html>