<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة مستندات الموظفين</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        /* (ضع هنا كل أكواد CSS التي ذكرتها في طلبك للجمالية) */
        body { font-family: 'Tajawal', sans-serif; background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%); min-height: 100vh; padding: 30px; }
        .container-fluid { max-width: 1500px; background-color: #ffffff; border-radius: 25px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.18); padding: 40px; margin-bottom: 40px; }
        .page-header { font-weight: 800; color: #2c3e50; text-align: center; margin-bottom: 50px; font-size: 3.2rem; position: relative; }
        .page-header::after { content: ''; position: absolute; bottom: -15px; left: 50%; transform: translateX(-50%); width: 80px; height: 5px; background: linear-gradient(90deg, #6a93cb, #88b0eb); border-radius: 5px; }
        table.dataTable thead { background: linear-gradient(90deg, #537bbd, #6a93cb); color: white; }
        .expiring-soon { color: #dc3545 !important; font-weight: bold !important; background-color: #fff5f5 !important;} /* كلاس التلوين بالأحمر */
        
        /* تصميم الفلاتر المخصصة */
        .custom-filter-box { background: #f8f9fa; padding: 15px; border-radius: 15px; margin-bottom: 20px; border: 1px solid #e0e6ed; }
    </style>
</head>
<body>

    <div class="container-fluid">
        <h5 class="page-header" style="font-size:30px;">إدارة المستندات</h5>

        <div class="custom-filter-box row g-3 align-items-center">
            <div class="col-md-3">
                <input type="text" id="filter_emp" class="form-control" placeholder="بحث بالرقم أو اسم الموظف...">
            </div>
            <div class="col-md-3">
                <select id="filter_type" class="form-select">
                    <option value="">كل أنواع المستندات</option>
                    <option value="هوية وطنية / إقامة">هوية وطنية / إقامة</option>
                    <option value="جواز سفر">جواز سفر</option>
                    <option value="عقد عمل">عقد عمل</option>
                    <option value="شهادة طبية">شهادة طبية</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filter_status" class="form-select">
                    <option value="">حالة الصلاحية (الكل)</option>
                    <option value="expiring">ينتهي خلال 60 يوم (أو منتهي)</option>
                    <option value="valid">صالح</option>
                </select>
            </div>
        </div>

        <div class="data-table-container">
            <div class="d-flex flex-wrap justify-content-start align-items-center mb-4 gap-3">
                <div id="filter-container" class="order-0"></div>
                <div id="buttons-container" class="order-2"></div>
                <div id="length-container" class="order-3"></div>
                <div class="order-4">
                    <button class="btn btn-primary" style="background-color:#17a2b8; border:none; border-radius:10px; padding:10px 20px;" onclick="openDocumentForm()">
                        <i class="fas fa-plus-circle"></i> إضافة مستند
                    </button>
                </div>
            </div>

            <table id="documentTable" class="table table-striped table-hover dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>الرقم</th>
                        <th>الرقم الوظيفي</th>
                        <th>اسم الموظف</th>
                        <th>نوع المستند</th>
                        <th>تاريخ الانتهاء</th>
                        <th>الأيام المتبقية</th>
                        <th>الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $idno = 0; foreach($documents as $doc) : ?>
                        <?php 
                            $idno++; 
                            $is_expiring = false;
                            $days_left = "-";
                            
                            if (!empty($doc['expiry_date']) && $doc['expiry_date'] != '0000-00-00') {
                                $diff = strtotime($doc['expiry_date']) - time();
                                $days_left = floor($diff / (60 * 60 * 24));
                                
                                // إذا كان أقل من أو يساوي 60 يوم
                                if ($days_left <= 60) {
                                    $is_expiring = true;
                                }
                            }
                            // إضافة كلاس اللون الأحمر إذا شارف على الانتهاء
                            $row_class = $is_expiring ? 'expiring-soon' : '';
                        ?>
                        <tr class="<?php echo $row_class; ?>" style="font-family: 'Tajawal', sans-serif; font-size:14px;" data-expiring="<?php echo $is_expiring ? 'expiring' : 'valid'; ?>">
                            <td><?php echo $idno; ?></td> 
                            <td><?php echo $doc['employee_id']; ?></td> 
                            <td><?php echo $doc['emp_name']; ?></td> 
                            <td><?php echo $doc['document_type']; ?></td> 
                            <td><?php echo $doc['expiry_date']; ?></td>
                            <td><?php echo $days_left . ' يوم'; ?></td>
                            <td>
                                <a href="<?php echo site_url('users1/download_document/'.$doc['id']); ?>" class="btn btn-sm btn-success" style="border-radius: 8px;">
                                    <i class="fas fa-download"></i> تنزيل
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>  
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.5.0/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>

    <script>
        function openDocumentForm() {
            var width = 600, height = 650;
            var left = (window.innerWidth / 2) - (width / 2);
            var top = (window.innerHeight / 2) - (height / 2);
            window.open("<?php echo site_url('users1/add_document'); ?>", "Add Document", "width=" + width + ",height=" + height + ",left=" + left + ",top=" + top);
        }

        $(document).ready(function() {
            var table = $('#documentTable').DataTable({
                scrollY: '50vh',
                dom: 'rtip', // إخفاء العناصر الافتراضية للتحكم بأماكنها
                language: { url: '//cdn.datatables.net/plug-ins/1.11.3/i18n/ar.json' }
            });

            // ربط الفلاتر المخصصة الخاصة بنا مع DataTables
            $('#filter_emp').on('keyup', function() {
                table.columns([1, 2]).search(this.value).draw(); // البحث في عمودي الرقم والاسم
            });

            $('#filter_type').on('change', function() {
                table.columns(3).search(this.value).draw(); // البحث في عمود النوع
            });


            // فلتر حالة الانتهاء (مخصص)
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var filterStatus = $('#filter_status').val();
                    var rowNode = table.row(dataIndex).node();
                    var rowStatus = $(rowNode).attr('data-expiring');

                    if (filterStatus === "" || filterStatus === rowStatus) {
                        return true;
                    }
                    return false;
                }
            );
            $('#filter_status').on('change', function() {
                table.draw();
            });
        });
    </script>
</body>
</html>