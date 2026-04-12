<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير التوزيع</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    <!-- Google Fonts Tajawal -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8f9fa;
        }
        .header-title {
            color: #0E1F3B;
            font-weight: bold;
            font-size: 32px;
            margin: 30px 0;
            text-align: center;
            animation: fadeInDown 1s ease-in-out;
        }
        table.dataTable thead {
            background-color: #F29840;
            color: #fff;
        }
        .dt-buttons .btn {
            background-color: #0E1F3B;
            color: #fff;
            border-radius: 20px;
            padding: 6px 15px;
            margin-left: 5px;
            transition: transform 0.3s ease;
        }
        .dt-buttons .btn:hover {
            transform: scale(1.05);
            background-color: #F29840;
        }
        @keyframes fadeInDown {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <h2 class="header-title">تقرير التوزيع للموظفين</h2>

    <div class="table-responsive">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <table id="reportTable" class="table table-bordered table-striped text-center align-middle" style="font-family:'Tajawal',sans-serif;font-size:14px;">
        <thead style="background-color:#F29840;color:white;">
            <tr>
                <th>الرقم الوظيفي</th>
                <th>اسم الموظف</th>
                <th>عدد العملاء</th>
                <th>إجمالي المديونية</th>
                <th>معلوم</th>
                <th>مجهول</th>
                <th>النسبة (معلوم/مجهول)</th>
                <th>أقل من 50 ألف</th>
                <th>50 - 100 ألف</th>
                <th>100 - 150 ألف</th>
                <th>أكثر من 150 ألف</th>
                <th>≤ سنة</th>
                <th>> سنة - سنتين</th>
                <th>> سنتين - 3 سنوات</th>
                <th>> 3 - 4 سنوات</th>
                <th>> 4 - 5 سنوات</th>
                <th>> 5 سنوات</th>
                <th>عمر العميل أقل من 40</th>
                <th>40 - 60</th>
                <th>أكثر من 60</th>
                <th>عمر غير معروف</th>
                <th>عدد العملاء المثبتين</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $report_data = $this->user_model->get_employee_distribution_report($this->uri->segment(3,0));
            $totals = [
                'clients'=>0,'debt'=>0,'known'=>0,'unknown'=>0,
                'b50'=>0,'b100'=>0,'b150'=>0,'a150'=>0,
                'age1'=>0,'age2'=>0,'age3'=>0,'age4'=>0,'age5'=>0,'age6'=>0,
                'c_below40'=>0,'c_40_60'=>0,'c_above60'=>0,'c_unknown'=>0,
                'stabilized'=>0
            ];

            if(!empty($report_data)):
                foreach($report_data as $row):
                    // تجميع الإجماليات
                    $totals['clients']   += $row['total_clients'];
                    $totals['debt']      += $row['total_debt'];
                    $totals['known']     += $row['known_clients'];
                    $totals['unknown']   += $row['unknown_clients'];
                    $totals['b50']       += $row['debt_below_50k'];
                    $totals['b100']      += $row['debt_50k_to_100k'];
                    $totals['b150']      += $row['debt_100k_to_150k'];
                    $totals['a150']      += $row['debt_above_150k'];
                    $totals['age1']      += $row['debt_age_1_year_or_less'];
                    $totals['age2']      += $row['debt_age_1_to_2_years'];
                    $totals['age3']      += $row['debt_age_2_to_3_years'];
                    $totals['age4']      += $row['debt_age_3_to_4_years'];
                    $totals['age5']      += $row['debt_age_4_to_5_years'];
                    $totals['age6']      += $row['debt_age_above_5_years'];
                    $totals['c_below40'] += $row['age_below_40'];
                    $totals['c_40_60']   += $row['age_40_to_60'];
                    $totals['c_above60'] += $row['age_above_60'];
                    $totals['c_unknown'] += $row['age_unknown'];
                    $totals['stabilized']+= $row['stabilized_clients'];

                    $known_percent = $row['total_clients']>0?round(($row['known_clients']/$row['total_clients'])*100,1):0;
                    $unknown_percent = $row['total_clients']>0?round(($row['unknown_clients']/$row['total_clients'])*100,1):0;
            ?>
            <tr>
                <td><?= $row['employee_number'] ?></td>
                <td><?= $row['employee_name'] ?></td>
                <td><?= $row['total_clients'] ?></td>
                <td><?= number_format($row['total_debt']) ?></td>
                <td><?= $row['known_clients'] ?></td>
                <td><?= $row['unknown_clients'] ?></td>
                <td><?= $known_percent ?>% / <?= $unknown_percent ?>%</td>
                <td><?= $row['debt_below_50k'] ?></td>
                <td><?= $row['debt_50k_to_100k'] ?></td>
                <td><?= $row['debt_100k_to_150k'] ?></td>
                <td><?= $row['debt_above_150k'] ?></td>
                <td><?= $row['debt_age_1_year_or_less'] ?></td>
                <td><?= $row['debt_age_1_to_2_years'] ?></td>
                <td><?= $row['debt_age_2_to_3_years'] ?></td>
                <td><?= $row['debt_age_3_to_4_years'] ?></td>
                <td><?= $row['debt_age_4_to_5_years'] ?></td>
                <td><?= $row['debt_age_above_5_years'] ?></td>
                <td><?= $row['age_below_40'] ?></td>
                <td><?= $row['age_40_to_60'] ?></td>
                <td><?= $row['age_above_60'] ?></td>
                <td><?= $row['age_unknown'] ?></td>
                <td><?= $row['stabilized_clients'] ?></td>
            </tr>
            <?php endforeach; ?>
            <!-- صف الإجماليات -->
            <tr style="font-weight:bold;background:#f9f9f9;">
                <td colspan="2">الإجمالي</td>
                <td><?= $totals['clients'] ?></td>
                <td><?= number_format($totals['debt']) ?></td>
                <td><?= $totals['known'] ?></td>
                <td><?= $totals['unknown'] ?></td>
                <td><?= round(($totals['known']/$totals['clients'])*100,1) ?>% / <?= round(($totals['unknown']/$totals['clients'])*100,1) ?>%</td>
                <td><?= $totals['b50'] ?></td>
                <td><?= $totals['b100'] ?></td>
                <td><?= $totals['b150'] ?></td>
                <td><?= $totals['a150'] ?></td>
                <td><?= $totals['age1'] ?></td>
                <td><?= $totals['age2'] ?></td>
                <td><?= $totals['age3'] ?></td>
                <td><?= $totals['age4'] ?></td>
                <td><?= $totals['age5'] ?></td>
                <td><?= $totals['age6'] ?></td>
                <td><?= $totals['c_below40'] ?></td>
                <td><?= $totals['c_40_60'] ?></td>
                <td><?= $totals['c_above60'] ?></td>
                <td><?= $totals['c_unknown'] ?></td>
                <td><?= $totals['stabilized'] ?></td>
            </tr>
            <?php else: ?>
            <tr><td colspan="22">لا توجد بيانات</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    </div>
</div>
<div id="loadingOverlay" style="
    display:none;
    position:fixed;
    top:0;left:0;width:100%;height:100%;
    background:rgba(255,255,255,0.95);
    z-index:9999;
    text-align:center;
    font-family:'Tajawal',sans-serif;">
  <div style="position: relative; top: 40%;">
    <div class="spinner-border text-primary" style="width:4rem;height:4rem;"></div>
    <h3 style="margin-top:20px;color:#0E1F3B;font-weight:bold;">جاري تحميل التقرير...</h3>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('loadingOverlay').style.display = 'block';
    setTimeout(()=>{ document.getElementById('loadingOverlay').style.display = 'none'; }, 500);
});
</script>







<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<button class="btn btn-success btn-sm" onclick="exportTableToExcel('reportTable')">تصدير Excel</button>
<button class="btn btn-danger btn-sm" onclick="exportTableToPDF()">تصدير PDF</button>

<script>
function exportTableToExcel(tableID){
    // احصل على عنصر الجدول
    var table = document.getElementById(tableID);
    if(!table){
        alert('لم يتم العثور على الجدول');
        return;
    }

    // حول الجدول إلى ملف Excel
    var wb = XLSX.utils.table_to_book(table, {sheet:"Sheet1"});
    XLSX.writeFile(wb, 'تقرير_التوزيع.xlsx');
}
</script>



</body>
</html>
