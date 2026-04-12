<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Resignation Clearances</title>
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
        .section-header { font-size: 1.1rem; color: #001f3f; border-bottom: 2px solid #FF8C00; margin-bottom: 15px; padding-bottom: 5px; margin-top: 10px;}
    </style>
</head>
<body>
<div id="loading-screen"><div class="loader"></div><h3 style="color:#fff">Loading ...</h3></div>
<div class="top-actions"><a href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-home"></i><span>Dashboard</span></a></div>

<div class="main-container container-fluid">
    <div class="text-center"><h1 class="page-title">Resignation Clearances</h1></div>
    <div class="card table-card">
        <div class="card-body">
            <table class="table table-striped table-bordered dataTables-example w-100">
                <thead><tr><th>#</th><th>Req ID</th><th>Param ID</th><th>Task Description</th><th>Approver ID</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="genericModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title">Clearance Details</h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body bg-light">
                <form id="genericForm">
                    <input type="hidden" name="id" id="record_id">
                    
                    <div class="row">
                        <div class="col-12"><h5 class="section-header">Request & Task Info</h5></div>
                        <div class="col-md-4 mb-3"><label>Resignation Request ID</label><input type="text" name="resignation_request_id" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label>Clearance Parameter ID</label><input type="text" name="clearance_parameter_id" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label>Approver User ID</label><input type="text" name="approver_user_id" class="form-control"></div>
                        <div class="col-md-12 mb-3"><label>Task Description</label><textarea name="task_description" class="form-control" rows="2"></textarea></div>

                        <div class="col-12"><h5 class="section-header">Status & Attachments</h5></div>
                        <div class="col-md-4 mb-3"><label>Status</label><input type="text" name="status" class="form-control"></div>
                        <div class="col-md-8 mb-3"><label>Rejection Reason</label><input type="text" name="rejection_reason" class="form-control"></div>
                        <div class="col-md-12 mb-3"><label>Attachment Path</label><input type="text" name="attachment_path" class="form-control" placeholder="uploads/file.pdf"></div>

                        <div class="col-12"><h5 class="section-header">System Logs</h5></div>
                        <div class="col-md-3 mb-3"><label>Created By ID</label><input type="text" name="created_by_user_id" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label>Updated By ID</label><input type="text" name="updated_by_user_id" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label>Created At</label><input type="datetime-local" step="1" name="created_at" class="form-control"></div>
                        <div class="col-md-3 mb-3"><label>Updated At</label><input type="datetime-local" step="1" name="updated_at" class="form-control"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white"><button class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" form="genericForm" class="btn btn-primary fw-bold px-4">Save Clearance</button></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script><script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script><script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script><script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.js"></script><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.addEventListener('load', function(){ document.getElementById('loading-screen').style.display='none'; document.querySelector('.main-container').style.visibility='visible'; document.querySelector('.main-container').style.opacity='1'; });
    $(document).ready(function () {
        let csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>'; let csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        const TABLE_NAME = 'resignation_clearances';
        
        var dt = $('.dataTables-example').DataTable({
            pageLength: 20, processing: true, serverSide: true, order: [],
            ajax: { url: "<?php echo site_url('emp_management/dt_resignation_clearances'); ?>", type: "POST", data: function(d) { d[csrfName] = csrfHash; } },
            layout: { topStart: { buttons: [ {text:'<i class="fa fa-plus"></i> Add Clearance', className:'btn btn-success', action: function(){ $('#genericForm')[0].reset(); $('#record_id').val(''); $('#genericModal').modal('show'); } }, {text:'<i class="fa fa-trash"></i> Wipe All', className:'btn btn-danger', action: function(){ Swal.fire({title:'Are you sure?', icon:'warning', showCancelButton:true, confirmButtonColor:'#d33', confirmButtonText:'Yes, Wipe!'}).then((res) => { if(res.isConfirmed) { $.post("<?php echo site_url('emp_management/generic_action'); ?>", {action:'clear', table:TABLE_NAME, [csrfName]:csrfHash}, function(r){ let j=JSON.parse(r); csrfHash=j.csrf_hash; dt.ajax.reload(); }); } }); } } ] } }
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
                    if((k === 'created_at' || k === 'updated_at') && v !== null) { $('[name="'+k+'"]').val(v.replace(' ', 'T')); } 
                    else { $('[name="'+k+'"]').val(v); }
                }); 
                $('#genericModal').modal('show');
            });
        });

        $('.dataTables-example').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            Swal.fire({title:'Delete?', icon:'warning', showCancelButton:true, confirmButtonColor:'#d33', confirmButtonText:'Yes'}).then((result) => {
                if (result.isConfirmed) {
                    $.post("<?php echo site_url('emp_management/generic_action'); ?>", {action:'delete', table:TABLE_NAME, id: id, [csrfName]: csrfHash}, function(res) {
                        let j = JSON.parse(res); csrfHash = j.csrf_hash; dt.ajax.reload();
                    });
                }
            });
        });
    });
</script></body></html>