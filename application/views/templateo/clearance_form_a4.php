<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>نموذج إخلاء طرف</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        /* Keep ALL your existing CSS styles */
        @page { size: A4; margin: 12mm 12mm 14mm 12mm; }
        html, body { font-family:'Tajawal',system-ui,sans-serif; background:#f4f6f9; color:#111; }
        .a4-sheet { width:210mm; min-height:297mm; margin:10mm auto; background:#fff; box-shadow:0 6px 24px rgba(0,0,0,.08); padding:14mm; }
        .no-print { display:flex; flex-wrap:wrap; gap:.5rem; justify-content:center; margin:12px auto; padding: 0 14mm; }
        .btn { border:1px solid #001f3f; color:#001f3f; background:#fff; padding:8px 14px; border-radius:8px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
        .btn:hover { background:#001f3f; color:#fff; }
        .btn-success { border-color:#198754; color:#198754; background-color: #fff;}
        .btn-success:hover { background:#198754; color:#fff; }
        .header { display:flex; align-items:center; justify-content:space-between; gap:16px; padding-bottom:10px; border-bottom:2px solid #001f3f; margin-bottom:14px; }
        .brand h1 { font-size:20px; margin:0; font-weight:800; color:#001f3f; }
        .section-title { margin:14px 0 8px; padding:8px 10px; background:#f7f9fc; border-right:4px solid #FF8C00; font-weight:800; }
        .print-table { width:100%; border-collapse:collapse; margin-top:6px; }
        .print-table th, .print-table td { border:1px solid #d9dee3; padding:6px 8px; font-size:13px; vertical-align:middle; }
        .print-table th { background:#001f3f; color:#fff; font-weight:700; text-align:center; }
        .center{text-align:center}
        .search-container { max-width: 600px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 6px 24px rgba(0,0,0,.08); text-align: center; }
        .search-container h2 { font-weight:800; color:#001f3f; margin-bottom:20px; }
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#000;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .departments-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; }
        .form-check { padding: 0.5rem 1rem; border: 1px solid #eee; border-radius: 8px; transition: all 0.2s ease-in-out; }
        .form-check:hover { background-color: #f7f9fc; border-color: #001f3f; }
        .form-check-input { float: right; margin-left: .75em; width:1.2em; height:1.2em; }
        .form-check-label { margin-right: 1.5em; font-weight: 500;}

        @media print {
            body { background:#fff; }
            .a4-sheet { margin:0; width:auto; min-height:auto; box-shadow:none; padding:0; }
            .no-print { display:none !important; }
            .departments-grid { display: block; }
            .form-check { border: none; padding: 0.2rem 0; }
            .form-check-input { display: none; }
            .form-check-label::before { content: '☐ '; font-family: sans-serif; }
            .form-check-input:checked + .form-check-label::before { content: '☑ '; }
        }
    </style>
</head>
<body>

<?php if (empty($row)): ?>
    <div class="search-container">
       <div class="top-actions" style="color:black;">
        <a href="<?php echo site_url('users1/main_hr1'); ?>" style="text-decoration: none;"><i class="fas fa-home"></i> الرئيسية</a>
       </div>
        <h2>بدء إجراءات إخلاء الطرف</h2>
        <p>الرجاء اختيار طلب الاستقالة المعتمد للموظف لبدء عملية إخلاء الطرف.</p>
        <?php if (!empty($err)): ?>
            <div class="alert alert-danger mt-3"><?php echo html_escape($err); ?></div>
        <?php endif; ?>
         <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger mt-3"><?php echo $this->session->flashdata('error'); ?></div>
         <?php endif; ?>
        <form method="get" action="<?php echo site_url('users1/clearance_form'); ?>" class="mt-4">
    <div class="mb-3">
        <select name="resignation_id" id="resignation_search" class="form-select" required>
            <option></option> <?php if(!empty($resignations)): // Check if $resignations is not empty ?>
                <?php foreach($resignations as $res): // Loop through the fetched resignations ?>
                    <option value="<?php echo html_escape($res['id']); ?>">
                        <?php echo html_escape($res['emp_name']) . ' (' . html_escape($res['emp_id']) . ') - تاريخ الطلب: ' . html_escape($res['date']); ?>
                    </option>
                <?php endforeach; ?>
            <?php else: ?>
                 <option value="" disabled>لا توجد طلبات متاحة</option>
            <?php endif; ?>
        </select>
    </div>
    <button class="btn btn-success" type="submit">بدء إخلاء الطرف</button>
</form>
    </div>

<?php else: ?>
    <div class="no-print">
        <a class="btn" href="<?php echo site_url('users1/clearance_form'); ?>"><i class="fas fa-search me-2"></i>العودة للبحث</a>
        <a class="btn" href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-home me-2"></i>الرئيسية </a>

        <?php $submit_button_text = ($clearance_in_progress ?? false) ? 'إلغاء الحالي وإعادة التقديم' : 'حفظ وتقديم للمدير المباشر'; ?>
        <button class="btn btn-success" type="submit" form="clearanceForm" id="submitClearanceBtn">
            <i class="fas fa-paper-plane me-2"></i> <?php echo $submit_button_text; ?>
        </button>
        <a class="btn" href="javascript:window.print()"><i class="fas fa-print me-2"></i>طباعة</a>
        <button class="btn" id="exportExcelBtn"><i class="fas fa-file-excel me-2"></i>تصدير Excel</button>
        <button class="btn" id="exportPdfBtn"><i class="fas fa-file-pdf me-2"></i>تصدير PDF</button>
    </div>

     <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger mt-3 no-print"><?php echo $this->session->flashdata('error'); ?></div>
     <?php endif; ?>
     <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success mt-3 no-print"><?php echo $this->session->flashdata('success'); ?></div>
     <?php endif; ?>

    <form id="clearanceForm" method="post" action="<?php echo site_url('users1/initiate_or_resubmit_clearance'); ?>">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
        <input type="hidden" name="resignation_id" value="<?php echo html_escape($row['id']); ?>">
        <input type="hidden" name="employee_id" value="<?php echo html_escape($row['emp_id']); ?>">
        <input type="hidden" name="direct_manager_id" value="<?php echo html_escape($direct_manager['username'] ?? ''); ?>">

        <input type="hidden" name="resubmit_confirmation" id="resubmit_confirmation" value="">
        <div class="a4-sheet">
            <div class="header">
               <div class="brand"><h1>نموذج إخلاء طرف</h1></div>
               <div class="meta"><div class="date">التاريخ: <?php echo date('Y-m-d'); ?></div></div>
             </div>
             <div class="section-title">بيانات الموظف</div>
             <table class="print-table">
                 <tbody>
                     <tr>
                         <td class="center" style="width:20%;">اسم الموظف</td><td><?php echo html_escape($row['emp_name']); ?></td>
                         <td class="center" style="width:20%;">الرقم الوظيفي</td><td><?php echo html_escape($row['emp_id']); ?></td>
                     </tr>
                     <tr>
                         <td class="center">تاريخ آخر يوم عمل</td><td><?php echo html_escape($row['date_of_the_last_working']); ?></td>
                         <td class="center">سبب الاستقالة</td><td><?php echo html_escape($row['reason_for_resignation']); ?></td>
                     </tr>
                 </tbody>
             </table>

            <div class="section-title">اختر الإدارات المطلوبة لإخلاء الطرف</div>
            <div class="departments-grid p-3 border rounded mt-3">
                 <?php if (!empty($direct_manager)): ?>
                 <div class="form-check">
                     <input class="form-check-input" type="checkbox" name="include_direct_manager" value="1" id="dept_dm" checked>
                     <label class="form-check-label fw-bold" for="dept_dm">
                         المدير المباشر: <?php echo html_escape($direct_manager['name']); ?>
                     </label>
                 </div>
                 <?php else: ?>
                 <div class="alert alert-warning p-2">لم يتم العثور على مدير مباشر لهذا الموظف في الهيكل التنظيمي.</div>
                 <?php endif; ?>

                 <?php if(!empty($departments)): ?>
                   <?php foreach ($departments as $dept): ?>
    <div class="form-check">
        <input class="form-check-input department-checkbox" type="checkbox" name="department_ids[]" value="<?php echo $dept['id']; ?>" id="dept_<?php echo $dept['id']; ?>" data-dept-id="<?php echo $dept['id']; ?>">
        <label class="form-check-label" for="dept_<?php echo $dept['id']; ?>">
            <?php echo html_escape($dept['name']); ?>
        </label>
    </div>

    <?php if ($dept['id'] == 12): ?>
        <div id="finance_options_container" class="ms-4 mt-2 p-2 border rounded bg-light" style="display: none;">
            <p class="mb-2 small fw-bold text-primary">اختر المسؤول المالي:</p>
            
            <div class="form-check">
                <input class="form-check-input" type="radio" name="finance_approver" id="finance_2909" value="2909" checked>
                <label class="form-check-label" for="finance_2909">
                    محمد مجدي سيد رشوان  (2909) </label>
            </div>
            
            <div class="form-check">
                <input class="form-check-input" type="radio" name="finance_approver" id="finance_2833" value="2833">
                <label class="form-check-label" for="finance_2833">
                    نواف مخلد الهويمل المطيري  (2833) </label>
            </div>
        </div>
    <?php endif; ?>
    <?php endforeach; ?>
                 <?php endif; ?>
            </div>
        </div>
    </form>
<?php endif; ?>
<div class="modal fade" id="resubmitConfirmModal" tabindex="-1" aria-labelledby="resubmitConfirmModalLabel" aria-hidden="true" dir="rtl">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-warning text-dark border-0">
        <h5 class="modal-title" id="resubmitConfirmModalLabel">
            <i class="fas fa-exclamation-triangle me-2"></i> تأكيد إعادة التقديم
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>
      <div class="modal-body">
        <p class="fs-6">توجد مهام إخلاء طرف قائمة بالفعل لهذا الطلب.</p>
        <p class="fw-bold">هل أنت متأكد أنك ترغب بإلغاء المهام الحالية المعلقة وإنشاء مهام جديدة بناءً على اختيارك الحالي؟</p>
        <p class="text-muted small">(سيتم إلغاء أي مهام لم يتم الموافقة عليها بعد)</p>
      </div>
      <div class="modal-footer border-0 justify-content-center">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> إلغاء
        </button>
        <button type="button" class="btn btn-warning px-4" id="confirmResubmitBtn">
            <i class="fas fa-check me-1"></i> نعم، إلغاء وإعادة التقديم
        </button>
      </div>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<script>
$(document).ready(function() {
    console.log("Document ready. Initializing clearance form script."); // Debug

    // Keep Select2 initialization
    if ($('#resignation_search').length) {
        console.log("Initializing Select2 for resignation search."); // Debug
        $('#resignation_search').select2({
            theme: 'bootstrap-5',
            placeholder: 'ابحث عن طلب استقالة بالاسم أو الرقم الوظيفي',
            allowClear: true
        });
    }

    // --- Confirmation logic for resubmission ---
    const clearanceForm = document.getElementById('clearanceForm');
    const resubmitConfirmationInput = document.getElementById('resubmit_confirmation');
    const clearanceInProgress = <?php echo json_encode($clearance_in_progress ?? false); ?>;
    const submitButton = document.getElementById('submitClearanceBtn');
    const resubmitModalElement = document.getElementById('resubmitConfirmModal');
    const confirmResubmitBtn = document.getElementById('confirmResubmitBtn'); // Button inside the modal

    // Log initial state for debugging
    console.log("Clearance In Progress Flag:", clearanceInProgress);
    console.log("Form Element:", clearanceForm ? 'Found' : 'NOT FOUND');
    console.log("Submit Button Element:", submitButton ? 'Found' : 'NOT FOUND');
    console.log("Modal Element:", resubmitModalElement ? 'Found' : 'NOT FOUND');
    console.log("Modal Confirm Button Element:", confirmResubmitBtn ? 'Found' : 'NOT FOUND');
$('.department-checkbox').on('change', function() {
        var deptId = $(this).data('dept-id');
        if (deptId == 12) {
            if ($(this).is(':checked')) {
                $('#finance_options_container').slideDown();
            } else {
                $('#finance_options_container').slideUp();
            }
        }
    });

    let resubmitModal = null; // Initialize Bootstrap modal instance
    if (resubmitModalElement && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
         try {
            resubmitModal = new bootstrap.Modal(resubmitModalElement);
            console.log("Bootstrap Modal instance created successfully."); // Debug
         } catch (e) {
            console.error("Error creating Bootstrap Modal instance:", e); // Debug
         }
    } else {
        console.error("Could not create Bootstrap Modal instance. Element or Bootstrap JS missing?"); // Debug
    }


    // Ensure all required elements were found before attaching listeners
    if (clearanceForm && submitButton && resubmitModal && confirmResubmitBtn) {
        console.log("Attaching submit listener to clearanceForm."); // Debug
        clearanceForm.addEventListener('submit', function(event) {
            console.log("Clearance form submit event triggered."); // Debug

            // Disable button immediately to prevent double-clicks
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جاري التحقق...';

            const confirmationValue = resubmitConfirmationInput.value;
            console.log("Current resubmit_confirmation value:", confirmationValue); // Debug

            // Check if clearance is in progress and confirmation hasn't been set yet
            if (clearanceInProgress && confirmationValue !== 'yes') {
                console.log("Clearance in progress and not yet confirmed. Preventing default submit."); // Debug
                event.preventDefault(); // Stop the default form submission

                console.log("Attempting to show confirmation modal."); // Debug
                try {
                    resubmitModal.show(); // Show the Bootstrap modal
                     console.log("Bootstrap modal show() called."); // Debug
                } catch(e) {
                    console.error("Error showing modal:", e); // Debug
                     // Fallback alert if modal fails
                     alert('حدث خطأ أثناء عرض نافذة التأكيد. يرجى مراجعة الكونسول.');
                     // Re-enable button since modal failed
                     submitButton.disabled = false;
                     submitButton.innerHTML = '<i class="fas fa-paper-plane me-2"></i> <?php echo addslashes($submit_button_text); ?>';
                }

            } else {
                 // If not in progress OR confirmation already set to 'yes'
                 console.log("Condition for modal not met OR already confirmed. Allowing submission."); // Debug
                 if(confirmationValue !== 'yes') { // Standard submission
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جاري التقديم...';
                 } else { // Resubmission already confirmed
                     submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جاري الإلغاء والتقديم...';
                 }
                 console.log("Form will proceed to submit."); // Debug
                 // Allow form submission to proceed naturally (button remains disabled)
            }
        });

        // Add listener for the modal's "Yes" (confirm) button
        console.log("Attaching click listener to modal confirm button."); // Debug
        confirmResubmitBtn.addEventListener('click', function() {
            console.log("Modal confirm button ('Yes') clicked."); // Debug
            // Hide the modal
            console.log("Hiding modal."); // Debug
            resubmitModal.hide();
            // Set the hidden input value to indicate confirmation
            console.log("Setting resubmit_confirmation to 'yes'."); // Debug
            resubmitConfirmationInput.value = 'yes';
            // Update the main submit button's text/state to show processing
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جاري الإلغاء والتقديم...';
            // Programmatically submit the form now that confirmation is given
            console.log("Submitting clearanceForm programmatically after confirmation."); // Debug
            clearanceForm.submit();
        });

        // Add listener for when the modal is fully hidden (closed by cancel, 'x', or confirm)
        if (resubmitModalElement) {
             console.log("Attaching hidden.bs.modal listener."); // Debug
            resubmitModalElement.addEventListener('hidden.bs.modal', function (event) {
                 console.log("Modal hidden event fired."); // Debug
                // Check if the form *wasn't* submitted (i.e., user clicked cancel/'x') AND the button is still disabled
                if (resubmitConfirmationInput.value !== 'yes' && submitButton.disabled) {
                    console.log("Modal closed without confirmation. Re-enabling submit button."); // Debug
                    submitButton.disabled = false; // Re-enable the main submit button
                    submitButton.innerHTML = '<i class="fas fa-paper-plane me-2"></i> <?php echo addslashes($submit_button_text); ?>'; // Reset its text
                } else {
                     // This case means either the form was submitted (value is 'yes') or the button wasn't disabled (shouldn't happen often here)
                     console.log("Modal hidden, but form was likely submitted or button state is unexpected."); // Debug
                }
            });
         }

    } else {
         // Log an error if any essential element wasn't found during setup
         console.error("Initialization failed: Could not attach listeners because one or more required elements (form, buttons, modal instance) were not found or initialized.");
    }
    // --- Excel Export Logic ---
    const exportExcelBtn = document.getElementById('exportExcelBtn');
    if (exportExcelBtn) {
        exportExcelBtn.addEventListener('click', function() {
            // 1. Get Employee Data from the table
            const empName = "<?php echo addslashes($row['emp_name']); ?>";
            const empId = "<?php echo addslashes($row['emp_id']); ?>";
            const lastDay = "<?php echo addslashes($row['date_of_the_last_working']); ?>";
            const reason = "<?php echo addslashes($row['reason_for_resignation']); ?>";
            const today = new Date().toISOString().slice(0, 10);

            // 2. Prepare CSV content
            let csvContent = "data:text/csv;charset=utf-8,\uFEFF"; // \uFEFF is the BOM for Arabic in Excel
            csvContent += "نموذج إخلاء طرف\r\n";
            csvContent += "التاريخ:," + today + "\r\n";
            csvContent += "\r\n"; // Empty line
            csvContent += "بيانات الموظف\r\n";
            csvContent += "البند,البيان\r\n";
            csvContent += "اسم الموظف," + empName + "\r\n";
            csvContent += "الرقم الوظيفي," + empId + "\r\n";
            csvContent += "تاريخ آخر يوم عمل," + lastDay + "\r\n";
            csvContent += "سبب الاستقالة," + reason + "\r\n";

            // 3. Create and trigger download link
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", `clearance_form_${empId}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }

    // --- PDF Export Logic ---
    const exportPdfBtn = document.getElementById('exportPdfBtn');
    if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', async function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // 1. Load Arabic Font (this is the complex part)
            // We fetch a font that supports Arabic characters.
            const fontUrl = 'https://fonts.gstatic.com/s/tajawal/v9/Iura6YBj_oCad4k1nzSBC45I.woff';
            const fontResponse = await fetch(fontUrl);
            const fontBuffer = await fontResponse.arrayBuffer();
            const fontBase64 = btoa(new Uint8Array(fontBuffer).reduce((data, byte) => data + String.fromCharCode(byte), ''));

            doc.addFileToVFS('Tajawal-Regular.ttf', fontBase64);
            doc.addFont('Tajawal-Regular.ttf', 'Tajawal', 'normal');
            doc.setFont('Tajawal');

            // 2. Get Employee Data
            const empName = "<?php echo addslashes($row['emp_name']); ?>";
            const empId = "<?php echo addslashes($row['emp_id']); ?>";
            const lastDay = "<?php echo addslashes($row['date_of_the_last_working']); ?>";
            const reason = "<?php echo addslashes($row['reason_for_resignation']); ?>";

            // 3. Add content to PDF (RTL is handled automatically by autoTable with an Arabic font)
            doc.setFontSize(20);
            doc.text("نموذج إخلاء طرف", 105, 20, { align: 'center' });

            doc.setFontSize(12);
            doc.text(`التاريخ: ${new Date().toISOString().slice(0, 10)}`, 200, 30, { align: 'right' });
            
            // Use autoTable to easily create tables
            doc.autoTable({
                startY: 40,
                head: [['بيانات الموظف', '']],
                body: [
                    ['اسم الموظف', empName],
                    ['الرقم الوظيفي', empId],
                    ['تاريخ آخر يوم عمل', lastDay],
                    ['سبب الاستقالة', reason]
                ],
                theme: 'grid',
                headStyles: { font: 'Tajawal', halign: 'center', fillColor: [0, 31, 63] },
                bodyStyles: { font: 'Tajawal' },
                didParseCell: function (data) {
                    // This aligns Arabic text to the right in each cell
                    data.cell.styles.halign = 'right';
                }
            });

            // 4. Save the PDF
            doc.save(`clearance_form_${empId}.pdf`);
        });
    }
});
</script>
</body>
</html>