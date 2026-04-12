<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management (orders_emp)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;}
        body{font-family:'Tajawal',sans-serif;overflow-x:hidden;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark); min-height: 100vh;}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        #loading-screen{position:fixed;inset:0;background:var(--marsom-blue);z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column;transition:opacity .5s}
        .loader{width:50px;height:50px;border:5px solid rgba(255,255,255,.3);border-top:5px solid var(--marsom-orange);border-radius:50%;animation:spin 1s linear infinite;margin-bottom:16px}
        @keyframes spin{to{transform:rotate(360deg)}}
        .main-container{padding:30px 15px;position:relative;z-index:1}
        .page-title{font-weight:700;font-size:2.5rem;color:var(--text-light);margin-bottom:32px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;}
        .page-title::after{content:'';position:absolute;width:100px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
        .table-card{background:rgba(255,255,255,.95); border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .dataTables-example thead th{background-color:#001f3f !important;color:#fff;text-align:center;vertical-align:middle;}
        .dataTables-example tbody td{text-align:center;vertical-align:middle;font-size:14px;}
        .dt-buttons .btn{background-color:var(--marsom-orange);border-color:var(--marsom-orange);color:#fff;margin:0 2px;}
        .dt-buttons .btn:hover{background:#e0882f;border-color:#e0882f;}
        .top-actions{position:absolute;top:15px;left:15px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.2);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;}
        .action-btn { cursor: pointer; margin: 0 5px; font-size: 1.1rem; }
        .nav-tabs .nav-link { color: var(--marsom-blue); font-weight: 600; }
        .nav-tabs .nav-link.active { color: var(--marsom-orange); border-bottom: 3px solid var(--marsom-orange); }
        .tab-content { padding: 20px; border: 1px solid #dee2e6; border-top: none; background: #fff; }
    </style>
</head>
<body>

<div id="loading-screen"><div class="loader"></div><h3 style="color:#fff">Loading...</h3></div>

<div class="top-actions">
    <a href="<?php echo site_url('users1/main_emp'); ?>"><i class="fas fa-home"></i> Dashboard</a>
    <a href="<?php echo site_url('emp_management/index'); ?>"><i class="fas fa-users"></i> EMP1</a>
</div>

<div class="main-container container-fluid">
    <div class="text-center"><h1 class="page-title">Employee Orders & Requests</h1></div>
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
                                    <th>Name</th>
                                    <th>Order Name / Type</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $idno = 0; ?>
                                <?php if (!empty($orders)): ?>
                                    <?php foreach($orders as $ord) : ?>
                                        <?php $idno++; ?>
                                        <tr>
                                            <td><?php echo $idno; ?></td>
                                            <td><?php echo html_escape($ord['emp_id']); ?></td>
                                            <td><?php echo html_escape($ord['emp_name']); ?></td>
                                            <td>
                                                <span class="badge bg-info text-dark"><?php echo html_escape($ord['order_name'] ?: $ord['type']); ?></span>
                                            </td>
                                            <td><?php echo html_escape($ord['date']); ?></td>
                                            <td>
                                                <?php if($ord['status'] == 'Approved'): ?>
                                                    <span class="badge bg-success">Approved</span>
                                                <?php elseif($ord['status'] == 'Pending'): ?>
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?php echo html_escape($ord['status']); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a class="action-btn text-primary edit-btn" data-id="<?php echo $ord['id']; ?>" title="Edit"><i class="fas fa-edit"></i></a>
                                                <a class="action-btn text-danger delete-btn" data-id="<?php echo $ord['id']; ?>" title="Delete"><i class="fas fa-trash-alt"></i></a>
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
</div>

<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="orderModalLabel">Order Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <form id="orderForm">
                    <input type="hidden" name="id" id="record_id">
                    
                    <ul class="nav nav-tabs px-3 pt-3" id="myTab" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-general" type="button">General Info</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-vacation" type="button">Vacation / Mission</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-financial" type="button">Overtime / Expenses</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-other" type="button">Other Details</button></li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        
                        <div class="tab-pane fade show active" id="tab-general">
                            <div class="row">
                                <div class="col-md-3 mb-3"><label>Emp ID</label><input type="text" class="form-control field-input" name="emp_id" required></div>
                                <div class="col-md-3 mb-3"><label>Emp Name</label><input type="text" class="form-control field-input" name="emp_name" required></div>
                                <div class="col-md-3 mb-3"><label>Order Name</label><input type="text" class="form-control field-input" name="order_name"></div>
                                <div class="col-md-3 mb-3"><label>Type</label><input type="text" class="form-control field-input" name="type"></div>
                                <div class="col-md-3 mb-3"><label>Status</label><input type="text" class="form-control field-input" name="status"></div>
                                <div class="col-md-3 mb-3"><label>Date</label><input type="date" class="form-control field-input" name="date"></div>
                                <div class="col-md-3 mb-3"><label>Time</label><input type="time" class="form-control field-input" name="time"></div>
                                <div class="col-md-3 mb-3"><label>Responsible Employee</label><input type="text" class="form-control field-input" name="responsible_employee"></div>
                                <div class="col-md-12 mb-3"><label>Note</label><textarea class="form-control field-input" name="note" rows="2"></textarea></div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-vacation">
                            <div class="row">
                                <h6 class="text-primary border-bottom pb-2">Vacation Details</h6>
                                <div class="col-md-3 mb-3"><label>Vac Type</label><input type="text" class="form-control field-input" name="vac_type"></div>
                                <div class="col-md-3 mb-3"><label>Vac Start</label><input type="date" class="form-control field-input" name="vac_start"></div>
                                <div class="col-md-3 mb-3"><label>Vac End</label><input type="date" class="form-control field-input" name="vac_end"></div>
                                <div class="col-md-3 mb-3"><label>Days Count</label><input type="number" class="form-control field-input" name="vac_days_count"></div>
                                
                                <h6 class="text-primary border-bottom pb-2 mt-3">Mission & Permission</h6>
                                <div class="col-md-3 mb-3"><label>Mission Type</label><input type="text" class="form-control field-input" name="mission_type"></div>
                                <div class="col-md-3 mb-3"><label>Mission Date</label><input type="date" class="form-control field-input" name="mission_date"></div>
                                <div class="col-md-3 mb-3"><label>Permission Date</label><input type="date" class="form-control field-input" name="permission_date"></div>
                                <div class="col-md-3 mb-3"><label>Permission Hours</label><input type="number" step="0.5" class="form-control field-input" name="permission_hours"></div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-financial">
                            <div class="row">
                                <h6 class="text-primary border-bottom pb-2">Overtime Details</h6>
                                <div class="col-md-3 mb-3"><label>OT Type</label><input type="text" class="form-control field-input" name="ot_type"></div>
                                <div class="col-md-3 mb-3"><label>OT Date</label><input type="date" class="form-control field-input" name="ot_date"></div>
                                <div class="col-md-3 mb-3"><label>OT Hours</label><input type="number" step="0.5" class="form-control field-input" name="ot_hours"></div>
                                <div class="col-md-3 mb-3"><label>OT Amount</label><input type="number" step="0.01" class="form-control field-input" name="ot_amount"></div>
                                
                                <h6 class="text-primary border-bottom pb-2 mt-3">Expenses Details</h6>
                                <div class="col-md-3 mb-3"><label>Item Name</label><input type="text" class="form-control field-input" name="exp_item_name"></div>
                                <div class="col-md-3 mb-3"><label>Exp Amount</label><input type="number" step="0.01" class="form-control field-input" name="exp_amount"></div>
                                <div class="col-md-3 mb-3"><label>Exp Date</label><input type="date" class="form-control field-input" name="exp_date"></div>
                                <div class="col-md-3 mb-3"><label>Payment Status</label><input type="text" class="form-control field-input" name="ot_payment_status"></div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-other">
                            <div class="row">
                                <div class="col-md-4 mb-3"><label>Asset Type</label><input type="text" class="form-control field-input" name="asset_type"></div>
                                <div class="col-md-4 mb-3"><label>Letter Type</label><input type="text" class="form-control field-input" name="letter_type"></div>
                                <div class="col-md-4 mb-3"><label>Reason for Resignation</label><input type="text" class="form-control field-input" name="reason_for_resignation"></div>
                                <div class="col-md-4 mb-3"><label>Date of Termination</label><input type="date" class="form-control field-input" name="date_of_termination"></div>
                                <div class="col-md-4 mb-3"><label>Reason for Rejection</label><input type="text" class="form-control field-input" name="reason_for_rejection"></div>
                                <div class="col-md-4 mb-3"><label>Original Order ID</label><input type="text" class="form-control field-input" name="original_order_id"></div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="orderForm" class="btn btn-primary">Save Order</button>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    window.addEventListener('load', function() { $('#loading-screen').fadeOut(500); });

    $(document).ready(function () {
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
        var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

        $('.dataTables-example').DataTable({
            pageLength: 25,
            order: [[0, 'desc']], // Sort by ID descending
            layout: {
                topStart: {
                    buttons: [
                        { extend:'excel', text:'<i class="fa fa-file-excel"></i> Export', className: 'btn btn-primary' },
                        {
                            text:'<i class="fa fa-plus"></i> New Request',
                            className:'btn btn-success',
                            action: function () {
                                $('#orderForm')[0].reset();
                                $('#record_id').val('');
                                $('#orderModal').modal('show');
                            }
                        },
                        {
                            text:'<i class="fa fa-trash"></i> Wipe Data',
                            className:'btn btn-danger',
                            action: function () {
                                Swal.fire({
                                    title: 'Are you sure?',
                                    text: "This deletes ALL orders!",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    confirmButtonText: 'Yes, wipe it!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        let data = {}; data[csrfName] = csrfHash;
                                        $.post("<?php echo site_url('emp_management/clear_all_orders'); ?>", data, function(res) {
                                            let response = JSON.parse(res);
                                            csrfHash = response.csrf_hash; 
                                            if(response.status === 'success') { location.reload(); }
                                        });
                                    }
                                });
                            }
                        }
                    ]
                }
            }
        });

        // Submit Form
        $('#orderForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serializeArray();
            formData.push({name: csrfName, value: csrfHash});

            $.post("<?php echo site_url('emp_management/save_order'); ?>", $.param(formData), function(res) {
                let response = JSON.parse(res);
                csrfHash = response.csrf_hash;
                if (response.status === 'success') { 
                    Swal.fire('Success!', response.message, 'success').then(() => location.reload());
                } else { 
                    Swal.fire('Error', response.message, 'error'); 
                }
            });
        });

        // Edit Data
        $('.dataTables-example tbody').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            let data = {id: id}; data[csrfName] = csrfHash;

            $.post("<?php echo site_url('emp_management/get_order'); ?>", data, function(res) {
                let response = JSON.parse(res);
                csrfHash = response.csrf_hash;
                if(response.status === 'success'){
                    var d = response.data;
                    $('#record_id').val(d.id);
                    
                    // Loop dynamically to fill all fields based on their name attribute
                    $.each(d, function(key, value) {
                        $('.field-input[name="'+key+'"]').val(value);
                    });
                    
                    $('#orderModal').modal('show');
                }
            });
        });

        // Delete Data
        $('.dataTables-example tbody').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Delete this order?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let data = {id: id}; data[csrfName] = csrfHash;
                    $.post("<?php echo site_url('emp_management/delete_order'); ?>", data, function(res) {
                        let response = JSON.parse(res);
                        csrfHash = response.csrf_hash;
                        if(response.status === 'success'){
                            Swal.fire('Deleted!', response.message, 'success').then(() => location.reload());
                        }
                    });
                }
            });
        });
    });
</script>
</body>
</html>