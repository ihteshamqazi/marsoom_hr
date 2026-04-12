<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مهامي - لوحة التحكم</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --font-main: 'Tajawal', sans-serif;
            --primary-color: #001f3f;
            --accent-color: #FF8C00;
            --bg-body: #f0f2f5;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg-body);
            color: #333;
            min-height: 100vh;
        }

        /* Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary-color), #1e3c72);
            color: white;
            padding: 2rem 0 4rem;
            margin-bottom: -3rem;
            border-radius: 0 0 2rem 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .page-title { font-family: 'El Messiri', sans-serif; font-weight: 700; }
        .top-btn { background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; padding: 8px 15px; text-decoration: none; transition: 0.3s; }
        .top-btn:hover { background: white; color: var(--primary-color); }

        /* Task Grid (Kanban Style Cards) */
        .task-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            padding: 0 15px;
        }

        .task-card {
            background: white;
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .task-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .card-status-line { height: 5px; width: 100%; position: absolute; top: 0; }
        
        /* Status Colors */
        .st-pending { border-top: 5px solid #ffc107; }
        .st-progress { border-top: 5px solid #0dcaf0; }
        .st-completed { border-top: 5px solid #198754; opacity: 0.8; background: #f9fafb; }
        .st-rejected { border-top: 5px solid #dc3545; background: #fff5f5; }

        .task-body { padding: 20px; }
        .task-title { font-weight: 800; font-size: 1.1rem; margin-bottom: 8px; color: #2d3748; }
        .task-meta { font-size: 0.85rem; color: #718096; margin-bottom: 15px; display: flex; justify-content: space-between; }
        .task-badge { padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }
        
        /* Modal Styling */
        .modal-header { background: var(--primary-color); color: white; }
        .nav-tabs .nav-link { color: #555; font-weight: 600; }
        .nav-tabs .nav-link.active { color: var(--accent-color); border-bottom: 3px solid var(--accent-color); }
        .tab-content { padding: 20px 0; }

        .deadline-badge { background: #edf2f7; color: #4a5568; padding: 5px 10px; border-radius: 6px; font-size: 0.9rem; display: inline-block; margin-top: 10px; }
        .deadline-overdue { background: #fed7d7; color: #c53030; }
    </style>
</head>
<body>

<div class="page-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">🗂️ مهامي</h1>
                <p class="mb-0 opacity-75">إدارة المهام ومتابعة الإنجاز</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= site_url('users1/main_emp') ?>" class="top-btn"><i class="fas fa-home"></i> الرئيسية</a>
                <a href="<?= site_url('users/logout') ?>" class="top-btn"><i class="fas fa-sign-out-alt"></i> خروج</a>
            </div>
        </div>
    </div>
</div>

<div class="container" style="margin-top: 2rem;">
    <div class="task-grid">
        <?php if(empty($my_tasks)): ?>
            <div class="col-12 text-center py-5" style="grid-column: 1/-1;">
                <div class="bg-white p-5 rounded-4 shadow-sm">
                    <i class="fas fa-check-circle fa-4x text-muted mb-3"></i>
                    <h3>لا توجد مهام حالياً</h3>
                    <p class="text-muted">أنت جاهز! لا توجد مهام جديدة مسندة إليك.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach($my_tasks as $task): 
                $status = $task['status'];
                $class = 'st-' . $status;
                if($status == 'in_progress') $class = 'st-progress';
                
                $badges = [
                    'pending' => '<span class="badge bg-warning text-dark">قيد الانتظار</span>',
                    'in_progress' => '<span class="badge bg-info text-dark">جاري التنفيذ</span>',
                    'completed' => '<span class="badge bg-success">مكتملة</span>',
                    'rejected' => '<span class="badge bg-danger">مرفوضة</span>'
                ];
            ?>
            <div class="task-card <?= $class ?>" onclick='openTaskModal(<?= json_encode($task) ?>)'>
                <div class="card-status-line <?= $class ?>"></div>
                <div class="task-body">
                    <div class="task-meta">
                        <span><i class="fas fa-user-tie"></i> <?= $task['manager_name'] ?></span>
                        <?= $badges[$status] ?>
                    </div>
                    <div class="task-title"><?= $task['task_title'] ?></div>
                    <div class="text-muted small mb-3">
                        <?= mb_strimwidth($task['task_description'], 0, 60, '...') ?>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted"><i class="far fa-clock"></i> تسليم: <span dir="ltr"><?= $task['due_date'] ?></span></small>
                        <?php if($task['is_extension_requested']): ?>
                            <span class="badge bg-purple text-white" style="background-color: #6f42c1;"><i class="fas fa-history"></i> طلب تمديد</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="taskDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalTitle">تفاصيل المهمة</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalTaskId">
                
                <div class="mb-4">
                    <h4 class="fw-bold text-primary mb-2" id="modalTaskTitle"></h4>
                    <div class="text-muted mb-2">
                        <i class="fas fa-user-tie me-1"></i> المشرف: <span id="modalManager"></span> | 
                        <i class="far fa-calendar-alt me-1"></i> البدء: <span id="modalStart" dir="ltr"></span>
                    </div>
                    <div id="modalDueDateBadge" class="deadline-badge"></div>
                    <p class="mt-3 p-3 bg-light rounded border" id="modalDesc"></p>
                </div>

                <ul class="nav nav-tabs nav-justified" id="taskTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="status-tab" data-bs-toggle="tab" data-bs-target="#status-pane" type="button"><i class="fas fa-tasks"></i> حالة المهمة</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes-pane" type="button"><i class="fas fa-sticky-note"></i> ملاحظاتي</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="extension-tab" data-bs-toggle="tab" data-bs-target="#extension-pane" type="button"><i class="fas fa-clock"></i> تمديد الوقت</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-danger" id="reject-tab" data-bs-toggle="tab" data-bs-target="#reject-pane" type="button"><i class="fas fa-ban"></i> رفض المهمة</button>
                    </li>
                </ul>

                <div class="tab-content">
                    
                    <div class="tab-pane fade show active text-center" id="status-pane">
                        <p class="text-muted mb-3">قم بتحديث حالة العمل على هذه المهمة</p>
                        <div class="d-flex gap-3 justify-content-center">
                            <button onclick="updateStatus('in_progress')" class="btn btn-lg btn-outline-primary">
                                <i class="fas fa-play"></i> ابدأ العمل
                            </button>
                            <button onclick="updateStatus('completed')" class="btn btn-lg btn-success text-white">
                                <i class="fas fa-check-circle"></i> تم الإنجاز
                            </button>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="notes-pane">
                        <label class="form-label fw-bold">ملاحظاتك حول المهمة:</label>
                        <textarea id="modalNotes" class="form-control mb-3" rows="4" placeholder="اكتب أي ملاحظات أو تحديثات هنا..."></textarea>
                        <button onclick="saveNote()" class="btn btn-primary w-100">حفظ الملاحظات</button>
                    </div>

                    <div class="tab-pane fade" id="extension-pane">
                        <div class="alert alert-info">يمكنك طلب تمديد الموعد النهائي إذا لزم الأمر. سيتم إشعار المشرف بذلك.</div>
                        <label class="form-label">الموعد الجديد المقترح:</label>
                        <input type="date" id="modalExtDate" class="form-control mb-3">
                        <label class="form-label">سبب التمديد:</label>
                        <textarea id="modalExtReason" class="form-control mb-3" rows="2" placeholder="لماذا تحتاج إلى وقت إضافي؟"></textarea>
                        <button onclick="requestExtension()" class="btn btn-warning w-100 fw-bold text-dark">إرسال طلب التمديد</button>
                    </div>

                    <div class="tab-pane fade" id="reject-pane">
                        <div class="alert alert-danger">تحذير: رفض المهمة يعني أنك لن تقوم بتنفيذها. يرجى ذكر السبب بوضوح.</div>
                        <label class="form-label">سبب الرفض:</label>
                        <textarea id="modalRejectReason" class="form-control mb-3" rows="3" placeholder="لماذا ترفض هذه المهمة؟"></textarea>
                        <button onclick="rejectTask()" class="btn btn-danger w-100 fw-bold">تأكيد رفض المهمة</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const csrfName = '<?= $csrf_token_name ?>';
    let csrfHash = '<?= $csrf_hash ?>';

    // Open Modal and Populate Data
    function openTaskModal(task) {
        $('#modalTaskId').val(task.id);
        $('#modalTaskTitle').text(task.task_title);
        $('#modalDesc').text(task.task_description || 'لا يوجد وصف');
        $('#modalManager').text(task.manager_name);
        $('#modalStart').text(task.start_date);
        $('#modalNotes').val(task.employee_notes); // Pre-fill notes
        
        // Deadline Logic
        const today = new Date().toISOString().split('T')[0];
        let dueHtml = `<i class="fas fa-flag-checkered"></i> الموعد النهائي: <span dir="ltr">${task.due_date}</span>`;
        
        if(task.status !== 'completed' && task.due_date < today) {
            $('#modalDueDateBadge').html(dueHtml + ' (متأخرة)').addClass('deadline-overdue');
        } else {
            $('#modalDueDateBadge').html(dueHtml).removeClass('deadline-overdue');
        }

        // Disable actions if completed or rejected
        if(task.status === 'completed' || task.status === 'rejected') {
            $('button').prop('disabled', false); // Reset
            // Maybe disable status buttons visually
        }

        const modal = new bootstrap.Modal(document.getElementById('taskDetailModal'));
        modal.show();
    }

    // Generic AJAX Sender
    function sendAction(actionType, dataPayload) {
        dataPayload[csrfName] = csrfHash;
        dataPayload['task_id'] = $('#modalTaskId').val();
        dataPayload['action_type'] = actionType;

        $.ajax({
            url: '<?= site_url("users1/ajax_task_action") ?>',
            type: 'POST',
            data: dataPayload,
            dataType: 'json',
            success: function(res) {
                // Assuming CI doesn't regenerate hash on every ajax, but if it does, handle it here.
                // csrfHash = res.csrf_hash; 
                if(res.status === 'success') {
                    Swal.fire({icon: 'success', title: 'تم', text: res.message, timer: 1500, showConfirmButton: false}).then(() => location.reload());
                } else {
                    Swal.fire('خطأ', res.message, 'error');
                }
            }
        });
    }

    // 1. Update Status
    function updateStatus(status) {
        sendAction('update_status', { status: status });
    }

    // 2. Save Note
    function saveNote() {
        const note = $('#modalNotes').val();
        sendAction('add_note', { note: note });
    }

    // 3. Request Extension
    function requestExtension() {
        const date = $('#modalExtDate').val();
        const reason = $('#modalExtReason').val();
        if(!date || !reason) { Swal.fire('تنبيه', 'الرجاء تعبئة التاريخ والسبب', 'warning'); return; }
        sendAction('request_extension', { new_date: date, reason: reason });
    }

    // 4. Reject Task
    function rejectTask() {
        const reason = $('#modalRejectReason').val();
        if(!reason) { Swal.fire('تنبيه', 'سبب الرفض مطلوب', 'warning'); return; }
        
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من التراجع عن رفض المهمة.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'نعم، ارفضها'
        }).then((result) => {
            if (result.isConfirmed) {
                sendAction('reject_task', { reason: reason });
            }
        })
    }
</script>

</body>
</html>