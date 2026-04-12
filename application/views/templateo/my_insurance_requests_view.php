<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلباتي - التأمين الطبي</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f0f2f5;
            color: #333;
        }

        .main-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 15px;
        }

        .page-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            font-weight: 700;
            margin: 0;
            font-size: 1.8rem;
        }

        .card-custom {
            background: white;
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: transform 0.2s;
            margin-bottom: 20px;
        }

        .table-custom th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 700;
            border-bottom: 2px solid #e9ecef;
            padding: 15px;
        }

        .table-custom td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f1f1;
        }

        /* Status Badges */
        .status-badge {
            padding: 8px 12px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-processing { background-color: #cff4fc; color: #055160; }
        .status-approved { background-color: #d1e7dd; color: #0f5132; }
        .status-rejected { background-color: #f8d7da; color: #842029; }

        /* Request ID Circle */
        .req-id {
            background-color: #e9ecef;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: bold;
            color: #495057;
        }

        .btn-add-new {
            background-color: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.4);
            padding: 10px 20px;
            border-radius: 50px;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-add-new:hover {
            background-color: white;
            color: #1e3c72;
        }

        .family-pill {
            background-color: #e2e6ea;
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 0.8rem;
            margin: 2px;
            display: inline-block;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px;
        }
        .empty-state i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?php echo base_url('users1/main_emp'); ?>" class="btn btn-outline-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="fa fa-home ms-2"></i> الرئيسية
        </a>
        <button onclick="history.back()" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="fa fa-arrow-left ms-2"></i> رجوع
        </button>
    </div>
        <div class="page-header">
            <div>
                <h1><i class="fa-solid fa-file-medical ms-2"></i> سجل طلبات التأمين</h1>
                <p class="mb-0 opacity-75 mt-1">عرض ومتابعة حالة طلبات التأمين الطبي الخاصة بك</p>
            </div>
            <div>

                <a href="<?php echo base_url('users1/export_insurance_requests'); ?>" class="btn btn-success rounded-pill px-3 shadow-sm text-decoration-none d-flex align-items-center" style="height: 45px;">
        <i class="fa fa-file-excel me-2 ms-1"></i> تصدير Excel
    </a> </br>
                <a href="<?php echo base_url('users1/insurance_request'); ?>" class="btn-add-new">
                    <i class="fa fa-plus ms-1"></i> طلب جديد
                </a>
            </div>
        </div>

        <div class="card-custom p-0">
            <?php if(empty($requests)): ?>
                <div class="empty-state">
                    <i class="fa-regular fa-folder-open"></i>
                    <h3>لا توجد طلبات سابقة</h3>
                    <p class="text-muted">لم تقم بتقديم أي طلبات تأمين طبي حتى الآن.</p>
                    <a href="<?php echo base_url('users1/insurance_request'); ?>" class="btn btn-primary mt-3">تقديم طلب الآن</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
    <table class="table table-custom table-hover mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>الرقم الوظيفي</th> <th>نوع الطلب</th>
                <th>التفاصيل</th>
                <th>أفراد العائلة</th>
                <th>تاريخ الطلب</th>
                <th>المرفقات</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($requests as $row): ?>
                <tr>
                    <td>
                        <div class="req-id"><?php echo $row['id']; ?></div>
                    </td>
                    
                    <td>
                        <span class="fw-bold text-secondary"><?php echo $row['emp_id']; ?></span>
                    </td>

                    <td>
                        <span class="fw-bold text-dark"><?php echo $row['request_type']; ?></span>
                    </td>
                    <td>
                        <small class="text-muted d-block" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?php echo $row['reason'] ? $row['reason'] : 'لا يوجد ملاحظات'; ?>
                        </small>
                    </td>
                    <td>
                        <?php if(!empty($row['members'])): ?>
                            <div>
                                <?php foreach($row['members'] as $member): ?>
                                    <span class="family-pill">
                                        <i class="fa fa-user fa-xs text-secondary"></i> <?php echo $member['full_name']; ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <span class="text-muted fs-6">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex align-items-center text-muted">
                            <i class="fa-regular fa-calendar me-2 ms-1"></i>
                            <span dir="ltr"><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></span>
                        </div>
                    </td>
                    <td>
                        <?php if(!empty($row['attachments'])): ?>
                            <?php 
                                $files_array = explode(',', $row['attachments']);
                                foreach($files_array as $index => $file_name):
                                    $file_name = trim($file_name);
                                    if(empty($file_name)) continue;
                            ?>
                                <a href="<?php echo base_url('./uploads/insurance/' . $file_name); ?>" 
                                   class="btn btn-sm btn-outline-primary rounded-pill py-1 px-2 m-1" 
                                   download 
                                   target="_blank">
                                    <i class="fa fa-download"></i> ملف <?php echo $index + 1; ?>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php 
                            $st = $row['status'];
                            if($st == '0' || strtolower($st) == 'pending') {
                                echo '<span class="status-badge status-pending"><i class="fa fa-clock"></i> قيد الانتظار</span>';
                                if($row['current_approver']) {
                                    echo '<div class="mt-1" style="font-size:10px; color:#999">عند: '.$row['current_approver'].'</div>';
                                }
                            } elseif($st == '1' || strtolower($st) == 'processing') {
                                echo '<span class="status-badge status-processing"><i class="fa fa-spinner fa-spin"></i> جاري المعالجة</span>';
                            } elseif($st == '2' || strtolower($st) == 'approved') {
                                echo '<span class="status-badge status-approved"><i class="fa fa-check-circle"></i> معتمد</span>';
                            } elseif($st == '3' || strtolower($st) == 'rejected') {
                                echo '<span class="status-badge status-rejected"><i class="fa fa-times-circle"></i> مرفوض</span>';
                            } else {
                                echo '<span class="badge bg-secondary">'.$st.'</span>';
                            }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-4">
             <a href="<?php echo base_url('users1'); ?>" class="text-decoration-none text-muted">
                <i class="fa fa-arrow-right"></i> العودة للرئيسية
             </a>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>