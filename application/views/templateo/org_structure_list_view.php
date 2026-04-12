<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة الهيكل التنظيمي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;}
        body{font-family:'Tajawal',sans-serif;overflow-y:auto !important;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .main-container{padding:30px 15px;position:relative;z-index:1}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.6rem;color:var(--text-light);margin-bottom:24px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .page-title::after{content:'';position:absolute;width:160px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
        .table-card{background:rgba(255,255,255,.95);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.3);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items-center;gap:8px;transition:.25s}
        .top-actions a:hover{background:rgba(255,255,255,.2);color:var(--marsom-orange)}
        .table thead th { background-color: #001f3f; color: #fff; }
        .table tbody td { vertical-align: middle; }
        .level-arrow { color: #aaa; margin: 0 8px; font-size: 0.8em; }
        .employee-chip { display: inline-block; background-color: #e9ecef; border-radius: 6px; padding: 4px 8px; font-size: 0.9em; }
    </style>
</head>
<body>

<div class="top-actions">
    <a href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
</div>

<div class="main-container container-fluid">
    <div class="text-center">
        <h1 class="page-title">إدارة الهيكل التنظيمي</h1>
    </div>

    <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success col-lg-10 mx-auto"><?= $this->session->flashdata('success'); ?></div>
    <?php endif; ?>

    <div class="card table-card">
        <div class="card-header bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center px-0 pt-0">
             <div class="col-12 col-md-4">
                <input type="text" id="searchInput" class="form-control" placeholder="ابحث باسم الموظف أو رقمه...">
             </div>
             <a href="<?= site_url('users1/edit_org_structure') ?>" class="btn btn-primary"><i class="fas fa-plus me-2"></i>إضافة سلسلة جديدة</a>
        </div>
        <div class="card-body p-0 pt-3">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th>الهيكل التنظيمي (من الأعلى للأسفل)</th>
                            <th style="width: 15%;">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="structureTableBody">
                        <?php if (empty($structures)): ?>
                            <tr id="no-results-row">
                                <td colspan="3" class="text-center text-muted py-4">لم يتم إضافة أي هياكل تنظيمية بعد.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach($structures as $row): ?>
                        <tr class="structure-row">
                            <td><?= $row['id'] ?></td>
                            <td>
                                <?php
                                $chain = [];
                                for ($i = 1; $i <= 7; $i++) {
                                    if (!empty($row['n'.$i])) {
                                        $name = html_escape($row['n'.$i.'_name'] ?? 'موظف غير معروف');
                                        $id = html_escape($row['n'.$i]);
                                        $chain[] = "<span class='employee-chip'>{$name} ({$id})</span>";
                                    }
                                }
                                echo implode('<span class="level-arrow"><i class="fas fa-chevron-left"></i></span>', $chain);
                                ?>
                            </td>
                            <td>
                                <a href="<?= site_url('users1/edit_org_structure/'.$row['id']) ?>" class="btn btn-sm btn-info" title="تعديل"><i class="fa fa-pen"></i></a>
                                <a href="<?= site_url('users1/delete_org_structure/'.$row['id']) ?>" class="btn btn-sm btn-danger" title="حذف" onclick="return confirm('هل أنت متأكد من الحذف؟')"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                         <tr id="no-results-row" style="display: none;">
                            <td colspan="3" class="text-center text-muted py-4">لا توجد نتائج مطابقة لبحثك.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('structureTableBody');
    const rows = tableBody.getElementsByClassName('structure-row');
    const noResultsRow = document.getElementById('no-results-row');

    searchInput.addEventListener('keyup', function() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleRows = 0;

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const rowText = row.textContent.toLowerCase();
            
            if (rowText.includes(searchTerm)) {
                row.style.display = ''; // Show the row
                visibleRows++;
            } else {
                row.style.display = 'none'; // Hide the row
            }
        }

        // Show or hide the "no results" message
        if (noResultsRow) {
            noResultsRow.style.display = (visibleRows === 0) ? '' : 'none';
        }
    });
});
</script>

</body>
</html>