<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة عمل يوم السبت</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <style>
    :root { 
        --marsom-blue: #001f3f;
        --marsom-orange: #FF8C00;
        --text-light: #fff;
        --text-dark: #343a40;
    }
    body { 
        font-family:'Tajawal',sans-serif; 
        background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%); 
        background-size:400% 400%; 
        animation:grad 20s ease infinite; 
        color:var(--text-dark); /* Changed to dark text for content cards */
    }
    @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
    .particles{position:fixed;inset:0;overflow:hidden;z-index:-1}
    .particle{position:absolute;background:rgba(255,140,0,.1);clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);animation:float 25s infinite ease-in-out;opacity:0;}
    @keyframes float{0%{transform:translateY(0) rotate(0);opacity:0}20%{opacity:1}80%{opacity:1}100%{transform:translateY(-100vh) rotate(360deg);opacity:0}}
    .main-container{padding:30px 15px;z-index:1}
    .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.8rem;color:var(--text-light);margin-bottom:32px;text-align:center;text-shadow:0 3px 6px rgba(0,0,0,.4)}
    .top-actions a{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;}
    .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}

    /* === THE FIX IS HERE === */

    /* 1. Make the main card light and semi-transparent */
    .content-card {
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(8px);
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,.15);
        padding: 30px;
    }
    .content-card h4, .content-card p {
        color: var(--text-dark); /* Ensure text inside the card is dark */
    }

    /* 2. Reset form elements to their default (light) style */
    .form-control, .form-select, .select2-container--bootstrap-5 .select2-selection {
        background-color: #fff;
        border-color: #dee2e6;
        color: #212529;
    }
    .form-control:focus, .form-select:focus, .select2-container--bootstrap-5.select2-container--focus .select2-selection {
        border-color: var(--marsom-orange);
        box-shadow: 0 0 0 .25rem rgba(255,140,0,.25);
    }
    
    /* 3. Style the calendar for a light background */
    .fc .fc-daygrid-day.fc-day-sat { background-color: rgba(255, 140, 0, 0.1) !important; }
    .fc-daygrid-day-frame:hover { background-color: rgba(0, 31, 63, 0.1); cursor: pointer; }
    .fc-event { background-color: var(--marsom-orange) !important; border-color: var(--marsom-orange) !important; font-size: 0.75rem; }
    .fc-button-primary { background-color: var(--marsom-blue) !important; border-color: var(--marsom-blue) !important; }
    .fc-button-primary:hover { background-color: #003366 !important; }
    
</style>
</head>
<body>

<div class="top-actions">
    <a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
    <a href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="container-fluid my-4">
    <div class="text-center mb-4"><h1 class="page-title">إدارة عمل يوم السبت</h1></div>
    
    <div id="alert-container"></div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="content-card h-100">
                <h4 class="text-light"><i class="fas fa-calendar-day me-2"></i>عرض التقويم</h4>
                <p class="text-white-50">انقر على أي يوم سبت في التقويم لإسناد موظفين للعمل أو حذفهم.</p>
                <div id="calendar"></div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="content-card h-100">
                <h4 class="text-light"><i class="fas fa-users-cog me-2"></i>إسناد جماعي</h4>
                <p class="text-white-50">اختر تاريخاً ومجموعة من الموظفين لإضافتهم دفعة واحدة.</p>
                <form id="bulkAssignForm">
                    <div class="mb-3">
                        <label for="saturday_date_bulk" class="form-label fw-bold">اختر تاريخ يوم السبت</label>
                        <input type="date" id="saturday_date_bulk" class="form-control" required>
                        <div class="invalid-feedback" id="date-error" style="display:none;">الرجاء اختيار يوم سبت فقط.</div>
                    </div>
                    <div class="mb-3">
                        <label for="employee_ids_bulk" class="form-label fw-bold">اختر الموظفين (ابحث بالاسم أو الرقم الوظيفي)</label>
                        <select class="form-select" id="employee_ids_bulk" multiple required>
                            <?php foreach($all_employees as $emp): ?>
                                <option value="<?php echo html_escape($emp['username']); ?>" data-id="<?php echo html_escape($emp['username']); ?>">
                                    <?php echo html_escape($emp['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save me-2"></i>حفظ الإسناد</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="assignmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #0a2a4b; border-color: var(--glass-border);">
            <div class="modal-header">
                <h5 class="modal-title text-light">إسناد موظفين ليوم <span id="modalDate"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="modalAssignForm">
                    <input type="hidden" id="modal_saturday_date">
                    <div class="mb-3">
                        <label for="modal_employee_ids" class="form-label">اختر موظفًا أو أكثر (ابحث بالاسم أو الرقم الوظيفي):</label>
                        <select class="form-select" id="modal_employee_ids" multiple>
                            <?php foreach($all_employees as $emp): ?>
                                <option value="<?php echo html_escape($emp['username']); ?>" data-id="<?php echo html_escape($emp['username']); ?>">
                                    <?php echo html_escape($emp['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" id="saveAssignmentBtn">حفظ</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const assignmentModal = new bootstrap.Modal(document.getElementById('assignmentModal'));
    const csrfName = '<?php echo $csrf_name; ?>';
    let csrfHash = '<?php echo $csrf_hash; ?>';

    // THE FIX: Custom matcher for Select2 to search by both text and data-id
    function customMatcher(params, data) {
        if ($.trim(params.term) === '') return data;
        if (typeof data.text === 'undefined' || typeof data.element === 'undefined' || typeof data.element.dataset.id === 'undefined') return null;

        var term = params.term.toLowerCase();
        var text = data.text.toLowerCase();
        var id = data.element.dataset.id.toLowerCase();

        if (text.indexOf(term) > -1 || id.indexOf(term) > -1) {
            return data;
        }
        return null;
    }

    $('#modal_employee_ids, #employee_ids_bulk').select2({
        theme: 'bootstrap-5',
        placeholder: 'ابحث بالاسم أو الرقم الوظيفي...',
        dropdownParent: $('#modal_employee_ids').closest('.modal-body'),
        allowClear: true,
        matcher: customMatcher // Apply the custom search function
    });
    // For the bulk form, the dropdown parent is the body
    $('#employee_ids_bulk').select2({
        theme: 'bootstrap-5',
        placeholder: 'ابحث بالاسم أو الرقم الوظيفي...',
        allowClear: true,
        matcher: customMatcher
    });
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'ar',
        height: 'auto',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth' },
        events: "<?php echo site_url('users1/get_saturday_assignments_ajax'); ?>",
        dateClick: function(info) {
            if (new Date(info.dateStr).getDay() !== 6) {
                showAlert('يمكنك فقط إسناد العمل لأيام السبت.', 'warning');
                return;
            }
            $('#modalDate').text(info.dateStr);
            $('#modal_saturday_date').val(info.dateStr);
            $('#modal_employee_ids').val(null).trigger('change');
            assignmentModal.show();
        },
        eventClick: function(info) {
            if (confirm(`هل أنت متأكد من حذف إسناد "${info.event.title}" لهذا اليوم؟`)) {
                const props = info.event.extendedProps;
                $.ajax({
                    url: "<?php echo site_url('users1/remove_saturday_assignment_ajax'); ?>",
                    type: 'POST',
                    data: { [csrfName]: csrfHash, employee_id: props.employee_id, saturday_date: info.event.startStr },
                    dataType: 'json',
                    success: function(response){
                        if(response.status === 'success'){
                            info.event.remove();
                            showAlert('تم حذف الإسناد بنجاح.', 'success');
                        } else {
                            showAlert(response.message || 'فشل حذف الإسناد.', 'danger');
                        }
                    },
                    error: () => showAlert('خطأ في الاتصال بالخادم.', 'danger')
                });
            }
        }
    });
    calendar.render();

    $('#saveAssignmentBtn').on('click', function() {
        const employeeIds = $('#modal_employee_ids').val();
        const saturdayDate = $('#modal_saturday_date').val();
        if (!employeeIds || employeeIds.length === 0) {
            alert('الرجاء اختيار موظف واحد على الأقل.');
            return;
        }
        
        saveAssignments(employeeIds, saturdayDate, () => {
            assignmentModal.hide();
            calendar.refetchEvents();
        });
    });

    $('#bulkAssignForm').on('submit', function(e) {
        e.preventDefault();
        const dateInput = document.getElementById('saturday_date_bulk');
        const selectedDate = new Date(dateInput.value + 'T00:00:00'); // Ensure it's treated as local time
        if (selectedDate.getDay() !== 6) {
            showAlert('الرجاء اختيار يوم سبت فقط.', 'danger');
            return;
        }
        
        const employeeIds = $('#employee_ids_bulk').val();
        if (!employeeIds || employeeIds.length === 0) {
            showAlert('الرجاء اختيار موظف واحد على الأقل.', 'danger');
            return;
        }

        saveAssignments(employeeIds, dateInput.value, () => {
            calendar.refetchEvents();
            $('#bulkAssignForm')[0].reset();
            $('#employee_ids_bulk').trigger('change');
        });
    });

    function saveAssignments(employeeIds, date, callback) {
        $.ajax({
            url: "<?php echo site_url('users1/assign_saturday_work_ajax'); ?>",
            type: 'POST',
            data: { [csrfName]: csrfHash, employee_ids: employeeIds, saturday_date: date },
            dataType: 'json',
            success: function(response){
                if(response.status === 'success'){
                    showAlert('تم حفظ الإسناد بنجاح.', 'success');
                    if(callback) callback();
                } else {
                    showAlert(response.message || 'فشل حفظ الإسناد.', 'danger');
                }
            },
            error: () => showAlert('خطأ في الاتصال بالخادم.', 'danger')
        });
    }

    function showAlert(message, type = 'success') {
        const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
        $('#alert-container').html(alertHtml);
        setTimeout(() => $('.alert').alert('close'), 5000);
    }
});
</script>

</body>
</html>