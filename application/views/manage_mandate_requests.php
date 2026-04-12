<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mandate Requests Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;--glass-bg:rgba(255,255,255,.08);--glass-border:rgba(255,255,255,.2);--glass-shadow:rgba(0,0,0,.5)}
        body{font-family:'Tajawal',sans-serif;overflow-x:hidden;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative; min-height: 100vh;}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .particles{position:fixed;inset:0;overflow:hidden;z-index:-1}
        .particle{position:absolute;background:rgba(255,140,0,.1);clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);animation:float 25s infinite ease-in-out;opacity:0;filter:blur(2px)}
        .particle:nth-child(even){background:rgba(0,31,63,.1)}
        .particle:nth-child(1){width:40px;height:40px;left:10%;top:20%;animation-duration:18s}
        .particle:nth-child(2){width:70px;height:70px;left:25%;top:50%;animation-duration:22s;animation-delay:2s}
        .particle:nth-child(3){width:55px;height:55px;left:40%;top:10%;animation-duration:25s;animation-delay:5s}
        .particle:nth-child(4){width:80px;height:80px;left:60%;top:70%;animation-duration:20s;animation-delay:8s}
        #loading-screen{position:fixed;inset:0;background:transparent;z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column;transition:opacity .5s}
        .loader{width:50px;height:50px;border:5px solid rgba(255,255,255,.3);border-top:5px solid var(--marsom-orange);border-radius:50%;animation:spin 1s linear infinite;margin-bottom:16px}
        @keyframes spin{to{transform:rotate(360deg)}}
        .main-container{padding:30px 15px;visibility:hidden;opacity:0;transition:opacity .5s;position:relative;z-index:1}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.8rem;color:var(--text-light);margin-bottom:32px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .page-title::after{content:'';position:absolute;width:100px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
        .table-card{background:rgba(255,255,255,.9);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.3);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .dataTables-example thead th{background-color:#001f3f !important;color:#fff;text-align:center;vertical-align:middle;border-bottom:2px solid #00152b}
        .dataTables-example tbody td{text-align:center;vertical-align:middle;font-size:14px;white-space:nowrap}
        .dt-buttons .btn{background-color:var(--marsom-orange);border-color:var(--marsom-orange);color:#fff;font-weight:500;margin:0 2px;box-shadow:0 2px 8px rgba(0,0,0,.2)}
        .dt-buttons .btn:hover{background:#e0882f;border-color:#e0882f;transform:translateY(-1px)}
        .top-actions{position:fixed;top:12px;left:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .top-actions a:hover{background:rgba(255,255,255,.2);color:var(--marsom-orange)}
        .action-btn { cursor: pointer; margin: 0 5px; }
        
        /* Modal Tabs Styling */
        .nav-pills .nav-link { color: #001f3f; font-weight: bold; border: 1px solid #ddd; margin-bottom: 5px; border-radius: 5px; }
        .nav-pills .nav-link.active { background-color: #FF8C00; color: white; border-color: #FF8C00; }
        .tab-content { padding: 20px; background: white; border: 1px solid #ddd; border-radius: 5px; }
        .modal-body label { font-weight: bold; font-size: 0.85rem; margin-top: 10px; color: #333; }
        .section-header { font-size: 1.1rem; color: #001f3f; border-bottom: 2px solid #FF8C00; margin-bottom: 15px; padding-bottom: 5px; }
    </style>
</head>
<body>

<div class="particles"><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div></div>
<div id="loading-screen"><div class="loader"></div><h3 style="color:#fff">Loading ...</h3></div>

<div class="top-actions">
    <a href="<?php echo site_url('users1/main_emp'); ?>"><i class="fas fa-home"></i><span>Dashboard</span></a>
</div>

<div class="main-container container-fluid">
    <div class="text-center"><h1 class="page-title">Mandate Requests</h1></div>
    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Emp ID</th>
                                    <th>Department</th>
                                    <th>Request Date</th>
                                    <th>Duration (Start - End)</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Pay Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mandateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="mandateModalLabel">Add / Edit Mandate Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light">
                <form id="mandateForm">
                    <input type="hidden" name="id" id="record_id">
                    
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <div class="nav flex-column nav-pills" role="tablist">
                                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-general" type="button"><i class="fas fa-info-circle"></i> General</button>
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-financial" type="button"><i class="fas fa-money-bill-wave"></i> Financial</button>
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-workflow" type="button"><i class="fas fa-tasks"></i> Workflow</button>
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-files" type="button"><i class="fas fa-paperclip"></i> Attachments</button>
                            </div>
                        </div>
                        
                        <div class="col-md-10">
                            <div class="tab-content">
                                
                                <div class="tab-pane fade show active" id="tab-general">
                                    <h5 class="section-header">General Information</h5>
                                    <div class="row">
                                        <div class="col-md-4"><label>Employee ID</label><input type="text" name="emp_id" class="form-control"></div>
                                        <div class="col-md-4"><label>Created By</label><input type="text" name="created_by" class="form-control"></div>
                                        <div class="col-md-4"><label>Department</label><input type="text" name="department" class="form-control"></div>
                                        
                                        <div class="col-md-4"><label>Request Date</label><input type="datetime-local" step="1" name="request_date" class="form-control"></div>
                                        <div class="col-md-4"><label>Start Date</label><input type="datetime-local" step="1" name="start_date" class="form-control"></div>
                                        <div class="col-md-4"><label>End Date</label><input type="datetime-local" step="1" name="end_date" class="form-control"></div>
                                        
                                        <div class="col-md-4"><label>Duration (Days)</label><input type="number" step="any" name="duration_days" class="form-control"></div>
                                        <div class="col-md-4"><label>Transport Mode</label><input type="text" name="transport_mode" class="form-control"></div>
                                        <div class="col-md-12"><label>Reason</label><textarea name="reason" class="form-control" rows="2"></textarea></div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-financial">
                                    <h5 class="section-header">Financial Details</h5>
                                    <div class="row">
                                        <div class="col-md-4"><label>Ticket Amount</label><input type="number" step="any" name="ticket_amount" class="form-control"></div>
                                        <div class="col-md-4"><label>Road Total KM</label><input type="number" step="any" name="road_total_km" class="form-control"></div>
                                        <div class="col-md-4"><label>Road Fuel Amount</label><input type="number" step="any" name="road_fuel_amount" class="form-control"></div>
                                        
                                        <div class="col-md-6"><label>Allowance Amount</label><input type="number" step="any" name="allowance_amount" class="form-control"></div>
                                        <div class="col-md-6"><label>Total Amount</label><input type="number" step="any" name="total_amount" class="form-control"></div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-workflow">
                                    <h5 class="section-header">Workflow & Payment Status</h5>
                                    <div class="row">
                                        <div class="col-md-4"><label>Status</label><input type="text" name="status" class="form-control"></div>
                                        <div class="col-md-4"><label>Current Approver</label><input type="text" name="current_approver" class="form-control"></div>
                                        <div class="col-md-4"><label>Rejected From ID</label><input type="text" name="rejected_from_id" class="form-control"></div>
                                        <div class="col-md-12"><label>Rejection Reason</label><input type="text" name="rejection_reason" class="form-control"></div>
                                        
                                        <div class="col-md-6"><label>Payment Status</label><input type="text" name="payment_status" class="form-control"></div>
                                        <div class="col-md-6"><label>Payment Date</label><input type="datetime-local" step="1" name="payment_date" class="form-control"></div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-files">
                                    <h5 class="section-header">Attachments & Receipts</h5>
                                    <div class="row">
                                        <div class="col-md-6"><label>Attachment 1</label><input type="text" name="attachment1" class="form-control"></div>
                                        <div class="col-md-6"><label>Attachment 2</label><input type="text" name="attachment2" class="form-control"></div>
                                        <div class="col-md-6"><label>Attachment 3</label><input type="text" name="attachment3" class="form-control"></div>
                                        <div class="col-md-6"><label>Attachment 4</label><input type="text" name="attachment4" class="form-control"></div>
                                        <div class="col-md-6"><label>Attachment 5</label><input type="text" name="attachment5" class="form-control"></div>
                                        <div class="col-md-6"><label>Payment Receipt</label><input type="text" name="payment_receipt" class="form-control"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="mandateForm" class="btn btn-success fw-bold px-4">Save Data</button>
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
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.colVis.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    window.addEventListener('load', function(){
        const loading = document.getElementById('loading-screen');
        const main = document.querySelector('.main-container');
        loading.style.opacity='0';
        setTimeout(function(){ loading.style.display='none'; document.body.style.overflow='auto'; main.style.visibility='visible'; main.style.opacity='1'; }, 400);
    });

    $(document).ready(function () {
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
        var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

        var dt = $('.dataTables-example').DataTable({
            responsive: true,
            pageLength: 20,
            lengthMenu: [[10, 20, 50, 100, 500], [10, 20, 50, 100, 500]],
            processing: true,
            serverSide: true,
            order: [], 
            ajax: {
                url: "<?php echo site_url('emp_management/fetch_mandate_ajax'); ?>",
                type: "POST",
                data: function ( d ) { d[csrfName] = csrfHash; }
            },
            layout: {
                topStart: {
                    buttons: [
                        { extend:'colvis', text:'<i class="fa fa-eye"></i> Show/Hide Columns', className: 'btn btn-secondary' },
                        { extend:'excel', text:'<i class="fa fa-file-excel"></i> Excel' },
                        {
                            text:'<i class="fa fa-plus-circle"></i> Add Request',
                            className:'btn btn-success',
                            action: function () {
                                $('#mandateForm')[0].reset();
                                $('#record_id').val('');
                                $('#mandateModalLabel').text('Add New Request');
                                $('#mandateModal').modal('show');
                            }
                        },
                        {
                            text:'<i class="fa fa-trash"></i> Wipe All',
                            className:'btn btn-danger',
                            action: function () {
                                Swal.fire({
                                    title: 'Are you sure?',
                                    text: "Permanently wipe all mandate requests!",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    confirmButtonText: 'Yes, wipe it!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $.post("<?php echo site_url('emp_management/clear_all_mandate'); ?>", {[csrfName]: csrfHash}, function(res) {
                                            let response = JSON.parse(res);
                                            csrfHash = response.csrf_hash;
                                            if(response.status === 'success'){
                                                dt.ajax.reload();
                                                Swal.fire('Deleted!', response.message, 'success');
                                            }
                                        });
                                    }
                                });
                            }
                        }
                    ]
                }
            }
        });

        dt.on('xhr.dt', function ( e, settings, json, xhr ) {
            if (json && json.csrf_hash) { csrfHash = json.csrf_hash; }
        });

        $('#mandateForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serializeArray();
            formData.push({name: csrfName, value: csrfHash});

            $.post("<?php echo site_url('emp_management/save_mandate'); ?>", $.param(formData), function(res) {
                let response = JSON.parse(res);
                csrfHash = response.csrf_hash;
                if (response.status === 'success') {
                    $('#mandateModal').modal('hide');
                    dt.ajax.reload();
                    Swal.fire({icon: 'success', title: 'Success', text: response.message});
                } else {
                    Swal.fire({icon: 'error', title: 'Error', html: response.message});
                }
            });
        });

        $('.dataTables-example tbody').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $.post("<?php echo site_url('emp_management/get_mandate'); ?>", {id: id, [csrfName]: csrfHash}, function(res) {
                let response = JSON.parse(res);
                csrfHash = response.csrf_hash;
                if(response.status === 'success') {
                    $.each(response.data, function(key, value) {
                        if((key === 'request_date' || key === 'start_date' || key === 'end_date' || key === 'payment_date') && value !== null) {
                            $('[name="'+key+'"]').val(value.replace(' ', 'T'));
                        } else {
                            $('[name="'+key+'"]').val(value);
                        }
                    });
                    $('#mandateModalLabel').text('Edit Request');
                    $('#mandateModal').modal('show');
                }
            });
        });

        $('.dataTables-example tbody').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("<?php echo site_url('emp_management/delete_mandate'); ?>", {id: id, [csrfName]: csrfHash}, function(res) {
                        let response = JSON.parse(res);
                        csrfHash = response.csrf_hash;
                        if(response.status === 'success'){
                            dt.ajax.reload();
                            Swal.fire('Deleted!', response.message, 'success');
                        }
                    });
                }
            });
        });
    });
</script>
</body>
</html>