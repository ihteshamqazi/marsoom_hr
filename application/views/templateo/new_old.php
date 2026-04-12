<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title> تقرير تحليل المحفظة العام  </title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- خطوط + Bootstrap -->
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

  <style>
    body {
      font-family: 'Tajawal', sans-serif;
      background-color: #f8f9fa;
      color: #0E1F3B;
      padding: 40px 0;
    }
    .table-custom {
      background-color: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .table thead th {
      background-color: #0E1F3B;
      color: #ffffff;
      text-align: center;
    }
    .table tbody td {
      text-align: center;
      vertical-align: middle;
      font-size: 16px;
    }
    h2 {
      font-weight: bold;
      margin-bottom: 30px;
      color: #0E1F3B;
    }
  </style>
</head>
<body>

<div class="container">
  <h2 class="text-center"> تقرير تحليل المحفظة العام  </h2>
  <div class="table-responsive table-custom">
    <table class="table table-bordered">
      <thead>

        <tr class="table-active text-center">
  <td colspan="4"><strong>   البيانات المسحوبة (موجوده في المحفظة القديمة ولا توجد في الجديدة) </strong></td>
</tr>

     <tr>
  <td>1</td>
   <td>   عدد العقود المسحوبة  </td>
  <td>         </td>
 
   
  <td><span class="badge badge-warning"><?php echo $withdrawn_count; ?></span></td>
</tr>
<tr>
  <td>2</td>
  <td>عدد العملاء المسحوبين</td>
  <td>         </td>
  
  
  <td><span class="badge badge-info"><?php echo $unique_clients_count; ?></span></td>
</tr>
<tr>
  <td>3</td>
  <td>إجمالي المديونية المسحوبة</td>
  <td></td>
  <td><span class="badge badge-danger"><?php echo number_format($total_debt, 2); ?> ﷼</span></td>
</tr>
<tr>
  <td>4</td>
   <td>نسبة المديونية المسحوبة</td>
  <td>         </td>
 
    
  <td><span class="badge badge-success"><?php echo $debt_percentage; ?>%</span></td>
</tr>

<tr>
  <td>5</td>
  <td>نسبة العملاء المسحوبين</td>
  <td>         </td>
  <td><span class="badge badge-primary"><?php echo $client_percentage; ?>%</span></td>
</tr>

<tr>
  <td>6</td>
  <td>اجمالي مديونية المحفظة القديمة</td>
  <td>           </td>
  <td><span class="badge badge-dark"><?php echo number_format($total_debt_old, 2); ?> ﷼</span></td>
</tr>
<tr>
  <td>7</td>
  <td>  اجمالي مديونية المحفظة الجديدة    </td>
  <td>           </td>
  <td><span class="badge badge-secondary"><?php echo number_format($total_debt_new, 2); ?> ﷼</span></td>
</tr>

<tr>
  <td>8</td>
  <td>الفرق بين اجمالي مديونية المحفظة القديمة والمحفظة الجديدة  </td>
  <td>             </td>
  <td>
    <span class="badge badge-success">
      <?php echo number_format($debt_difference, 2); ?> ﷼
    </span>
  </td>
</tr>

<tr class="table-active text-center">
  <td colspan="4"><strong>   البيانات الجديدة   (موجوده في المحفظة الجديدة ولا توجد في القديمة)  </strong></td>
</tr>
<tr>
  <td>9</td>
  <td>عدد  العقود  الجديدة</td>
  <td>               </td>
  <td><span class="badge badge-warning"><?php echo $new_count; ?></span></td>
</tr>
<tr>
  <td>10</td>
  <td>عدد العملاء الجدد</td>
  <td>    </td>
  <td><span class="badge badge-info"><?php echo $new_unique_clients; ?></span></td>
</tr>
<tr>
  <td>11</td>
  <td>إجمالي المديونية الجديدة</td>
  <td>         </td>
  <td><span class="badge badge-danger"><?php echo number_format($new_debt, 2); ?> ﷼</span></td>
</tr>
<tr>
  <td>12</td>
  <td>نسبة المديونية الجديدة</td>
  <td>     </td>
  <td><span class="badge badge-success"><?php echo $new_debt_percentage; ?>%</span></td>
</tr>
<tr>
  <td>13</td>
  <td>نسبة العملاء الجدد</td>
  <td>         </td>
  <td><span class="badge badge-primary"><?php echo $new_client_percentage; ?>%</span></td>
</tr>






        <!-- 🟢 يمكنك إضافة المزيد من التقارير هنا -->
      </tbody>

     

 

    </table>

    <div class="container mt-5">
  <h4 class="text-center mb-3">تقرير مقارنة عدد المنتجات في المحفظتين</h4>
  <div class="table-responsive table-custom">
    <table class="table table-bordered text-center">
      <thead class="thead-dark">
        <tr>
          <th>#</th>
          <th>اسم المنتج</th>
          <th>   عدد العقود في المحفظة القديمة  </th>
          <th>  عدد العقود في المحفظة الجديدة   </th>
          <th>الفرق</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; foreach ($product_comparison as $row): ?>
          <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo $row['product_name']; ?></td>
            <td><span class="badge badge-secondary"><?php echo $row['count_old']; ?></span></td>
            <td><span class="badge badge-info"><?php echo $row['count_new']; ?></span></td>
            <td>
              <span class="badge <?php echo ($row['difference'] >= 0) ? 'badge-success' : 'badge-danger'; ?>">
                <?php echo $row['difference']; ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="container mt-5">
  <h4 class="text-center mb-3">تقرير محفظة   العملاء الى سنة </h4>
  <div class="table-responsive table-custom">
    <table class="table table-bordered text-center">
      <thead class="thead-dark">
        <tr>
          <th>#</th>
          <th>العنصر</th>
          <th>الوصف</th>
          <th>القيمة</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>عدد  العقود</td>
          <td>      </td>
          <td><span class="badge badge-info"><?php echo $marsoom_summary['record_count']; ?></span></td>
        </tr>
        <tr>
          <td>2</td>
          <td>عدد العملاء</td>
          <td>   </td>
          <td><span class="badge badge-secondary"><?php echo $marsoom_summary['unique_clients']; ?></span></td>
        </tr>
        <tr>
          <td>3</td>
          <td>إجمالي المديونية</td>
          <td>       </td>
          <td><span class="badge badge-danger"><?php echo number_format($marsoom_summary['total_debt'], 2); ?> ﷼</span></td>
        </tr>
        <tr>
          <td>4</td>
          <td>     نسبة المديونية من الاجمالي    </td>
          <td>           </td>
          <td><span class="badge badge-success"><?php echo $marsoom_summary['debt_percentage']; ?>%</span></td>
        </tr>
        <tr>
          <td>5</td>
          <td>    نسبة العملاء من الاجمالي     </td>
          <td>         </td>
          <td><span class="badge badge-primary"><?php echo $marsoom_summary['client_percentage']; ?>%</span></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<div class="container mt-5">
  <h4 class="text-center mb-3">تقرير محفظة  اكبر من سنة</h4>
  <div class="table-responsive table-custom">
    <table class="table table-bordered text-center">
      <thead class="thead-dark">
        <tr>
          <th>#</th>
          <th>العنصر</th>
          <th> </th>
          <th>القيمة</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>عدد   العقود</td>
          <td>       </td>
          <td><span class="badge badge-info"><?php echo $marsoom_summary_main['record_count']; ?></span></td>
        </tr>
        <tr>
          <td>2</td>
          <td>عدد العملاء</td>
          <td>   </td>
          <td><span class="badge badge-secondary"><?php echo $marsoom_summary_main['unique_clients']; ?></span></td>
        </tr>
        <tr>
          <td>3</td>
          <td>إجمالي المديونية</td>
          <td>       </td>
          <td><span class="badge badge-danger"><?php echo number_format($marsoom_summary_main['total_debt'], 2); ?> ﷼</span></td>
        </tr>
        <tr>
          <td>4</td>
          <td>    نسبة المديونية من الاجمالي     </td>
          <td>           </td>
          <td><span class="badge badge-success"><?php echo $marsoom_summary_main['debt_percentage']; ?>%</span></td>
        </tr>
        <tr>
          <td>5</td>
          <td>    نسبة العملاء من الاجمالي     </td>
          <td>         </td>
          <td><span class="badge badge-primary"><?php echo $marsoom_summary_main['client_percentage']; ?>%</span></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>



<div class="container mt-4">
  <h5 class="text-center mb-3">     مقارنة اكبر من سنة في المحفظة الجديدة والقديمة      </h5>
  <div class="table-responsive table-custom">
    <table class="table table-bordered text-center">
      <thead class="thead-light">
        <tr>
          <th>العنصر</th>
          <th>    المحفظة الجديدة  </th>
          <th> المحفظة القديمة  </th>
          <th>الفرق</th>
          
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>عدد  العقود</td>
          <td><?php echo $marsoom_summary_main['record_count']; ?></td>
          <td><?php echo $marsoom_summary_main['total_records_old']; ?></td>
          <td><?php echo $withdrawn_vs_marsoom['withdrawn_count']; ?></td>
        
        </tr>
        <tr>
          <td>عدد العملاء  </td>
          <td><?php echo $marsoom_summary_main['unique_clients']; ?></td>
          <td><?php echo $marsoom_summary_main['total_clients_old']; ?></td>
          <td><?php echo $withdrawn_vs_marsoom['unique_clients']; ?></td>
         
        </tr>
        <tr>
          <td>إجمالي المديونية</td>
          <td><?php echo number_format($marsoom_summary_main['total_debt'], 2); ?> ﷼</td>
          <td><?php echo number_format($marsoom_summary_main['total_debt_old'], 2); ?> ﷼</td>
          <td><?php echo number_format($withdrawn_vs_marsoom['total_debt'], 2); ?> ﷼</td>
          
        </tr>
      </tbody>
    </table>
  </div>
</div>

 


<div class="container mt-5">
  <h4 class="text-center mb-3">       تفاصيل الدعم الجديد في اكبر من سنة </h4>
  <div class="table-responsive table-custom">
    <table class="table table-bordered text-center">
      <thead class="thead-dark">
        <tr>
          <th>#</th>
          <th>العنصر</th>
          <th> </th>
          <th>القيمة</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>عدد  العقود الجديدة</td>
          <td>               </td>
          <td><span class="badge badge-success"><?php echo $new_entries_marsoom_fast['new_count']; ?></span></td>
        </tr>
        <tr>
          <td>2</td>
          <td>عدد العملاء الجدد</td>
          <td>     </td>
          <td><span class="badge badge-info"><?php echo $new_entries_marsoom_fast['unique_clients']; ?></span></td>
        </tr>
        <tr>
          <td>3</td>
          <td>إجمالي المديونية</td>
          <td>       </td>
          <td><span class="badge badge-danger"><?php echo number_format($new_entries_marsoom_fast['total_debt'], 2); ?> ﷼</span></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>










    


  </div>
</div>

</body>
</html>
