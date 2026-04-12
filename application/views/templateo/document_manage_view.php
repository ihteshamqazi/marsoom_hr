<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;--glass-border:rgba(255,255,255,.2);}
        body{font-family:'Tajawal',sans-serif;overflow:hidden;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        #loading-screen{position:fixed;inset:0;background:transparent;z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column;transition:opacity .5s}
        .loader{width:50px;height:50px;border:5px solid rgba(255,255,255,.3);border-top:5px solid var(--marsom-orange);border-radius:50%;animation:spin 1s linear infinite;margin-bottom:16px}
        @keyframes spin{to{transform:rotate(360deg)}}
        .main-container{padding:30px 15px;visibility:hidden;opacity:0;transition:opacity .5s;position:relative;z-index:1}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.8rem;color:var(--text-light);margin-bottom:32px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .page-title::after{content:'';position:absolute;width:100px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
        .table-card{background:rgba(255,255,255,.95);backdrop-filter:blur(10px);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .dataTables-example thead th{background-color:#001f3f !important;color:#fff;text-align:center;vertical-align:middle;}
        .dataTables-example tbody td{text-align:center;vertical-align:middle;font-size:14px;}
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .top-actions a:hover{background:rgba(255,255,255,.2);color:var(--marsom-orange)}
        .expiring-soon { background-color: #ffeaea !important; color: #d32f2f !important; font-weight: bold; }
        
        /* Dynamic Row Styling */
        .doc-row { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 10px; padding: 15px; margin-bottom: 15px; position: relative; }
        .remove-doc-btn { position: absolute; top: -10px; left: -10px; border-radius: 50%; width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>

<div id="loading-screen"><div class="loader"></div><h3 style="color:#fff">Loading Data ...</h3></div>
<div class="top-actions"><a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a><a href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a></div>

<div class="main-container container-fluid">
    <div class="text-center"><h1 class="page-title">ملف مستندات الموظفين</h1></div>
    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الرقم الوظيفي</th>
                                    <th>اسم الموظف</th>
                                    <th>عدد المستندات المرفقة</th>
                                    <th>أقرب تاريخ انتهاء</th>
                                    <th>ملاحظات</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $idno = 0; ?>
                                <?php if (isset($documents) && is_array($documents)): ?>
                                    <?php foreach($documents as $doc) : ?>
                                        <?php 
                                            $idno++; 
                                            $is_expiring = false;
                                            $nearest_date = null;
                                            
                                            // Decode JSON to check all dates in this record
                                            $files_array = json_decode($doc['documents_data'], true);
                                            $total_docs = is_array($files_array) ? count($files_array) : 0;

                                            if(is_array($files_array)) {
                                                foreach($files_array as $file) {
                                                    if (!empty($file['expiry_date'])) {
                                                        $diff = strtotime($file['expiry_date']) - time();
                                                        $days_left = floor($diff / (60 * 60 * 24));
                                                        
                                                        // Find nearest date
                                                        if($nearest_date === null || strtotime($file['expiry_date']) < strtotime($nearest_date)) {
                                                            $nearest_date = $file['expiry_date'];
                                                        }

                                                        // If ANY document is < 60 days, mark row as red
                                                        if ($days_left <= 60) {
                                                            $is_expiring = true;
                                                        }
                                                    }
                                                }
                                            }
                                        ?>
                                        <tr class="<?php echo $is_expiring ? 'expiring-soon' : ''; ?>">
                                            <td><?php echo $idno; ?></td>
                                            <td><?php echo html_escape($doc['employee_id']); ?></td>
                                            <td><?php echo html_escape($doc['emp_name']); ?></td>
                                            <td><span class="badge bg-primary fs-6"><?php echo $total_docs; ?> مستندات</span></td>
                                            <td><?php echo $nearest_date ? $nearest_date : '-'; ?></td>
                                            <td><?php echo html_escape($doc['notes']); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info text-white view-files-btn" 
                                                        data-files='<?php echo htmlspecialchars($doc['documents_data'], ENT_QUOTES, 'UTF-8'); ?>'>
                                                    <i class="fas fa-folder-open"></i> عرض السجل
                                                </button>
                                                <a class="btn btn-sm btn-danger text-white delete-btn" data-id="<?php echo $doc['id']; ?>" title="حذف"><i class="fas fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="documentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">إضافة سجل مستندات جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="documentForm" enctype="multipart/form-data">
                    <div class="row bg-light p-3 rounded mb-4 border">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">الرقم الوظيفي</label>
                            <input type="text" class="form-control" id="emp_id" name="emp_id" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">اسم الموظف</label>
                            <input type="text" class="form-control bg-white" id="emp_name" name="emp_name" readonly>
                        </div>
                    </div>

                    <h5 class="mb-3 border-bottom pb-2 text-primary"><i class="fas fa-file-invoice"></i> المستندات المرفقة</h5>
                    
                    <div id="dynamic-docs-container">
                        <div class="doc-row">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">نوع المستند</label>
                                    <select class="form-select" name="doc_type[]" required>
                                        <option value="">-- اختر --</option>
                                        <option value="هوية وطنية / إقامة">هوية وطنية / إقامة</option>
                                        <option value="جواز سفر">جواز سفر</option>
                                        <option value="عقد عمل">عقد عمل</option>
                                        <option value="شهادة طبية">شهادة طبية</option>
                                        <option value="أخرى">أخرى</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">تاريخ الانتهاء</label>
                                    <input type="date" class="form-control" name="expiry_date[]" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">المرفق</label>
                                    <input type="file" class="form-control" name="doc_file[]" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <button type="button" id="add-row-btn" class="btn btn-warning fw-bold text-dark">
                            <i class="fas fa-plus-circle"></i> إضافة مستند آخر لنفس الموظف
                        </button>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">ملاحظات عامة للسجل</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="submit" form="documentForm" class="btn btn-primary px-4">حفظ السجل بالكامل</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="filesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">سجل المستندات</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr><th>نوع المستند</th><th>تاريخ الانتهاء</th><th>الملف</th></tr>
                        </thead>
                        <tbody id="filesListContainer"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    window.addEventListener('load', function(){
        document.getElementById('loading-screen').style.opacity='0';
        setTimeout(() => { document.getElementById('loading-screen').style.display='none'; document.body.style.overflow='auto'; document.querySelector('.main-container').style.visibility='visible'; document.querySelector('.main-container').style.opacity='1'; }, 400);
    });

    $(document).ready(function () {
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
        var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

        $('.dataTables-example').DataTable({
            responsive: true,
            layout: {
                topStart: {
                    buttons: [{
                        text:'<i class="fa fa-plus-circle"></i> إضافة سجل جديد',
                        className:'btn btn-success fw-bold',
                        action: function () {
                            $('#documentForm')[0].reset();
                            // Reset dynamic rows to only show one
                            $('#dynamic-docs-container').children('.doc-row:not(:first)').remove();
                            $('#documentModal').modal('show');
                        }
                    }]
                }
            },
            language: { url:'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json' }
        });

        // JQuery Logic for the Dynamic Row Repeater
        $('#add-row-btn').on('click', function() {
            var newRow = `
                <div class="doc-row">
                    <button type="button" class="btn btn-danger remove-doc-btn" title="حذف هذا المستند"><i class="fas fa-times"></i></button>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">نوع المستند</label>
                            <select class="form-select" name="doc_type[]" required>
                                <option value="">-- اختر --</option>
                                <option value="هوية وطنية / إقامة">هوية وطنية / إقامة</option>
                                <option value="جواز سفر">جواز سفر</option>
                                <option value="عقد عمل">عقد عمل</option>
                                <option value="شهادة طبية">شهادة طبية</option>
                                <option value="أخرى">أخرى</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">تاريخ الانتهاء</label>
                            <input type="date" class="form-control" name="expiry_date[]" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">المرفق</label>
                            <input type="file" class="form-control" name="doc_file[]" required>
                        </div>
                    </div>
                </div>
            `;
            $('#dynamic-docs-container').append(newRow);
        });

        // Remove a dynamically added row
        $(document).on('click', '.remove-doc-btn', function() {
            $(this).closest('.doc-row').slideUp(function() { $(this).remove(); });
        });

        // Fetch Employee Name
        $('#emp_id').on('blur', function() {
            var empId = $(this).val();
            if (empId) {
                $.post("<?php echo site_url('users1/get_employee_data'); ?>", { employee_id: empId, [csrfName]: csrfHash }, function(response) {
                    $('#emp_name').val(response.status === 'success' ? response.employee_name : 'غير موجود');
                }, 'json');
            }
        });

        // Submit Form
        $('#documentForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            formData.append(csrfName, csrfHash);
            
            Swal.fire({ title: 'جاري الحفظ...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

            $.ajax({
                url: "<?php echo site_url('users1/save_document_ajax'); ?>",
                type: "POST", data: formData, contentType: false, processData: false, dataType: "json",
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({icon: 'success', title: 'تم!', text: response.message}).then(() => location.reload());
                    } else {
                        Swal.fire({icon: 'error', title: 'خطأ!', text: response.message});
                    }
                }
            });
        });

        // View Multiple Files
        $('.dataTables-example tbody').on('click', '.view-files-btn', function() {
            var filesArray = JSON.parse($(this).attr('data-files'));
            var html = '';
            
            filesArray.forEach(function(file) {
                var fileUrl = "<?php echo base_url('uploads/documents/'); ?>" + file.file_name;
                
                // Check if expired
                var today = new Date();
                var expDate = new Date(file.expiry_date);
                var isExp = (expDate <= today) ? '<span class="badge bg-danger">منتهي</span>' : '';

                html += `<tr>
                            <td class="fw-bold">${file.type}</td>
                            <td>${file.expiry_date} ${isExp}</td>
                            <td><a href="${fileUrl}" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-download"></i> تنزيل المرفق</a></td>
                         </tr>`;
            });
            
            $('#filesListContainer').html(html);
            $('#filesModal').modal('show');
        });
    });
</script>

</body>
</html>