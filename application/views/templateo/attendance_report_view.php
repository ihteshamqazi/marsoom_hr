<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التقرير الشامل للحضور</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=El+Messiri:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">

    <style>
        :root {
            --primary-color: #4a69bd;
            --secondary-color: #F29840;
            --background-color: #f8f9fa;
            --card-bg-color: #ffffff;
            --text-color: #343a40;
            --danger-text: #842029;
            --danger-bg: #f8d7da;
            --warning-text: #664d03;
            --warning-bg: #fff3cd;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
        }

        /* Loader */
        #loading-screen {
            position: fixed; width: 100%; height: 100%; background: #fff; z-index: 9999;
            display: flex; align-items: center; justify-content: center; flex-direction: column;
            transition: opacity 0.5s ease-out;
        }
        .loader {
            width: 50px; height: 50px; border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary-color); border-radius: 50%;
            animation: spin 1s linear infinite; margin-bottom: 20px;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        .main-container { padding: 30px 15px; visibility: hidden; opacity: 0; transition: opacity 0.5s ease-in; }

        .page-title {
            font-family: 'El Messiri', sans-serif; font-weight: 800; font-size: 2.5rem;
            color: #0E1F3B; margin-bottom: 30px; position: relative; display: inline-block;
        }
        .page-title::after {
            content: ''; position: absolute; width: 80px; height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            bottom: -10px; left: 50%; transform: translateX(-50%); border-radius: 2px;
        }

        .glass-card {
            background: var(--card-bg-color); border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); padding: 25px; margin-bottom: 25px;
            border: 1px solid rgba(0,0,0,0.02);
        }

        /* Stat Cards */
        .stat-card {
            background: white; border-radius: 12px; padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-bottom: 4px solid var(--primary-color);
            transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-value { font-size: 1.8rem; font-weight: 800; color: var(--primary-color); }
        .stat-label { font-size: 0.9rem; font-weight: 600; color: #666; }

        /* Table */
        table.dataTable thead th {
            background-color: var(--primary-color) !important; color: white !important;
            font-weight: 500; text-align: center; vertical-align: middle; white-space: nowrap;
        }
        table.dataTable tbody td { text-align: center; vertical-align: middle; padding: 10px 8px; font-size: 0.95rem; }

        /* Badges */
        .badge-single { background-color: var(--warning-bg); color: var(--warning-text); border: 1px solid #ffeeba; }
        .badge-absent { background-color: var(--danger-bg); color: var(--danger-text); border: 1px solid #f5c2c7; }
        .badge-late { color: var(--danger-text); font-weight: bold; }
        
        .form-label { color: var(--primary-color); font-weight: 700; }
        .form-control, .form-select { border-radius: 8px; padding: 10px; }
        .btn-filter { background-color: var(--secondary-color); color: white; font-weight: bold; border-radius: 8px; padding: 10px; width: 100%; }
        .btn-filter:hover { background-color: #e0882f; color: white; }
    </style>
</head>
<body>

<div id="loading-screen">
    <div class="loader"></div>
    <h3>جاري تحميل التقرير...</h3>
</div>

<div class="main-container container-fluid">
    
    <div class="text-center mb-5">
        <h1 class="page-title">التقرير الشامل للحضور</h1>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card" style="border-bottom-color: #dc3545;">
                <div class="stat-value text-danger" id="total_late">0</div>
                <div class="stat-label">دقائق التأخير</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-bottom-color: #198754;">
                <div class="stat-value text-success" id="total_overtime">0</div>
                <div class="stat-label">ساعات الإضافي</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-bottom-color: #ffc107;">
                <div class="stat-value text-warning" id="total_single">0</div>
                <div class="stat-label">بصمة منفردة</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-bottom-color: #0d6efd;">
                <div class="stat-value text-primary" id="total_absent">0</div>
                <div class="stat-label">أيام الغياب</div>
            </div>
        </div>
    </div>

    <div class="glass-card">
        <form id="filterForm">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">القسم</label>
                    <select class="form-select" name="department">
                        <option value="">عرض الكل</option>
                        <?php if(!empty($departments)): foreach($departments as $d): ?>
                            <option value="<?= $d['n1'] ?>"><?= $d['n1'] ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">الشركة</label>
                    <select class="form-select" name="company">
                        <option value="">عرض الكل</option>
                        <?php if(!empty($companies)): foreach($companies as $c): ?>
                            <option value="<?= $c['company_name'] ?>"><?= $c['company_name'] ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">الموقع</label>
                    <select class="form-select" name="location">
                        <option value="">عرض الكل</option>
                        <?php if(!empty($locations)): foreach($locations as $loc): ?>
                            <option value="<?= $loc ?>"><?= $loc ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">الفترة</label>
                    <div class="input-group">
                        <input type="date" class="form-control" name="start_date" value="<?= date('Y-m-01') ?>">
                        <span class="input-group-text fw-bold">إلى</span>
                        <input type="date" class="form-control" name="end_date" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-filter" onclick="reloadTable()">عرض النتائج</button>
                </div>
            </div>
        </form>
    </div>

    <div class="glass-card">
        <div class="table-responsive">
            <div class="text-end mb-3">
                <a href="<?php echo site_url('users1/main_hr1'); ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-home"></i> الرئيسية</a>
            </div>
            <table id="attendanceTable" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>الرقم</th>
                        <th>الموظف</th>
                        <th>الموقع</th> <th>ساعات العمل</th>
                        <th>المطلوب</th>
                        <th>بصمة منفردة</th>
                        <th>تأخير (د)</th>
                        <th>تأخير بعذر</th>
                        <th>مبكر (د)</th>
                        <th>مبكر بإذن</th>
                        <th>إضافي</th>
                        <th>إضافي معتمد</th>
                        <th>غياب</th>
                        <th>إجازات</th>
                        <th>راحة</th>
                        <th>انتداب</th>
                        <th>أيام عمل</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="pendingRequestsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered"> 
        <div class="modal-content">
            <div class="modal-header bg-danger text-white" style="background-color: var(--primary-color)!important;">
                <h5 class="modal-title"><i class="fas fa-bell me-2"></i> طلبات معلقة</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="pending-loading" class="text-center py-4"><div class="spinner-border text-primary"></div></div>
                <div id="pending-content" style="display:none;">
                    <table class="table table-bordered"><tbody id="pending-table-body"></tbody></table>
                </div>
                <div id="pending-empty" class="text-center py-4" style="display:none;"><h5>لا توجد طلبات</h5></div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>

<script>
    window.addEventListener('load', function() {
        setTimeout(() => {
            document.getElementById('loading-screen').style.opacity = '0';
            setTimeout(() => {
                document.getElementById('loading-screen').style.display = 'none';
                document.querySelector('.main-container').style.visibility = 'visible';
                document.querySelector('.main-container').style.opacity = '1';
                reloadTable();
            }, 500);
        }, 800);
    });

    var table;
    $(document).ready(function() {
        table = $('#attendanceTable').DataTable({
            processing: true, serverSide: false,
            ajax: {
                url: "<?= base_url('users1/attendance_report_json') ?>",
                type: "POST",
                data: function (d) {
                    var form = $('#filterForm').serializeArray();
                    $.each(form, function(i, field){ d[field.name] = field.value; });
                },
                dataSrc: function(json) {
                    if(json.csrf_hash) $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val(json.csrf_hash);
                    calcStats(json.data);
                    return json.data;
                }
            },
            columns: [
                { data: "emp_id" },
                { data: "name", render: function(d){ return '<strong>'+d+'</strong>'; } },
                { data: "location" }, // Added Location Data
                { data: "total_working_hours" },
                { data: "total_office_hours" },
                { data: "single_fingerprint", render: function(d){ return d > 0 ? '<span class="badge badge-single">'+d+'</span>' : '-'; } },
                { data: "late_arrival", render: function(d){ return d > 0 ? '<span class="badge-late">'+d+'</span>' : '-'; }},
                { data: "late_arrival_excuse" },
                { data: "early_exit", render: function(d){ return d > 0 ? '<span class="badge-late">'+d+'</span>' : '-'; }},
                { data: "early_exit_permission" },
                { data: "overtime", render: function(d){ return d > 0 ? '<span class="text-success fw-bold">'+d+'</span>' : '-'; }},
                { data: "confirmed_overtime" },
                { data: "absence", render: function(d){ return d > 0 ? '<span class="badge badge-absent">'+d+'</span>' : '-'; }},
                { data: "holidays" },
                { data: "rest_days" },
                { data: "business_travel" },
                { data: "working_days", render: function(d){ return '<span class="text-primary fw-bold">'+d+'</span>'; } }
            ],
            layout: {
                topStart: {
                    buttons: [
                        { extend: 'excel', text: 'Excel', className: 'btn btn-success btn-sm' },
                        { extend: 'print', text: 'Print', className: 'btn btn-secondary btn-sm' },
                        { 
                            text: 'طلبات معلقة', className: 'btn btn-danger btn-sm',
                            action: function () {
                                var myModal = new bootstrap.Modal(document.getElementById('pendingRequestsModal'));
                                myModal.show();
                                fetchPending();
                            }
                        }
                    ]
                }
            },
            pageLength: 25,
            language: { url: "https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json" }
        });
    });

    function reloadTable() { table.ajax.reload(); }

    function calcStats(data) {
        let l=0, o=0, s=0, a=0;
        data.forEach(r => {
            l += parseFloat(r.late_arrival||0);
            o += parseFloat(r.overtime||0);
            s += parseInt(r.single_fingerprint||0);
            a += parseInt(r.absence||0);
        });
        $('#total_late').text(l); $('#total_overtime').text(o.toFixed(2));
        $('#total_single').text(s); $('#total_absent').text(a);
    }

    function fetchPending() {
        $('#pending-loading').show();
        var s = $('input[name="start_date"]').val();
        var e = $('input[name="end_date"]').val();
        $.ajax({
            url: '<?php echo site_url("users1/ajax_check_pending_requests"); ?>',
            type: 'POST',
            data: { 
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                'start_date': s, 'end_date': e 
            },
            success: function(res) {
                $('#pending-loading').hide();
                if(res.status === 'success' && res.count > 0) {
                    var h = '';
                    res.data.forEach(function(i){
                        h += '<tr><td>'+i.id+'</td><td>'+i.emp_name+'</td><td>'+i.order_name+'</td><td>'+i.submission_date+'</td><td>انتظار</td><td><a href="#" class="btn btn-sm btn-primary">عرض</a></td></tr>';
                    });
                    $('#pending-table-body').html(h);
                    $('#pending-content').show();
                } else { $('#pending-empty').show(); }
            }
        });
    }
</script>

</body>
</html>