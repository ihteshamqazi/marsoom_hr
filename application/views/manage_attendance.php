<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Logs Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">
    <style>
        /* EXACT ORIGINAL CSS */
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;--glass-bg:rgba(255,255,255,.08);--glass-border:rgba(255,255,255,.2);--glass-shadow:rgba(0,0,0,.5)}
        body{font-family:'Tajawal',sans-serif;overflow-x:hidden;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative; min-height: 100vh;}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .particles{position:fixed;inset:0;overflow:hidden;z-index:-1;pointer-events: none;}
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
        .dataTables-example tbody tr:hover{background-color:rgba(0,31,63,.05)}
        .dt-buttons .btn{background-color:var(--marsom-orange);border-color:var(--marsom-orange);color:#fff;font-weight:500;margin:0 2px;box-shadow:0 2px 8px rgba(0,0,0,.2)}
        .dt-buttons .btn:hover{background:#e0882f;border-color:#e0882f;transform:translateY(-1px)}
        .top-actions{position:fixed;top:12px;left:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .top-actions a:hover{background:rgba(255,255,255,.2);color:var(--marsom-orange)}
        .action-btn { cursor: pointer; margin: 0 5px; }
        .section-header { background: #001f3f; color: white; padding: 8px 15px; border-radius: 5px; margin-top: 15px; margin-bottom: 15px; font-weight: bold; }
    </style>
</head>
<body>

<div class="particles"><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div></div>

<div id="loading-screen"><div class="loader"></div><h3 style="color:#fff">Loading ...</h3></div>

<div class="top-actions">
    <a href="<?php echo site_url('users1/main_emp'); ?>"><i class="fas fa-home"></i><span>Dashboard</span></a>
</div>

<div class="main-container container-fluid">
    <div class="text-center"><h1 class="page-title">Attendance Logs</h1></div>
    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Emp Code</th>
                                    <th>Name</th>
                                    <th>Punch Time</th>
                                    <th>Punch State</th>
                                    <th>Area</th>
                                    <th>Terminal</th>
                                    <th>Upload Time</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="attendanceModalLabel">Add / Edit Attendance Log</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="attendanceForm">
                    <input type="hidden" name="id" id="record_id">
                    
                    <div class="row">
                        <div class="col-12"><div class="section-header">Employee Details</div></div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Employee Code</label>
                            <input type="text" name="emp_code" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control">
                        </div>

                        <div class="col-12"><div class="section-header">Punch Information</div></div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Punch Time</label>
                            <input type="datetime-local"  step="1"name="punch_time" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Punch State (e.g. Check In, Check Out)</label>
                            <input type="text" name="punch_state" class="form-control">
                        </div>
                        
                        <div class="col-12"><div class="section-header">Terminal Details</div></div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Area Alias</label>
                            <input type="text" name="area_alias" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Terminal SN</label>
                            <input type="text" name="terminal_sn" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Terminal Alias</label>
                            <input type="text" name="terminal_alias" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upload Time</label>
                            <input type="datetime-local" step="1" name="upload_time" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Created At</label>
                            <input type="datetime-local" step="1" name="created_at" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="attendanceForm" class="btn btn-primary">Save Data</button>
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
    window.addEventListener('load', function(){
        const loading = document.getElementById('loading-screen');
        const main = document.querySelector('.main-container');
        loading.style.opacity='0';
        setTimeout(function(){ loading.style.display='none'; document.body.style.overflow='auto'; main.style.visibility='visible'; main.style.opacity='1'; }, 400);
    });

    $(document).ready(function () {
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
        var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

        // FASTER LOADING SERVER-SIDE SETUP
        var dt = $('.dataTables-example').DataTable({
            responsive: true,
            pageLength: 20,
            lengthMenu: [[10, 20, 50, 100, 500], [10, 20, 50, 100, 500]],
            processing: true, // Shows the "Processing" indicator
            serverSide: true, // Enables fast server-side loading
            order: [], // Let server handle default order
            ajax: {
                url: "<?php echo site_url('emp_management/fetch_attendance_ajax'); ?>",
                type: "POST",
                data: function ( d ) {
                    d[csrfName] = csrfHash;
                }
            },
            layout: {
                topStart: {
                    buttons: [
                        { extend:'copy', text:'<i class="fa fa-copy"></i> Copy' },
                        { extend:'excel', text:'<i class="fa fa-file-excel"></i> Excel' },
                        {
                            text:'<i class="fa fa-plus-circle"></i> Add Log',
                            className:'btn btn-success',
                            action: function () {
                                $('#attendanceForm')[0].reset();
                                $('#record_id').val('');
                                $('#attendanceModalLabel').text('Add New Attendance Log');
                                $('#attendanceModal').modal('show');
                            }
                        },
                        {
                            text:'<i class="fa fa-trash"></i> Wipe All Data',
                            className:'btn btn-danger',
                            action: function () {
                                Swal.fire({
                                    title: 'Are you completely sure?',
                                    text: "This will permanently wipe all logs!",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    confirmButtonText: 'Yes, wipe it!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $.post("<?php echo site_url('emp_management/clear_all_attendance'); ?>", {[csrfName]: csrfHash}, function(res) {
                                            let response = JSON.parse(res);
                                            csrfHash = response.csrf_hash;
                                            if(response.status === 'success'){
                                                dt.ajax.reload(); // Reload the DataTable instantly
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

        // Update CSRF token on every AJAX call from DataTables
        dt.on('xhr.dt', function ( e, settings, json, xhr ) {
            if (json && json.csrf_hash) {
                csrfHash = json.csrf_hash;
            }
        });

        // Submit Form
        $('#attendanceForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serializeArray();
            formData.push({name: csrfName, value: csrfHash});

            $.post("<?php echo site_url('emp_management/save_attendance'); ?>", $.param(formData), function(res) {
                let response = JSON.parse(res);
                csrfHash = response.csrf_hash;
                if (response.status === 'success') {
                    $('#attendanceModal').modal('hide');
                    dt.ajax.reload(); // Reload table instantly
                    Swal.fire({icon: 'success', title: 'Success', text: response.message});
                } else {
                    Swal.fire({icon: 'error', title: 'Error', html: response.message});
                }
            });
        });

        // Edit Button Click (Event Delegation needed for Server-Side)
        $('.dataTables-example tbody').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $.post("<?php echo site_url('emp_management/get_attendance'); ?>", {id: id, [csrfName]: csrfHash}, function(res) {
                let response = JSON.parse(res);
                csrfHash = response.csrf_hash;
                if(response.status === 'success') {
                    $.each(response.data, function(key, value) {
                        // For datetime-local inputs, we need to replace space with 'T'
                        if((key === 'punch_time' || key === 'upload_time' || key === 'created_at') && value !== null) {
                            $('[name="'+key+'"]').val(value.replace(' ', 'T'));
                        } else {
                            $('[name="'+key+'"]').val(value);
                        }
                    });
                    $('#attendanceModalLabel').text('Edit Log');
                    $('#attendanceModal').modal('show');
                }
            });
        });

        // Delete Button Click
        $('.dataTables-example tbody').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("<?php echo site_url('emp_management/delete_attendance'); ?>", {id: id, [csrfName]: csrfHash}, function(res) {
                        let response = JSON.parse(res);
                        csrfHash = response.csrf_hash;
                        if(response.status === 'success'){
                            dt.ajax.reload(); // Reload table instantly
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