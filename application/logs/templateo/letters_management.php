<?php /* application/views/templateo/letter_management.php */ ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة الخطابات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f4f7f6; }
        .page-header { color: #001f3f; margin-bottom: 2rem; }
        .letter-card {
            cursor: pointer; transition: all 0.2s ease-in-out;
            border-right: 4px solid #FF8C00;
        }
        .letter-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
        .letter-icon { font-size: 2rem; color: #FF8C00; }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
             <h1 class="page-header fw-bold">إصدار الخطابات</h1>
             <a href="javascript:history.back()" class="btn btn-outline-secondary"><i class="fas fa-arrow-right me-2"></i> رجوع</a>
        </div>
       

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 letter-card" data-letter-slug="salary-certificate">
                    <div class="card-body text-center">
                        <i class="fas fa-file-invoice-dollar letter-icon mb-3"></i>
                        <h5 class="card-title fw-bold">خطاب إثبات مزايا وظيفية</h5>
                        <p class="card-text text-muted small">خطاب تعريف بالراتب موجه لجهة معينة.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 letter-card" data-letter-slug="salary-commitment">
                    <div class="card-body text-center">
                        <i class="fas fa-exchange-alt letter-icon mb-3"></i>
                        <h5 class="card-title fw-bold">التزام تحويل راتب</h5>
                        <p class="card-text text-muted small">نموذج التزام لتحويل الراتب إلى بنك محدد.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 letter-card" data-letter-slug="salary-commitment-marsoom">
                    <div class="card-body text-center">
                        <i class="fas fa-file-signature letter-icon mb-3"></i>
                        <h5 class="card-title fw-bold">التزام تحويل راتب (مرسوم)</h5>
                        <p class="card-text text-muted small">نموذج التزام خاص بشركة مرسوم.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 letter-card" data-letter-slug="embassy-letter">
                    <div class="card-body text-center">
                        <i class="fas fa-passport letter-icon mb-3"></i>
                        <h5 class="card-title fw-bold">خطاب للسفارة (عربي/إنجليزي)</h5>
                        <p class="card-text text-muted small">خطاب تعريف ثنائي اللغة موجه للسفارات.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 letter-card" data-letter-slug="eos-certificate">
                    <div class="card-body text-center">
                        <i class="fas fa-user-check letter-icon mb-3"></i>
                        <h5 class="card-title fw-bold">إفادة نهاية الخدمة</h5>
                        <p class="card-text text-muted small">إفادة بتحويل مستحقات نهاية الخدمة للموظف.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="employeeSelectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">اختر الموظف</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>الرجاء تحديد الموظف الذي قدم طلب لهذا الخطاب:</p>
                    <select id="employeeDropdown" class="form-select" style="width: 100%;">
                        <option value="">جاري تحميل الموظفين...</option>
                    </select>
                    <div class="alert alert-danger mt-2 d-none" id="employeeError">الرجاء اختيار موظف.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" id="generateLetterBtn">إصدار الخطاب</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            const employeeModal = new bootstrap.Modal(document.getElementById('employeeSelectModal'));
            let selectedLetterSlug = '';

            // Initialize Select2
            $('#employeeDropdown').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#employeeSelectModal')
            });

            // When a letter card is clicked
            $('.letter-card').on('click', function() {
                selectedLetterSlug = $(this).data('letter-slug');
                $('#employeeDropdown').val(null).trigger('change').empty(); // Clear previous options
                
                // Fetch employees who have pending letter requests
                $.ajax({
                    url: '<?= site_url("users1/get_requesting_employees") ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success' && response.employees.length > 0) {
                            // Populate dropdown
                            $('#employeeDropdown').append(new Option('-- اختر الموظف --', '', true, true)).trigger('change');
                            $.each(response.employees, function(i, employee) {
                                var option = new Option(employee.name + ' (' + employee.id + ')', employee.id, false, false);
                                $('#employeeDropdown').append(option);
                            });
                             $('#employeeDropdown').trigger('change');
                        } else {
                            $('#employeeDropdown').append(new Option('لا يوجد موظفين لديهم طلبات حالياً', '', true, true)).trigger('change');
                        }
                    },
                    error: function() {
                         $('#employeeDropdown').append(new Option('خطأ في تحميل البيانات', '', true, true)).trigger('change');
                    }
                });
                
                employeeModal.show();
            });

            // When "Generate Letter" button is clicked
            $('#generateLetterBtn').on('click', function() {
                const employeeId = $('#employeeDropdown').val();
                if (!employeeId) {
                    $('#employeeError').removeClass('d-none');
                    return;
                }
                 $('#employeeError').addClass('d-none');

                // Open the letter in a new tab
                const url = `<?= site_url('users1/generate_letter') ?>/${selectedLetterSlug}/${employeeId}`;
                window.open(url, '_blank');
                
                employeeModal.hide();
            });
        });
    </script>
</body>
</html>