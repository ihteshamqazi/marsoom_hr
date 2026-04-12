<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Master Orders (Orders_Emp)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;}
        body{font-family:'Tajawal',sans-serif;overflow-x:hidden;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark); min-height: 100vh;}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        #loading-screen{position:fixed;inset:0;background:transparent;z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column;transition:opacity .5s}
        .loader{width:50px;height:50px;border:5px solid rgba(255,255,255,.3);border-top:5px solid var(--marsom-orange);border-radius:50%;animation:spin 1s linear infinite;margin-bottom:16px}
        @keyframes spin{to{transform:rotate(360deg)}}
        .main-container{padding:30px 15px;visibility:hidden;opacity:0;transition:opacity .5s;position:relative;z-index:1}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.8rem;color:var(--text-light);margin-bottom:32px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;}
        .page-title::after{content:'';position:absolute;width:100px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
        .table-card{background:rgba(255,255,255,.9);backdrop-filter:blur(8px);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .dataTables-example thead th{background-color:#001f3f !important;color:#fff;text-align:center;}
        .dataTables-example tbody td{text-align:center; vertical-align: middle;}
        .dt-buttons .btn{background-color:var(--marsom-orange);border-color:var(--marsom-orange);color:#fff;margin:0 2px;}
        .top-actions{position:fixed;top:12px;left:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;}
        .action-btn { cursor: pointer; margin: 0 5px; }
        
        /* Modals & Tabs */
        .nav-pills .nav-link { color: #001f3f; font-weight: bold; border: 1px solid #ddd; margin-bottom: 5px; border-radius: 5px; text-align: left;}
        .nav-pills .nav-link.active { background-color: #FF8C00; color: white; border-color: #FF8C00; }
        .tab-content { padding: 20px; background: white; border: 1px solid #ddd; border-radius: 5px; max-height: 65vh; overflow-y: auto;}
        .section-header { font-size: 1.1rem; color: #001f3f; border-bottom: 2px solid #FF8C00; margin-bottom: 15px; padding-bottom: 5px; margin-top: 10px;}
        .modal-body label { font-weight: 600; font-size: 0.85rem; margin-bottom: 2px;}
    </style>
</head>
<body>
<div id="loading-screen"><div class="loader"></div><h3 style="color:#fff">Loading ...</h3></div>
<div class="top-actions"><a href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-home"></i><span>Dashboard</span></a></div>

<div class="main-container container-fluid">
    <div class="text-center"><h1 class="page-title">Master Orders Database</h1></div>
    <div class="card table-card">
        <div class="card-body">
            <table class="table table-striped table-bordered dataTables-example w-100">
                <thead><tr><th>#</th><th>Emp ID</th><th>Emp Name</th><th>Type</th><th>Order Name</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="genericModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title">Order Details</h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body bg-light">
                <form id="genericForm">
                    <input type="hidden" name="id" id="record_id">
                    
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <div class="nav flex-column nav-pills" role="tablist">
                                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#t-gen" type="button">General</button>
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#t-res" type="button">Resign & Term</button>
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#t-cor" type="button">Corrections</button>
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#t-ot" type="button">Overtime</button>
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#t-exp" type="button">Exp & Assets</button>
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#t-vac" type="button">Vacations</button>
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#t-mis" type="button">Missions & Perm</button>
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#t-let" type="button">Letters</button>
                            </div>
                        </div>
                        
                        <div class="col-md-10">
                            <div class="tab-content shadow-sm">
                                
                                <div class="tab-pane fade show active" id="t-gen">
                                    <h5 class="section-header">Core Details</h5>
                                    <div class="row">
                                        <div class="col-md-4 mb-3"><label>Emp ID</label><input type="text" name="emp_id" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Emp Name</label><input type="text" name="emp_name" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Type</label><input type="text" name="type" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Order Name</label><input type="text" name="order_name" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Original Order ID</label><input type="text" name="original_order_id" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Created By ID</label><input type="text" name="created_by_id" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Status</label><input type="text" name="status" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Date</label><input type="date" name="date" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Time</label><input type="time" step="1" name="time" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>From Date</label><input type="date" name="from_date" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>To Date</label><input type="date" name="to_date" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Time Update</label><input type="time" step="1" name="time_update" class="form-control"></div>
                                        <div class="col-md-6 mb-3"><label>File Path</label><input type="text" name="file" class="form-control"></div>
                                        <div class="col-md-6 mb-3"><label>Note</label><input type="text" name="note" class="form-control"></div>
                                        <div class="col-md-12 mb-3"><label>Reason for Rejection</label><textarea name="reason_for_rejection" class="form-control"></textarea></div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="t-res">
                                    <h5 class="section-header">Resignation & Termination</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3"><label>Date of Last Working</label><input type="date" name="date_of_the_last_working" class="form-control"></div>
                                        <div class="col-md-6 mb-3"><label>Date of Termination</label><input type="date" name="date_of_termination" class="form-control"></div>
                                        <div class="col-md-12 mb-3"><label>Reason for Resignation</label><textarea name="reason_for_resignation" class="form-control" rows="2"></textarea></div>
                                        <div class="col-md-12 mb-3"><label>Resignation Details</label><textarea name="resignation_details" class="form-control" rows="3"></textarea></div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="t-cor">
    <h5 class="section-header">Attendance Corrections</h5>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label>Correction Date</label>
            <input type="date" name="correction_date" class="form-control">
        </div>
        <div class="col-md-4 mb-3">
            <label>Attendance Correction Status</label>
            <input type="text" name="attendance_correction" class="form-control">
        </div>
        <div class="col-md-4 mb-3">
            <label>Responsible Employee</label>
            <input type="text" name="responsible_employee" class="form-control" placeholder="Name or ID of responsible person">
        </div>
        
        <div class="col-md-6 mb-3">
            <label>Note on Entry (Check-in)</label>
            <input type="text" name="note_on_entry" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
            <label>Note on Checkout</label>
            <input type="text" name="note_on_checkout" class="form-control">
        </div>

        <div class="col-md-6 mb-3">
            <label>Correction of Entry</label>
            <input type="time" step="1" name="time_update" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
            <label>Correction of Departure</label>
            <input type="text" name="correction_of_departure" class="form-control">
        </div>

        <div class="col-md-12 mb-3">
            <label>Reason for Correction</label>
            <input type="text" name="reason_for_correction" class="form-control">
        </div>
        <div class="col-md-12 mb-3">
            <label>Details of the Reason</label>
            <textarea name="details_of_the_reason" class="form-control" rows="2"></textarea>
        </div>
    </div>
</div>

                                <div class="tab-pane fade" id="t-ot">
                                    <h5 class="section-header">Overtime Details</h5>
                                    <div class="row">
                                        <div class="col-md-4 mb-3"><label>OT Type</label><input type="text" name="ot_type" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>OT Date</label><input type="date" name="ot_date" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>OT Hours</label><input type="number" step="any" name="ot_hours" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>OT From</label><input type="time" step="1" name="ot_from" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>OT To</label><input type="time" step="1" name="ot_to" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>OT Reason</label><input type="text" name="ot_reason" class="form-control"></div>
                                        <div class="col-12"><h5 class="section-header">Overtime Payment</h5></div>
                                        <div class="col-md-3 mb-3"><label>OT Paid?</label><input type="text" name="ot_paid" class="form-control"></div>
                                        <div class="col-md-3 mb-3"><label>OT Amount</label><input type="number" step="any" name="ot_amount" class="form-control"></div>
                                        <div class="col-md-3 mb-3"><label>Payment Status</label><input type="text" name="ot_payment_status" class="form-control"></div>
                                        <div class="col-md-3 mb-3"><label>Payment Date</label><input type="date" name="payment_date" class="form-control"></div>
                                        <div class="col-md-12 mb-3"><label>Bank Receipt File</label><input type="text" name="bank_receipt_file" class="form-control"></div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="t-exp">
                                    <h5 class="section-header">Expenses</h5>
                                    <div class="row">
                                        <div class="col-md-4 mb-3"><label>Exp Item Name</label><input type="text" name="exp_item_name" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Exp Amount</label><input type="number" step="any" name="exp_amount" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Exp Date</label><input type="date" name="exp_date" class="form-control"></div>
                                        <div class="col-md-6 mb-3"><label>Exp Desc</label><input type="text" name="exp_desc" class="form-control"></div>
                                        <div class="col-md-6 mb-3"><label>Exp Reason</label><input type="text" name="exp_reason" class="form-control"></div>
                                    </div>
                                    <h5 class="section-header mt-3">Assets</h5>
                                    <div class="row">
                                        <div class="col-md-4 mb-3"><label>Asset Type</label><input type="text" name="asset_type" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Asset Desc</label><input type="text" name="asset_desc" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Asset Reason</label><input type="text" name="asset_reason" class="form-control"></div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="t-vac">
                                    <h5 class="section-header">Vacation / Leave</h5>
                                    <div class="row">
                                        <div class="col-md-4 mb-3"><label>Vac Type</label><input type="text" name="vac_type" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Vac Main Type</label><input type="text" name="vac_main_type" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Vac Half Date</label><input type="date" name="vac_half_date" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Vac Days Count</label><input type="number" step="any" name="vac_days_count" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Vac Half Period</label><input type="text" name="vac_half_period" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Delegation Emp ID</label><input type="text" name="delegation_employee_id" class="form-control"></div>
                                        <div class="col-md-6 mb-3"><label>Vac Start</label><input type="datetime-local" step="1" name="vac_start" class="form-control"></div>
                                        <div class="col-md-6 mb-3"><label>Vac End</label><input type="datetime-local" step="1" name="vac_end" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Visa Type</label><input type="text" name="vac_visa_type" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Visa Power</label><input type="text" name="vac_visa_power" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Review Before</label><input type="date" name="vac_review_before" class="form-control"></div>
                                        <div class="col-md-12 mb-3"><label>Vac Reason</label><textarea name="vac_reason" class="form-control"></textarea></div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="t-mis">
                                    <h5 class="section-header">Missions</h5>
                                    <div class="row">
                                        <div class="col-md-3 mb-3"><label>Mission Type</label><input type="text" name="mission_type" class="form-control"></div>
                                        <div class="col-md-3 mb-3"><label>Mission Date</label><input type="date" name="mission_date" class="form-control"></div>
                                        <div class="col-md-3 mb-3"><label>Start Time</label><input type="time" step="1" name="mission_start_time" class="form-control"></div>
                                        <div class="col-md-3 mb-3"><label>End Time</label><input type="time" step="1" name="mission_end_time" class="form-control"></div>
                                        <div class="col-md-12 mb-3"><label>Mission Note</label><input type="text" name="mission_note" class="form-control"></div>
                                    </div>
                                    <h5 class="section-header mt-3">Permissions</h5>
                                    <div class="row">
                                        <div class="col-md-3 mb-3"><label>Permission Date</label><input type="date" name="permission_date" class="form-control"></div>
                                        <div class="col-md-3 mb-3"><label>Start Time</label><input type="time" step="1" name="permission_start_time" class="form-control"></div>
                                        <div class="col-md-3 mb-3"><label>End Time</label><input type="time" step="1" name="permission_end_time" class="form-control"></div>
                                        <div class="col-md-3 mb-3"><label>Total Hours</label><input type="number" step="any" name="permission_hours" class="form-control"></div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="t-let">
                                    <h5 class="section-header">Letters & Correspondence</h5>
                                    <div class="row">
                                        <div class="col-md-4 mb-3"><label>Letter Type</label><input type="text" name="letter_type" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Letter To (EN)</label><input type="text" name="letter_to_en" class="form-control"></div>
                                        <div class="col-md-4 mb-3"><label>Letter To (AR)</label><input type="text" name="letter_to_ar" class="form-control"></div>
                                        <div class="col-md-12 mb-3"><label>Letter Reason</label><textarea name="letter_reason" class="form-control" rows="3"></textarea></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white"><button class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" form="genericForm" class="btn btn-primary fw-bold px-4">Save Order</button></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script><script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script><script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script><script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.js"></script><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.addEventListener('load', function(){ document.getElementById('loading-screen').style.display='none'; document.querySelector('.main-container').style.visibility='visible'; document.querySelector('.main-container').style.opacity='1'; });
    $(document).ready(function () {
        let csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>'; let csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        const TABLE_NAME = 'orders_emp';
        
        var dt = $('.dataTables-example').DataTable({
            pageLength: 25, processing: true, serverSide: true, order: [],
            ajax: { url: "<?php echo site_url('emp_management/dt_orders_emp'); ?>", type: "POST", data: function(d) { d[csrfName] = csrfHash; } },
            layout: { topStart: { buttons: [ 
                { extend:'excel', text:'<i class="fa fa-file-excel"></i> Excel' },
                { text:'<i class="fa fa-plus"></i> Add Master Order', className:'btn btn-success', action: function(){ $('#genericForm')[0].reset(); $('#record_id').val(''); $('#genericModal').modal('show'); } }, 
                { text:'<i class="fa fa-trash"></i> Wipe All', className:'btn btn-danger', action: function(){ Swal.fire({title:'Are you absolutely sure?', text:'This wipes the entire orders table!', icon:'warning', showCancelButton:true, confirmButtonColor:'#d33', confirmButtonText:'Yes, Wipe!'}).then((res) => { if(res.isConfirmed) { $.post("<?php echo site_url('emp_management/generic_action'); ?>", {action:'clear', table:TABLE_NAME, [csrfName]:csrfHash}, function(r){ let j=JSON.parse(r); csrfHash=j.csrf_hash; dt.ajax.reload(); }); } }); } } 
            ] } }
        });
        dt.on('xhr.dt', function (e, settings, json) { if (json && json.csrf_hash) csrfHash = json.csrf_hash; });

        $('#genericForm').on('submit', function(e) {
            e.preventDefault(); var fd = $(this).serializeArray(); fd.push({name: csrfName, value: csrfHash}, {name: 'action', value: 'save'}, {name: 'table', value: TABLE_NAME});
            $.post("<?php echo site_url('emp_management/generic_action'); ?>", $.param(fd), function(res) { let j = JSON.parse(res); csrfHash = j.csrf_hash; $('#genericModal').modal('hide'); dt.ajax.reload(); Swal.fire('Saved!','','success'); });
        });

        $('.dataTables-example').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $.post("<?php echo site_url('emp_management/generic_action'); ?>", {action:'get', table:TABLE_NAME, id: id, [csrfName]: csrfHash}, function(res) {
                let j = JSON.parse(res); csrfHash = j.csrf_hash;
                $.each(j.data, function(k, v) {
                    if(v !== null && (k === 'vac_start' || k === 'vac_end')) { $('[name="'+k+'"]').val(v.replace(' ', 'T')); } 
                    else { $('[name="'+k+'"]').val(v); }
                }); 
                $('#genericModal').modal('show');
            });
        });

        $('.dataTables-example').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            Swal.fire({title:'Delete Order?', icon:'warning', showCancelButton:true, confirmButtonColor:'#d33', confirmButtonText:'Yes'}).then((result) => {
                if (result.isConfirmed) {
                    $.post("<?php echo site_url('emp_management/generic_action'); ?>", {action:'delete', table:TABLE_NAME, id: id, [csrfName]: csrfHash}, function(res) {
                        let j = JSON.parse(res); csrfHash = j.csrf_hash; dt.ajax.reload();
                    });
                }
            });
        });
    });
</script></body></html>