<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير مشاكل البصمة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <style>
        :root { --primary-color: #001f3f; --accent-color: #FF8C00; }
        body { font-family: 'Tajawal', sans-serif; background-color: #f4f6f9; }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1e3c72 100%);
            color: white; padding: 40px 0; margin-bottom: 30px; border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .stat-card {
            background: white; border-radius: 12px; padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-right: 4px solid var(--accent-color);
        }
        
        /* Custom Tabs */
        .nav-pills .nav-link {
            color: #555; font-weight: bold; background: white; 
            border: 1px solid #ddd; margin-left: 10px; border-radius: 50px; padding: 10px 25px;
        }
        .nav-pills .nav-link.active {
            background-color: var(--primary-color); color: white; border-color: var(--primary-color);
        }

        /* Table Styling */
        .table-card { background: white; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); overflow: hidden; }
        tr.duplicate-row td { background-color: #ffebee !important; color: #c0392b; }
        tr.original-row td { background-color: #e8f5e9 !important; color: #27ae60; }
        
        .time-badge { font-family: 'Courier New', monospace; font-weight: bold; }
    </style>
</head>
<body>

<div class="page-header text-center">
    <h2 class="fw-bold"><i class="fas fa-fingerprint me-2"></i> تقرير مشاكل البصمة</h2>
    <p class="opacity-75">تحليل التكرار والبصمات الفردية (Single Punch)</p>
</div>

<div class="container-fluid px-4" style="margin-top: -60px;">
    
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">تكرار البصمة (Duplicate)</h6>
                        <h3 class="fw-bold mb-0 text-danger"><?php echo $dup_stats['total']; ?></h3>
                    </div>
                    <i class="fas fa-copy fa-2x text-danger opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card" style="border-right-color: #f1c40f;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">بصمة واحدة فقط (Single)</h6>
                        <h3 class="fw-bold mb-0 text-warning"><?php echo count($single_punches); ?></h3>
                    </div>
                    <i class="fas fa-exclamation-triangle fa-2x text-warning opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light rounded-3">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="fw-bold small mb-1">من تاريخ</label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
                </div>
                <div class="col-md-3">
                    <label class="fw-bold small mb-1">إلى تاريخ</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
                </div>
                <div class="col-md-3">
                    <label class="fw-bold small mb-1">هامش التكرار (للمكرر فقط)</label>
                    <select name="threshold" class="form-select">
                        <option value="2" <?php echo ($threshold == 2) ? 'selected' : ''; ?>>2 دقيقة (افتراضي)</option>
                        <option value="5" <?php echo ($threshold == 5) ? 'selected' : ''; ?>>5 دقائق</option>
                        <option value="60" <?php echo ($threshold == 60) ? 'selected' : ''; ?>>ساعة كاملة</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="fas fa-filter me-2"></i> عرض النتائج
                    </button>
                </div>
            </form>
        </div>
    </div>

    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-dup-tab" data-bs-toggle="pill" data-bs-target="#pills-dup" type="button">
                <i class="fas fa-copy me-2"></i> البصمات المكررة (Duplicates)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-single-tab" data-bs-toggle="pill" data-bs-target="#pills-single" type="button">
                <i class="fas fa-user-clock me-2"></i> بصمة واحدة (Missing Punch)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        
        <div class="tab-pane fade show active" id="pills-dup" role="tabpanel">
            <div class="table-card">
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-danger">سجل البصمات المكررة</h5>
                    <button class="btn btn-danger btn-sm fw-bold shadow-sm" onclick="deleteSelected()">
                        <i class="fas fa-trash-alt me-2"></i> حذف المحدد
                    </button>
                </div>
                <div class="table-responsive">
                    <table id="dupTable" class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center"><input type="checkbox" id="selectAll" class="form-check-input" onclick="toggleAll(this)"></th>
                                <th>الموظف</th>
                                <th>التاريخ</th>
                                <th>الوقت</th>
                                <th>الحالة</th>
                                <th>الفرق</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($logs)): ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted">لا يوجد بصمات مكررة</td></tr>
                            <?php else: ?>
                                <?php foreach($logs as $log): ?>
                                    <tr class="<?php echo $log['is_duplicate'] ? 'duplicate-row' : 'original-row'; ?>">
                                        <td class="text-center">
                                            <?php if($log['is_duplicate']): ?>
                                                <input type="checkbox" name="ids[]" value="<?php echo $log['id']; ?>" class="form-check-input dup-check">
                                            <?php else: ?>
                                                <i class="fas fa-check-circle text-success"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $log['first_name'] . ' ' . $log['last_name']; ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($log['punch_time'])); ?></td>
                                        <td class="time-badge"><?php echo date('H:i:s', strtotime($log['punch_time'])); ?></td>
                                        <td><?php echo $log['is_duplicate'] ? 'مكرر' : 'أصل'; ?></td>
                                        <td><?php echo isset($log['time_diff']) ? '+'.$log['time_diff'] : '-'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="pills-single" role="tabpanel">
            <div class="table-card border-top border-4 border-warning">
                <div class="p-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-warning">
                        <i class="fas fa-exclamation-circle me-2"></i> الموظفين ذوي البصمة الواحدة (نسيان دخول/خروج)
                    </h5>
                    <small class="text-muted">يعرض هذا الجدول الأيام التي سجل فيها الموظف بصمة واحدة فقط.</small>
                </div>
                <div class="table-responsive">
                    <table id="singleTable" class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>الموظف</th>
                                <th>التاريخ</th>
                                <th>وقت البصمة الموجودة</th>
                                <th>الجهاز</th>
                                <th>التحليل (توقع)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($single_punches)): ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted">ممتاز! لا يوجد بصمات فردية في هذه الفترة</td></tr>
                            <?php else: ?>
                                <?php foreach($single_punches as $s): 
                                    $time = strtotime($s['punch_time']);
                                    $hour = date('H', $time);
                                    // Guess logic: If before 12 PM, it's IN (Missing OUT). If after, it's OUT (Missing IN).
                                    $type = ($hour < 12) ? 'check_in' : 'check_out';
                                ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo $s['first_name'] . ' ' . $s['last_name']; ?></div>
                                            <small class="text-muted"><?php echo $s['emp_code']; ?></small>
                                        </td>
                                        <td class="fw-bold"><?php echo $s['log_date']; ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1 time-badge">
                                                <?php echo date('H:i:s', $time); ?>
                                                <?php echo ($hour < 12) ? 'صباحاً' : 'مساءً'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $s['terminal_alias']; ?></td>
                                        <td>
                                            <?php if($type == 'check_in'): ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-sign-out-alt"></i> نسيان خروج (Check Out)
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-info text-dark">
                                                    <i class="fas fa-sign-in-alt"></i> نسيان دخول (Check In)
                                                </span>
                                            <?php endif; ?>
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

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#dupTable').DataTable({ "language": { "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" } });
        $('#singleTable').DataTable({ "language": { "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" } });
    });

    function toggleAll(source) {
        $('.dup-check').prop('checked', source.checked);
    }

    function deleteSelected() {
        var selected = [];
        $('.dup-check:checked').each(function() { selected.push($(this).val()); });

        if (selected.length === 0) {
            Swal.fire('تنبيه', 'حدد سجلات للحذف', 'warning'); return;
        }

        Swal.fire({
            title: 'حذف التكرار؟',
            text: "سيتم حذف " + selected.length + " بصمة مكررة.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo base_url("users1/delete_duplicate_logs"); ?>',
                    type: 'POST',
                    data: { ids: selected, '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>' },
                    dataType: 'json',
                    success: function(res) {
                        if(res.status === 'success') Swal.fire('تم!', res.message, 'success').then(() => location.reload());
                        else Swal.fire('خطأ', res.message, 'error');
                    }
                });
            }
        });
    }
</script>

</body>
</html>