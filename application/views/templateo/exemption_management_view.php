<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الإعفاءات</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8f9fa;
        }
        .container-wrapper {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 20px;
        }
        .page-header h2 {
            color: #333;
            margin: 0;
            font-weight: 700;
        }
        .nav-buttons .btn {
            margin-left: 10px;
            font-weight: 500;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-outline-secondary {
            color: #5a5a5a;
            border-color: #ced4da;
        }
        .btn-outline-secondary:hover {
            background-color: #f1f1f1;
            color: #333;
        }
        .table {
            font-size: 0.95rem;
        }
        .table thead th {
            background-color: #f1f3f5;
            color: #343a40;
            font-weight: 600;
            border-bottom-width: 2px;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .table .btn-sm {
            padding: 0.25rem 0.6rem;
            font-size: 0.8rem;
        }
        .form-control::placeholder {
            color: #adb5bd;
        }
        .modal-header {
            background-color: #f7f7f7;
            border-bottom: 1px solid #e0e0e0;
        }
        .modal-title {
            font-weight: 600;
        }
        /* Make Select2 dropdowns work inside modals */
        .select2-container {
            width: 100% !important;
            z-index: 999999 !important; 
        }
    </style>
</head>
<body>

    <div class="container-wrapper">

        <div class="page-header">
            <h2><i class="fas fa-shield-alt text-primary"></i> إدارة الإعفاءات</h2>
            <div class="nav-buttons">
                <a href="<?php echo site_url('users1/main_hr1'); ?>" class="btn btn-primary">
                    <i class="fas fa-home"></i> الرئيسية
                </a>
                <button onclick="window.history.back()" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right"></i> رجوع
                </button>
            </div>
        </div>

        <div class="card border-0">
            <div class="card-header bg-white border-0 pb-0 pt-2">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exemptionModal" onclick="prepareAddModal()">
                    <i class="fas fa-plus"></i> إضافة إعفاء جديد
                </button>
            </div>
            <div class="card-body">
                
                <div class="mb-3">
                    <label for="searchInput" class="form-label fw-bold">بحث سريع:</label>
                    <input type="text" id="searchInput" class="form-control" placeholder="ابحث بالرقم الوظيفي أو الاسم...">
                </div>

                <div class="table-responsive">
                    <table id="exemptionsTable" class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>الرقم الوظيفي</th>
                                <th>اسم الموظف (المدخل)</th>
                                <th>الاسم (من ملف الموظف)</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($exemptions)): ?>
                                <?php foreach ($exemptions as $item): ?>
                                <tr data-id="<?= $item['id'] ?>">
                                    <td><?= html_escape($item['employee_id']) ?></td>
                                    <td><?= html_escape($item['name']) ?></td>
                                    <td class="text-muted"><?= html_escape($item['subscriber_name']) ?: '<i class="fas fa-exclamation-circle text-warning" title="غير موجود في ملف الموظفين"></i>' ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editExemption(<?= $item['id'] ?>)">
                                            <i class="fas fa-edit"></i> تعديل
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteExemption(<?= $item['id'] ?>)">
                                            <i class="fas fa-trash"></i> حذف
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted p-4">لا توجد سجلات إعفاء مضافة حالياً.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exemptionModal" tabindex="-1" aria-labelledby="exemptionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exemptionModalLabel">إضافة/تعديل إعفاء</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="exemptionForm">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="exemptionId">
                        <input type="hidden" id="csrf_token_name" name="<?= $csrf_name ?>" value="<?= $csrf_hash ?>">
                        
                        <div class="mb-3">
                            <label for="employeeSelect" class="form-label">الموظف<span class="text-danger">*</span></label>
                            <select class="form-control select2-modal" id="employeeSelect" name="n1" required>
                                <option value="" disabled selected>-- اختر موظف --</option>
                                <?php foreach ($all_employees as $emp): ?>
                                    <option value="<?= html_escape($emp['username']) ?>" data-name="<?= html_escape($emp['name']) ?>"><?= html_escape($emp['name']) ?> (<?= html_escape($emp['username']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="employeeName" class="form-label">اسم الموظف (للتأكيد)<span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-light" id="employeeName" name="name" required readonly>
                        </div>

                        <div id="form-message" class="alert d-none"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                        <button type="submit" class="btn btn-primary" id="saveButton">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Select2 for the modal
            // This makes the dropdown searchable
            $('.select2-modal').select2({
                dropdownParent: $('#exemptionModal')
            });

            // Simple client-side search
            $('#searchInput').on('keyup', function() {
                let filter = $(this).val().toLowerCase();
                $('#exemptionsTable tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(filter) > -1)
                });
            });

            // Handle employee selection change
            $('#employeeSelect').on('change', function() {
                let selectedOption = $(this).find('option:selected');
                let employeeName = selectedOption.data('name');
                $('#employeeName').val(employeeName);
            });
        });

        // Reset modal for adding
        function prepareAddModal() {
            $('#exemptionForm')[0].reset();
            $('#exemptionId').val('');
            $('#exemptionModalLabel').text('إضافة إعفاء جديد');
            $('#form-message').addClass('d-none').text('');
            $('#employeeSelect').val('').trigger('change');
            $('#employeeSelect').prop('disabled', false);
        }

        // AJAX Form Submission
        $('#exemptionForm').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let saveButton = $('#saveButton');
            let formMessage = $('#form-message');
            
            saveButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...');
            formMessage.addClass('d-none').text('');

            $.ajax({
                url: '<?= base_url('users1/ajax_save_exemption') ?>',
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        formMessage.removeClass('alert-danger d-none').addClass('alert alert-success').text(response.message);
                        setTimeout(function() {
                            location.reload();
                        }, 1200);
                    } else {
                        formMessage.removeClass('alert-success d-none').addClass('alert alert-danger').html(response.message || 'An error occurred.');
                        saveButton.prop('disabled', false).html('حفظ');
                    }
                    // Update CSRF token
                    $('#csrf_token_name').val(response.csrf_hash);
                },
                error: function(xhr) {
                    formMessage.removeClass('alert-success d-none').addClass('alert alert-danger').text('Error: ' + xhr.statusText);
                    saveButton.prop('disabled', false).html('حفظ');
                }
            });
        });

        // AJAX Edit
        function editExemption(id) {
            prepareAddModal();
            $('#exemptionModalLabel').text('تعديل الإعفاء');
            
            $.ajax({
                url: '<?= base_url('users1/ajax_get_exemption') ?>',
                type: 'POST',
                data: {
                    id: id,
                    '<?= $csrf_name ?>': $('#csrf_token_name').val() // Use current hash
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#exemptionId').val(response.data.id);
                        $('#employeeSelect').val(response.data.n1).trigger('change');
                        $('#employeeName').val(response.data.name);
                        
                        // Disable employee selection during edit
                        $('#employeeSelect').prop('disabled', true); 

                        $('#exemptionModal').modal('show');
                    } else {
                        alert('Error: ' + response.message);
                    }
                    // Update CSRF token
                    $('#csrf_token_name').val(response.csrf_hash);
                },
                error: function(xhr) {
                    alert('Error fetching data. ' + xhr.statusText);
                    // Reload hash on error
                    $('#csrf_token_name').val('<?= $csrf_hash ?>');
                }
            });
        }

        // AJAX Delete
        function deleteExemption(id) {
            if (!confirm('هل أنت متأكد أنك تريد حذف هذا السجل؟')) {
                return;
            }

            $.ajax({
                url: '<?= base_url('users1/ajax_delete_exemption') ?>',
                type: 'POST',
                data: {
                    id: id,
                    '<?= $csrf_name ?>': $('#csrf_token_name').val() // Use current hash
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                        // Update CSRF token
                        $('#csrf_token_name').val(response.csrf_hash);
                    }
                },
                error: function(xhr) {
                    alert('Error deleting data. ' + xhr.statusText);
                }
            });
        }
    </script>
</body>
</html>