<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل بيانات الموظف     </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;}
        body{font-family:'Tajawal',sans-serif;overflow-y:auto !important;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.8rem;color:var(--text-light);margin-bottom:32px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .page-title::after{content:'';position:absolute;width:100px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
        .table-card{background:rgba(255,255,255,.95);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.3);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:30px}
        
        .filter-label { font-size: 0.95rem; font-weight: 700; color: #001f3f; margin-bottom: 8px; display: block; }
        .form-control, .form-select { border-radius: 8px; font-size: 0.95rem; padding: 10px 15px; border: 1px solid #ced4da; transition: all 0.3s; }
        .form-control:focus, .form-select:focus { border-color: var(--marsom-orange); box-shadow: 0 0 0 0.2rem rgba(255, 140, 0, 0.25); }
        .btn-update { background: linear-gradient(45deg, var(--marsom-blue), #003366); color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: bold; font-size: 1.1rem; transition: 0.3s; }
        .btn-update:hover { background: linear-gradient(45deg, #003366, var(--marsom-blue)); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,31,63,0.3); color: white;}
        .section-title { color: var(--marsom-orange); font-family: 'El Messiri', sans-serif; font-weight: bold; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; margin-top: 15px;}
    </style>
</head>
<body>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="text-center flex-grow-1">
            <h1 class="page-title">تعديل بيانات الموظف</h1>
        </div>
        <div>
             <a href="<?= site_url('users1/main_hr1'); ?>" class="btn btn-light shadow-sm fw-bold">
                <i class="fas fa-arrow-right me-1"></i> رجوع للقائمة
             </a>
        </div>
    </div>

    <div class="card table-card mb-4">
        <form action="<?= base_url('users1/modify_staff_record/'.$employee['id']) ?>" method="POST" id="editEmployeeForm">
            
            <?php if(isset($csrf_token_name) && isset($csrf_hash)): ?>
                <input type="hidden" name="<?= $csrf_token_name ?>" value="<?= $csrf_hash ?>">
            <?php endif; ?>

            <h4 class="section-title"><i class="fas fa-id-card me-2"></i> البيانات الأساسية</h4>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="filter-label">الرقم الوظيفي</label>
                    <input type="text" name="employee_id" class="form-control" value="<?= set_value('employee_id', $employee['employee_id'] ?? '') ?>" required>
                </div>
                
                <div class="col-md-4">
                    <label class="filter-label">اسم الموظف</label>
                    <input type="text" name="subscriber_name" class="form-control" value="<?= set_value('subscriber_name', $employee['subscriber_name'] ?? '') ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="filter-label">رقم الهوية / الإقامة</label>
                    <input type="text" name="id_number" class="form-control" value="<?= set_value('id_number', $employee['id_number'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                    <label class="filter-label">تاريخ انتهاء الهوية</label>
                    <input type="date" name="id_expiry" class="form-control" value="<?= set_value('id_expiry', $employee['id_expiry'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                    <label class="filter-label">تاريخ انتهاء الإقامة</label>
                    <input type="date" name="Iqama_expiry_date" class="form-control" value="<?= set_value('Iqama_expiry_date', $employee['Iqama_expiry_date'] ?? '') ?>">
                </div>
            </div>

            <h4 class="section-title"><i class="fas fa-user me-2"></i> البيانات الشخصية</h4>
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <label class="filter-label">الجنسية</label>
                    <input type="text" name="nationality" class="form-control" value="<?= set_value('nationality', $employee['nationality'] ?? '') ?>">
                </div>

                <div class="col-md-3">
                    <label class="filter-label">تاريخ الميلاد</label>
                    <input type="date" name="birth_date" class="form-control" value="<?= set_value('birth_date', $employee['birth_date'] ?? '') ?>">
                </div>

                <div class="col-md-3">
                    <label class="filter-label">الجنس</label>
                    <select name="gender" class="form-select select2">
                        <option value="ذكر" <?= (isset($employee['gender']) && $employee['gender'] == 'ذكر') ? 'selected' : '' ?>>ذكر</option>
                        <option value="أنثى" <?= (isset($employee['gender']) && $employee['gender'] == 'أنثى') ? 'selected' : '' ?>>أنثى</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="filter-label">الحالة الاجتماعية</label>
                    <select name="marital" class="form-select select2">
                        <option value="">اختر...</option>
                        <option value="أعزب" <?= (isset($employee['marital']) && $employee['marital'] == 'أعزب') ? 'selected' : '' ?>>أعزب</option>
                        <option value="متزوج" <?= (isset($employee['marital']) && $employee['marital'] == 'متزوج') ? 'selected' : '' ?>>متزوج</option>
                        <option value="مطلق" <?= (isset($employee['marital']) && $employee['marital'] == 'مطلق') ? 'selected' : '' ?>>مطلق</option>
                        <option value="أرمل" <?= (isset($employee['marital']) && $employee['marital'] == 'أرمل') ? 'selected' : '' ?>>أرمل</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="filter-label">الديانة</label>
                    <input type="text" name="religion" class="form-control" value="<?= set_value('religion', $employee['religion'] ?? '') ?>">
                </div>
                <h4 class="section-title"><i class="fas fa-university me-2"></i> البيانات البنكية</h4>
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="filter-label">اسم البنك (Bank Name)</label>
                    <select name="n3" class="form-select select2" dir="ltr">
                        <option value="">-- Select Bank --</option>
                        <?php 
                        $bank_list = [
                            'Al Rajhi Bank', 'AlBank AlSaudi AlFransi', 'Alinma Bank', 'Arab National Bank',
                            'Bank Albilad', 'National Bank of Bahrain', 'Bank AlJazira', 'Bank Muscat',
                            'Deutsche Bank', 'Emirates Bank Intl', 'Gulf International Bank', 'National Bank of Kuwait',
                            'National Commercial Bank', 'Riyad Bank', 'First Abu Dhabi Bank', 'National Bank of Pakistan',
                            'Saudi Investment Bank', 'State Bank of India', 'T.C. Ziraat Bankasi', 'The Saudi British Bank',
                            'Standard Chartered Bank', 'Saudi Cairo Bank', 'Mufg Bank', 'JP Morgan Chase Bank', 'ICBK Bank'
                        ];
                        // نستخدم n3 لأنك ذكرت أن اسم البنك سيتم حفظه في عمود n3
                        $current_bank = trim($employee['n3'] ?? '');
                        foreach ($bank_list as $bank) {
                            $selected = ($current_bank === $bank) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($bank) . '" ' . $selected . '>' . htmlspecialchars($bank) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="filter-label">رقم الآيبان (IBAN)</label>
                    <input type="text" name="n2" class="form-control" dir="ltr" placeholder="SA0000000000000000000000" value="<?= set_value('n2', $employee['n2'] ?? '') ?>">
                </div>
            </div>
            </div>

            <h4 class="section-title"><i class="fas fa-address-book me-2"></i> بيانات الاتصال</h4>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="filter-label">رقم الجوال</label>
                    <input type="text" name="phone" class="form-control" value="<?= set_value('phone', $employee['phone'] ?? '') ?>" dir="ltr">
                </div>

                <div class="col-md-4">
                    <label class="filter-label">البريد الشخصي</label>
                    <input type="email" name="personal_email" class="form-control" value="<?= set_value('personal_email', $employee['personal_email'] ?? '') ?>" dir="ltr">
                </div>

                <div class="col-md-4">
                    <label class="filter-label">البريد الرسمي (العمل)</label>
                    <input type="email" name="email" class="form-control" value="<?= set_value('email', $employee['email'] ?? '') ?>" dir="ltr">
                </div>

                <div class="col-md-12">
                    <label class="filter-label">العنوان الوطني / السكن</label>
                    <input type="text" name="address" class="form-control" value="<?= set_value('address', $employee['address'] ?? '') ?>">
                </div>
            </div>

            <h4 class="section-title"><i class="fas fa-briefcase me-2"></i> البيانات الوظيفية</h4>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="filter-label">المسمى الوظيفي</label>
                    <input type="text" name="profession" class="form-control" value="<?= set_value('profession', $employee['profession'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                    <label class="filter-label">المدير المباشر</label>
                    <input type="text" name="manager" class="form-control" value="<?= set_value('manager', $employee['manager'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                    <label class="filter-label">تاريخ الانضمام (المباشرة)</label>
                    <input type="date" name="joining_date" class="form-control" value="<?= set_value('joining_date', $employee['joining_date'] ?? '') ?>">
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-update px-5 me-2"><i class="fas fa-save me-2"></i> تحديث وحفظ البيانات</button>
                    <a href="<?= site_url('users1/main_emp') ?>" class="btn btn-secondary px-5" style="border-radius: 8px; padding: 12px 30px; font-weight: bold; font-size: 1.1rem;">إلغاء</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // تفعيل Select2 للقوائم المنسدلة
        $('.select2').select2({
            theme: 'bootstrap-5',
            dir: 'rtl',
            width: '100%'
        });

        // عرض رسائل النجاح أو الخطأ باستخدام SweetAlert2
        <?php if($this->session->flashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'تم بنجاح',
                text: '<?= $this->session->flashdata('success'); ?>',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#001f3f'
            });
        <?php endif; ?>

        <?php if($this->session->flashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: '<?= $this->session->flashdata('error'); ?>',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#d33'
            });
        <?php endif; ?>
    });
</script>
</body>
</html>