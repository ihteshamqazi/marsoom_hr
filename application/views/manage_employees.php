<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees Management (EMP1)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">
    <style>
        /* YOUR EXACT ORIGINAL CSS RESTORED */
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;--glass-bg:rgba(255,255,255,.08);--glass-border:rgba(255,255,255,.2);--glass-shadow:rgba(0,0,0,.5)}
        body{font-family:'Tajawal',sans-serif;overflow-x:hidden;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
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
        .dataTables-example tbody tr:hover{background-color:rgba(0,31,63,.05)}
        .dt-buttons .btn{background-color:var(--marsom-orange);border-color:var(--marsom-orange);color:#fff;font-weight:500;margin:0 2px;box-shadow:0 2px 8px rgba(0,0,0,.2)}
        .dt-buttons .btn:hover{background:#e0882f;border-color:#e0882f;transform:translateY(-1px)}
        .top-actions{position:fixed;top:12px;left:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .top-actions a:hover{background:rgba(255,255,255,.2);color:var(--marsom-orange)}
        .action-btn { cursor: pointer; margin: 0 5px; }
        
        /* Small addition to make the massive modal look clean in your theme */
        .section-header { background: #001f3f; color: white; padding: 5px 15px; border-radius: 5px; margin-top: 15px; margin-bottom: 15px; font-weight: bold; }
        .modal-body { max-height: 70vh; overflow-y: auto; }
    </style>
</head>
<body>

<div class="particles"><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div></div>

<div id="loading-screen"><div class="loader"></div><h3 style="color:#fff">Loading ...</h3></div>

<div class="top-actions">
    <a href="<?php echo site_url('users1/main_emp'); ?>"><i class="fas fa-home"></i><span>Dashboard</span></a>
</div>

<div class="main-container container-fluid">
    <div class="text-center"><h1 class="page-title">Employees Database (EMP1)</h1></div>
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
                                    <th>ID/Iqama</th>
                                    <th>Job Tag</th>
                                    <th>Profession</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $idno = 0; ?>
                                <?php if (!empty($employees)): ?>
                                    <?php foreach($employees as $emp): ?>
                                        <?php $idno++; ?>
                                        <tr>
                                            <td><?php echo $idno; ?></td>
                                            <td><?php echo html_escape($emp['employee_id']); ?></td>
                                            <td><?php echo html_escape($emp['subscriber_name']); ?></td>
                                            <td><?php echo html_escape($emp['id_number']); ?></td>
                                            <td><?php echo html_escape($emp['job_tag']); ?></td>
                                            <td><?php echo html_escape($emp['profession']); ?></td>
                                            <td><?php echo html_escape($emp['location']); ?></td>
                                            <td><?php echo html_escape($emp['status']); ?></td>
                                            <td>
                                                <a class="action-btn text-primary edit-btn" data-id="<?php echo $emp['id']; ?>" title="Edit"><i class="fas fa-edit"></i></a>
                                                <a class="action-btn text-danger delete-btn" data-id="<?php echo $emp['id']; ?>" title="Delete"><i class="fas fa-trash-alt"></i></a>
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

<div class="modal fade" id="empModal" tabindex="-1" aria-labelledby="empModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="empModalLabel">Add / Edit Employee</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="empForm">
                    <input type="hidden" name="id" id="record_id">
                    
                    <div class="row">
                        <div class="col-12"><div class="section-header">Personal Information</div></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Employee ID</label><input type="text" name="employee_id" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Subscriber Name</label><input type="text" name="subscriber_name" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Birth Date</label><input type="date" name="birth_date" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">ID Number</label><input type="text" name="id_number" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">ID Expiry</label><input type="date" name="id_expiry" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Nationality</label><input type="text" name="nationality" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">Select</option><option value="Male">Male</option><option value="Female">Female</option></select></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Personal Email</label><input type="email" name="personal_email" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Marital Status</label><input type="text" name="marital" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Religion</label><input type="text" name="religion" class="form-control"></div>
                        <div class="col-md-9 mb-3"><label class="form-label">Address</label><input type="text" name="address" class="form-control"></div>

                        <div class="col-12"><div class="section-header">Job & Status</div></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Status</label><input type="text" name="status" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Availability</label><input type="text" name="availability_status" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Company Name</label><input type="text" name="company_name" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Profession</label><input type="text" name="profession" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Job Tag</label><input type="text" name="job_tag" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Job Type</label><input type="text" name="job_type" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Location</label><input type="text" name="location" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Manager</label><input type="text" name="manager" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Joining Date</label><input type="date" name="joining_date" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Type</label><input type="text" name="type" class="form-control"></div>

                        <div class="col-12"><div class="section-header">Financial Information</div></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Base Salary</label><input type="number" step="0.01" name="base_salary" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Housing Allowance</label><input type="number" step="0.01" name="housing_allowance" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Commissions</label><input type="number" step="0.01" name="commissions" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Other Allowances</label><input type="number" step="0.01" name="other_allowances" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Total Salary</label><input type="number" step="0.01" name="total_salary" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Subject to Contribution</label><input type="number" step="0.01" name="salary_subject_to_contribution" class="form-control"></div>

                        <div class="col-12"><div class="section-header">Contract Details</div></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Contract Status</label><input type="text" name="contract_status" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Contract Period</label><input type="text" name="contract_period" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Iqama Expiry Date</label><input type="date" name="Iqama_expiry_date" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Contract Start</label><input type="date" name="contract_start" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Contract End</label><input type="date" name="contract_end" class="form-control"></div>

                        <div class="col-12"><div class="section-header">Custom Fields (N1 - N13)</div></div>
                        <?php for($i=1; $i<=13; $i++): ?>
                            <div class="col-md-2 mb-3"><label class="form-label">N<?= $i ?></label><input type="text" name="n<?= $i ?>" class="form-control"></div>
                        <?php endfor; ?>

                        <div class="col-12"><div class="section-header">Documents / Files</div></div>
                        <?php 
                        $files = ['national_address_file','commencement_form_file','job_description_file','confidentiality_form_file','gosi_subscription_file','experience_file','clearance_cert_file','medical_invoice','employment_guarantee_file','lawyer_license_file','criminal_record_file','medical_result','family_data_file','immediate_work_file'];
                        foreach($files as $f): ?>
                            <div class="col-md-4 mb-3"><label class="form-label"><?= ucwords(str_replace('_', ' ', $f)) ?></label><input type="text" name="<?= $f ?>" class="form-control" placeholder="File Path/URL"></div>
                        <?php endforeach; ?>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="empForm" class="btn btn-primary">Save Data</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="uploadModalLabel">Upload Excel / CSV</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo site_url('emp_management/upload_sheet'); ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="employee_file" class="form-label">Choose File</label>
                        <input class="form-control" type="file" id="employee_file" name="employee_file" required accept=".xlsx, .csv">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Upload</button>
                </div>
            </form>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // EXACT ORIGINAL LOADER JAVASCRIPT
    window.addEventListener('load', function(){
        const loading = document.getElementById('loading-screen');
        const main = document.querySelector('.main-container');
        loading.style.opacity='0';
        setTimeout(function(){ loading.style.display='none'; document.body.style.overflow='auto'; main.style.visibility='visible'; main.style.opacity='1'; }, 400);
    });

    $(document).ready(function () {
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
        var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

        // EXACT ORIGINAL DATATABLE SETUP
        var dt = $('.dataTables-example').DataTable({
            responsive: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            layout: {
                topStart: {
                    buttons: [
                        { extend:'copy', text:'<i class="fa fa-copy"></i> Copy' },
                        { extend:'excel', text:'<i class="fa fa-file-excel"></i> Excel' },
                        { extend:'pdf', text:'<i class="fa fa-file-pdf"></i> PDF' },
                        { extend:'print', text:'<i class="fa fa-print"></i> Print' },
                        {
                            text:'<i class="fa fa-plus-circle"></i> Add New',
                            className:'btn btn-success',
                            action: function () {
                                $('#empForm')[0].reset();
                                $('#record_id').val('');
                                $('#empModalLabel').text('Add New Employee');
                                $('#empModal').modal('show');
                            }
                        },
                        {
                            text:'<i class="fa fa-upload"></i> Upload File',
                            className:'btn btn-info',
                            action: function () {
                                $('#uploadModal').modal('show');
                            }
                        },
                        {
                            text:'<i class="fa fa-trash"></i> Delete All',
                            className:'btn btn-danger',
                            action: function () {
                                Swal.fire({
                                    title: 'Are you completely sure?',
                                    text: "This will delete all records!",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    confirmButtonText: 'Yes, delete!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $.post("<?php echo site_url('emp_management/clear_all'); ?>", {[csrfName]: csrfHash}, function(res) {
                                            let response = JSON.parse(res);
                                            csrfHash = response.csrf_hash;
                                            if(response.status === 'success'){
                                                Swal.fire('Deleted!', response.message, 'success').then(() => location.reload());
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

        // Submit Add/Edit Form
        $('#empForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serializeArray();
            formData.push({name: csrfName, value: csrfHash});

            $.post("<?php echo site_url('emp_management/save_record'); ?>", $.param(formData), function(res) {
                let response = JSON.parse(res);
                csrfHash = response.csrf_hash;
                if (response.status === 'success') {
                    $('#empModal').modal('hide');
                    Swal.fire({icon: 'success', title: 'Success', text: response.message}).then(() => location.reload());
                } else {
                    Swal.fire({icon: 'error', title: 'Error', html: response.message});
                }
            });
        });

        // Edit Button Click - Auto-fills ALL 62 fields dynamically
        $('.dataTables-example tbody').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $.post("<?php echo site_url('emp_management/get_record'); ?>", {id: id, [csrfName]: csrfHash}, function(res) {
                let response = JSON.parse(res);
                csrfHash = response.csrf_hash;
                if(response.status === 'success') {
                    $.each(response.data, function(key, value) {
                        $('[name="'+key+'"]').val(value);
                    });
                    $('#empModalLabel').text('Edit Employee');
                    $('#empModal').modal('show');
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
                    $.post("<?php echo site_url('emp_management/delete_record'); ?>", {id: id, [csrfName]: csrfHash}, function(res) {
                        let response = JSON.parse(res);
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