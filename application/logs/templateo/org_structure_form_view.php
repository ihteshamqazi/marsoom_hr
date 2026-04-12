<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;}
        body{font-family:'Tajawal',sans-serif;overflow-y:auto !important;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        
        .main-container{padding:30px 15px;position:relative;z-index:1}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.6rem;color:var(--text-light);margin-bottom:24px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .page-title::after{content:'';position:absolute;width:160px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
        
        .form-card{background:rgba(255,255,255,.95);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.3);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .top-actions a:hover{background:rgba(255,255,255,.2);color:var(--marsom-orange)}
        
        .form-label{font-weight:700;color:#0b2447;font-size:1rem;margin-bottom:8px}
        .form-control, .form-select { border-radius: 8px; }
        .select2-container--bootstrap-5 .select2-selection { min-height: 38px; border-radius: 8px !important; }
    </style>
</head>
<body>

<div class="top-actions">
    <a href="<?php echo site_url('users1/org_structure_management'); ?>"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
</div>

<div class="main-container container-fluid">
    <div class="text-center"><h1 class="page-title"><?= $page_title ?></h1></div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="card form-card">
                <div class="card-body">
                    <form action="<?= site_url('users1/save_org_structure') ?>" method="post">
                        <input type="hidden" name="id" value="<?= $structure['id'] ?? '' ?>">
                        <p class="text-muted mb-4">اختر الموظفين بالترتيب من المستوى الأعلى (N1) إلى المستوى الأدنى. يمكنك ترك الحقول السفلية فارغة.</p>
                        
                        <?php for ($i = 1; $i <= 7; $i++): ?>
                        <div class="mb-3">
                            <label for="n<?= $i ?>" class="form-label">المستوى <?= $i ?> (N<?= $i ?>)</label>
                            <select class="form-select" id="n<?= $i ?>" name="n<?= $i ?>">
                                <option value="">-- اختر موظف --</option>
                                <?php foreach($employees as $emp): ?>
                                    <option value="<?= $emp['employee_id'] ?>" <?= (($structure['n'.$i] ?? '') == $emp['employee_id']) ? 'selected' : '' ?>>
                                        <?= html_escape($emp['subscriber_name']) ?> (<?= $emp['employee_id'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endfor; ?>

                        <div class="mt-4 pt-4 border-top d-flex justify-content-end">
                            <a href="<?= site_url('users1/org_structure_management') ?>" class="btn btn-secondary me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary">حفظ الهيكل</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for all dropdowns
        $('select').select2({
            theme: 'bootstrap-5',
            placeholder: $(this).data('placeholder'),
        });
    });
</script>
</body>
</html>