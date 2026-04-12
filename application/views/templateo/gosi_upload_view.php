<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رفع بيانات GOSI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f4f6f9; }
        .card { box-shadow: 0 0 15px rgba(0,0,0,.05); border:0; }
        .card-header { background-color: #001f3f; color: white; }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-upload me-2"></i>رفع ملف GOSI (CSV)</h3>
                    </div>
                    <div class="card-body">

                        <?php if($this->session->flashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $this->session->flashdata('success'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <?php if($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $this->session->flashdata('error'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="<?php echo site_url('users1/process_gosi_upload'); ?>" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="<?php echo $csrf_token_name; ?>" value="<?php echo $csrf_hash; ?>">

                            <div class="mb-3">
                                <label for="company_code_select" class="form-label"><b>الشركة (مطلوب)</b></label>
                                <select class="form-select" id="company_code_select" name="company_code" required>
                                    <option value="" selected disabled>-- اختر الشركة --</option>
                                    <option value="1">شركة مرسوم</option>
                                    <option value="2">مكتب الدكتور صالح الجربوع</option>
                                </select>
                            </div>

                            <div class="mb-3" id="branch_select_container" style="display: none;">
                                <label for="branch_select" class="form-label"><b>الفرع (اختياري)</b></label>
                                <select class="form-select" id="branch_select" name="branch">
                                    <option value="">جميع الفروع</option>
                                    <option value="الرياض">الرياض</option>
                                    <option value="ابها">ابها</option>
                                    <option value="الخبر">الخبر</option>
                                </select>
                                <div class="form-text">
                                    إذا اخترت فرعاً، فإن "الاستبدال" سيؤثر فقط على موظفي هذا الفرع.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="upload_mode" class="form-label"><b>نوع العملية (مطلوب)</b></label>
                                <select class="form-select" id="upload_mode" name="upload_mode" required>
                                    <option value="" selected disabled>-- اختر نوع العملية --</option>
                                    <option value="add">إضافة (إضافة سجلات جديدة فقط)</option>
                                    <option value="replace">استبدال (حذف سجلات الشركة/الفرع ثم إضافة الجديد)</option>
                                </select>
                                <div class="form-text text-danger" id="replace-warning" style="display: none;">
                                    <b>تحذير:</b> سيقوم هذا الخيار بحذف جميع السجلات الحالية للشركة (أو الفرع المحدد) قبل إضافة السجلات الجديدة.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="gosi_file" class="form-label"><b>ملف CSV (مطلوب)</b></label>
                                <input class="form-control" type="file" id="gosi_file" name="gosi_file" accept=".csv" required>
                                <div class="form-text">
                                    يجب أن يحتوي الملف على الأعمدة التالية:
                                    <code>n1, n2, n3, n4, n5, n6</code>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-2"></i>رفع ومعالجة الملف</button>
                                <a href="<?php echo site_url('users1/main_hr1'); ?>" class="btn btn-secondary"><i class="fas fa-times me-2"></i>إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var companySelect = document.getElementById('company_code_select');
            var branchContainer = document.getElementById('branch_select_container');
            var uploadMode = document.getElementById('upload_mode');
            var warning = document.getElementById('replace-warning');

            // Show branch filter only for Company 1
            companySelect.addEventListener('change', function() {
                if (this.value === '1') {
                    branchContainer.style.display = 'block';
                } else {
                    branchContainer.style.display = 'none';
                }
            });

            // Show replace warning
            uploadMode.addEventListener('change', function() {
                if (this.value === 'replace') {
                    warning.style.display = 'block';
                } else {
                    warning.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>