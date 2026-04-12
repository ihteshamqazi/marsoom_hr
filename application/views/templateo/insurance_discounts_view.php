<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة خصومات التأمين</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />

    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;}
        body{font-family:'Tajawal',sans-serif;overflow:hidden;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .particles{position:fixed;inset:0;overflow:hidden;z-index:-1}
        .particle{position:absolute;background:rgba(255,140,0,.1);clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);animation:float 25s infinite ease-in-out;opacity:0;filter:blur(2px)}
        .particle:nth-child(even){background:rgba(0,31,63,.1)}
        @keyframes float{0%{transform:translateY(0) translateX(0) rotate(0);opacity:0}20%{opacity:1}80%{opacity:1}100%{transform:translateY(-100vh) translateX(50px) rotate(360deg);opacity:0}}
        #loading-screen{position:fixed;inset:0;background:transparent;z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column;transition:opacity .5s}
        .loader{width:50px;height:50px;border:5px solid rgba(255,255,255,.3);border-top:5px solid var(--marsom-orange);border-radius:50%;animation:spin 1s linear infinite;margin-bottom:16px}
        @keyframes spin{to{transform:rotate(360deg)}}
        .main-container{padding:30px 15px;visibility:hidden;opacity:0;transition:opacity .5s;position:relative;z-index:1}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.8rem;color:var(--text-light);margin-bottom:32px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .page-title::after{content:'';position:absolute;width:100px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
        .table-card{background:rgba(255,255,255,.9);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.3);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .dataTables-example thead th{background-color:#001f3f !important;color:#fff;text-align:center;vertical-align:middle;border-bottom:2px solid #00152b}
        .dataTables-example tbody td{text-align:center;vertical-align:middle;font-size:14px;white-space:nowrap}
        .dt-buttons .btn{background-color:var(--marsom-orange);border-color:var(--marsom-orange);color:#fff;font-weight:500;margin:0 2px;box-shadow:0 2px 8px rgba(0,0,0,.2)}
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .action-btns .btn{margin: 0 2px;}
        .select2-container--bootstrap-5 .select2-dropdown { z-index: 1060; } /* Ensure dropdown appears above modal */
        
        /* CSV Upload Styles */
        .csv-upload-section {
            background: rgba(255,255,255,0.95);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        .template-download {
            color: #0d6efd;
            text-decoration: none;
        }
        .template-download:hover {
            color: #0a58ca;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="particles">
    <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
    <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
</div>
<div id="loading-screen"><div class="loader"></div><h3 style="color:#fff">جاري تحميل البيانات ...</h3></div>
<div class="top-actions">
    <a href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
    <a href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="main-container container-fluid">
    <div class="text-center"><h1 class="page-title">إدارة خصومات التأمين</h1></div>
    
    <!-- CSV Upload Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="csv-upload-section">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5><i class="fas fa-file-csv text-success me-2"></i>رفع بيانات الخصومات من ملف CSV</h5>
                        <p class="mb-0 text-muted">يمكنك رفع ملف CSV يحتوي على بيانات الخصومات لعدة موظفين دفعة واحدة</p>
                        <small class="text-muted">
                            <a href="<?php echo site_url('users1/download_insurance_discounts_template'); ?>" class="template-download">
                                <i class="fas fa-download me-1"></i>تحميل نموذج CSV
                            </a>
                        </small>
                    </div>
                    <div class="col-md-4 text-left">
                        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#csvUploadModal">
                            <i class="fas fa-upload me-1"></i> رفع ملف CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-body">
                    <div class="mb-3 text-start">
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="fas fa-plus me-1"></i> إضافة خصم جديد
                        </button>
                    </div>
                    <table id="discountsTable" class="table table-striped table-bordered dataTables-example" style="width:100%">
                        <thead>
                            <tr>
                                <th>الرقم الوظيفي</th>
                                <th>اسم الموظف</th>
                                <th>نسبة الخصم (%)</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($discounts as $item): ?>
                            <tr>
                                <td><?php echo html_escape($item['n1']); ?></td>
                                <td><?php echo html_escape($item['n2']); ?></td>
                                <td><?php echo html_escape($item['n3']); ?></td>
                                <td class="action-btns">
                                    <button class="btn btn-sm btn-primary edit-btn"
                                            data-id="<?php echo $item['id']; ?>"
                                            data-employee-name="<?php echo html_escape($item['n2']); ?>"
                                            data-percentage="<?php echo html_escape($item['n3']); ?>">
                                        <i class="fa fa-pen"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $item['id']; ?>">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSV Upload Modal -->
<div class="modal fade" id="csvUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="csvUploadForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">رفع ملف CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="csrf-token" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> تعليمات:</h6>
                        <ul class="mb-0 small">
                            <li>يجب أن يكون الملف بصيغة <strong>CSV</strong></li>
                            <li>يجب أن يحتوي على الأعمدة التالية بالترتيب: <code>n1, n2, n3</code></li>
                            <li><strong>n1</strong>: الرقم الوظيفي</li>
                            <li><strong>n2</strong>: اسم الموظف</li>
                            <li><strong>n3</strong>: نسبة الخصم (%)</li>
                            <li>الصف الأول يجب أن يحتوي على رؤوس الأعمدة</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">اختر ملف CSV</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                        <div class="form-text">الحجم الأقصى: 5MB</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="update_existing" name="update_existing" value="1">
                            <label class="form-check-label" for="update_existing">
                                تحديث السجلات الموجودة (بناءً على الرقم الوظيفي)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary" id="uploadCsvBtn">
                        <i class="fas fa-upload me-1"></i> رفع ومعالجة الملف
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm">
                <div class="modal-header">
                    <h5 class="modal-title">تعديل نسبة الخصم</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <input type="hidden" class="csrf-token" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="mb-3">
                        <label class="form-label">الموظف</label>
                        <input type="text" id="edit-employee-name" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit-percentage" class="form-label">نسبة الخصم الجديدة (%)</label>
                        <input type="number" class="form-control" id="edit-percentage" name="n3" min="0" max="100" step="0.0001" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addForm">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة خصم تأمين جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="csrf-token" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="mb-3">
                        <label for="add-employee-id" class="form-label">الموظف</label>
                        <select class="form-select employee-select" id="add-employee-id" name="n1" required style="width: 100%;">
                            <option value="">-- اختر موظف --</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?php echo html_escape($emp['username']); ?>"><?php echo html_escape($emp['username']) . ' - ' . html_escape($emp['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                     <div class="mb-3">
                        <label class="form-label">اسم الموظف</label>
                        <input type="text" id="add-employee-name" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="add-percentage" class="form-label">نسبة الخصم (%)</label>
                        <input type="number" class="form-control" id="add-percentage" name="n3" min="0" max="100" step="0.0001" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success" id="addBtn">إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>
$(document).ready(function() {
    // --- Initialize DataTables with Buttons ---
    $('#discountsTable').DataTable({
        responsive: true,
        pageLength: 25,
        language: { url:'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json' },
        layout: {
            topStart: {
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> تصدير إكسل',
                        className: 'btn btn-sm',
                        exportOptions: {
                            columns: ':visible:not(.action-btns)' // Exclude action column
                        }
                    },
                     {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> طباعة',
                         className: 'btn btn-sm',
                        exportOptions: {
                            columns: ':visible:not(.action-btns)' // Exclude action column
                        }
                    }
                ]
            }
        }
    });

    // --- Initialize Select2 for Employee Dropdown ---
    $('.employee-select').select2({
        theme: "bootstrap-5",
        dropdownParent: $("#addModal") // Important for dropdowns inside modals
    });

    // --- Modals ---
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    const addModal = new bootstrap.Modal(document.getElementById('addModal'));
    const csvUploadModal = new bootstrap.Modal(document.getElementById('csvUploadModal'));

    // --- CSRF Handling ---
    // Function to update CSRF tokens in all forms
    function updateCsrfTokens(hash) {
        $('.csrf-token').val(hash);
    }

    // --- Edit Functionality ---
    $(document).on('click', '.edit-btn', function() {
        $('#edit-id').val($(this).data('id'));
        $('#edit-employee-name').val($(this).data('employee-name'));
        $('#edit-percentage').val($(this).data('percentage'));
        editModal.show();
    });

    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        const saveBtn = $('#saveBtn');
        saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جارٍ الحفظ...');
        $.ajax({
            url: "<?php echo site_url('users1/ajax_update_insurance_discount'); ?>",
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                updateCsrfTokens(response.csrf_hash); // Update CSRF token
                if (response.status === 'success') {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('خطأ: ' + response.message);
                }
            },
            error: function() { alert('حدث خطأ غير متوقع. حاول مرة أخرى.'); },
            complete: function() { saveBtn.prop('disabled', false).text('حفظ التغييرات'); }
        });
    });

    // --- Add New Functionality ---
    $('#add-employee-id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const employeeName = selectedOption.text().split(' - ')[1] || ''; // Extract name
        $('#add-employee-name').val(employeeName);
    });

     $('#addForm').on('submit', function(e) {
        e.preventDefault();
        const addBtn = $('#addBtn');
        addBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جارٍ الإضافة...');
        $.ajax({
            url: "<?php echo site_url('users1/ajax_add_insurance_discount'); ?>",
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                updateCsrfTokens(response.csrf_hash); // Update CSRF token
                if (response.status === 'success') {
                    alert(response.message);
                    location.reload(); // Reload page to show new entry
                } else {
                    alert('خطأ: ' + response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                 console.error("AJAX Error:", textStatus, errorThrown);
                 alert('حدث خطأ غير متوقع. حاول مرة أخرى.');
             },
            complete: function() { addBtn.prop('disabled', false).text('إضافة'); }
        });
    });

    // --- CSV Upload Functionality ---
    $('#csvUploadForm').on('submit', function(e) {
        e.preventDefault();
        const uploadBtn = $('#uploadCsvBtn');
        const formData = new FormData(this);
        
        // Check if file is selected
        const fileInput = document.getElementById('csv_file');
        if (!fileInput.files.length) {
            alert('يرجى اختيار ملف CSV أولاً');
            return;
        }
        
        uploadBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جارٍ المعالجة...');
        
        $.ajax({
            url: "<?php echo site_url('users1/process_insurance_discounts_csv'); ?>",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.csrf_hash) {
                    updateCsrfTokens(response.csrf_hash);
                }
                
                if (response.status === 'success') {
                    let message = `تمت العملية بنجاح!\n- السجلات المضافة: ${response.inserted}\n- السجلات المحدثة: ${response.updated}`;
                    
                    if (response.errors && response.errors.length > 0) {
                        message += `\n- عدد الأخطاء: ${response.errors.length}`;
                        if (response.errors.length <= 5) {
                            message += `\nالأخطاء:\n- ${response.errors.join('\n- ')}`;
                        }
                    }
                    
                    alert(message);
                    csvUploadModal.hide();
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alert('خطأ: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'حدث خطأ أثناء رفع الملف';
                
                // Try to parse JSON response if available
                if (xhr.responseText) {
                    try {
                        const jsonResponse = JSON.parse(xhr.responseText);
                        if (jsonResponse.message) {
                            errorMessage += ': ' + jsonResponse.message;
                        } else {
                            errorMessage += ': ' + xhr.responseText;
                        }
                    } catch (e) {
                        errorMessage += ': ' + xhr.responseText;
                    }
                } else {
                    errorMessage += ': ' + error;
                }
                
                alert(errorMessage);
                console.error('Upload error:', xhr.responseText || error);
            },
            complete: function() {
                uploadBtn.prop('disabled', false).html('<i class="fas fa-upload me-1"></i> رفع ومعالجة الملف');
            }
        });
    });

    // Reset CSV form when modal is closed
    $('#csvUploadModal').on('hidden.bs.modal', function () {
        $('#csvUploadForm')[0].reset();
    });

     // --- Delete Functionality ---
    $(document).on('click', '.delete-btn', function() {
        const recordId = $(this).data('id');
        if (confirm('هل أنت متأكد من حذف هذا السجل؟ لا يمكن التراجع عن هذا الإجراء.')) {
             // Get current CSRF token from one of the forms
            const csrfTokenName = $('.csrf-token').attr('name');
            const csrfTokenValue = $('.csrf-token').val();
            const postData = {
                id: recordId
            };
            postData[csrfTokenName] = csrfTokenValue; // Add CSRF token to data

            $.ajax({
                url: "<?php echo site_url('users1/ajax_delete_insurance_discount'); ?>",
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function(response) {
                    updateCsrfTokens(response.csrf_hash); // Update CSRF token
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload(); // Reload page to remove the row
                    } else {
                        alert('خطأ: ' + response.message);
                    }
                },
                error: function() { alert('حدث خطأ غير متوقع. حاول مرة أخرى.'); }
            });
        }
    });

}); // End document ready

// --- Loading Screen ---
window.addEventListener('load', function(){
    const loading = document.getElementById('loading-screen');
    const main = document.querySelector('.main-container');
    loading.style.opacity='0';
    setTimeout(function(){ 
        loading.style.display='none'; 
        document.body.style.overflow='auto'; 
        main.style.visibility='visible'; 
        main.style.opacity='1'; 
    }, 400);
});
</script>

</body>
</html>