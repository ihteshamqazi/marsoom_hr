<?php
/** @var array  $rows */
/** @var array  $stats */
/** @var string $title */
/** @var array  $opts  */
$qs = http_build_query($opts ?: []);
$export_url = site_url('users1/gosi_emp1_compare_export' . ($qs ? '?'.$qs : ''));
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo html_escape($title); ?></title>

  <!-- Bootstrap 5 RTL -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
  <!-- Google Fonts (اختياري) -->
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body{font-family:"Cairo","Tajawal",Tahoma,Arial,sans-serif;background:#f7f9fc;}
    .page-header{display:flex;align-items:center;justify-content:space-between;gap:.75rem;margin:20px 0;}
    .badge-status{font-size:.9rem}
    .badge-match{background:#16a34a}
    .badge-mismatch{background:#dc2626}
    .diff-badge{font-size:.80rem}
    .table thead th{white-space:nowrap}
    .sticky-top{top:0;background:#fff}
    .card{box-shadow:0 8px 20px rgba(0,0,0,.05);border:0;border-radius:1rem;}
    .metrics .card{min-height:110px}
    .small-text{font-size:.85rem;color:#6b7280}
    .num{font-variant-numeric:tabular-nums;}
    .highlight-diff{background:#fff7ed;}
    .legend{font-size:.9rem;color:#64748b}
    .toolbar{display:flex;gap:.5rem;flex-wrap:wrap;align-items:end}
    .form-control, .form-select{border-radius:.6rem}
  </style>
</head>

<body class="container-fluid py-3">

  <div class="page-header">
    <h3 class="m-0"><?php echo html_escape($title); ?></h3>
    <div class="d-flex gap-2">
      <a class="btn btn-success" href="<?php echo $export_url; ?>">
        تصدير CSV
      </a>
      <button class="btn btn-outline-secondary" onclick="window.print()">طباعة</button>
    </div>
  </div>

  <!-- شريط أدوات الفلاتر (اختياري) -->
  <div class="card p-3 mb-3">
    <form method="get" class="toolbar">
      <div>
        <label class="form-label small">بحث (اسم/هوية)</label>
        <input type="text" name="q" class="form-control" placeholder="ابحث..."
               value="<?php echo html_escape($opts['q'] ?? ''); ?>">
      </div>
      <div>
        <label class="form-label small">تسامح (عملة)</label>
        <input type="number" name="tolerance" step="0.01" min="0" class="form-control"
               value="<?php echo isset($opts['tolerance']) ? (float)$opts['tolerance'] : 0; ?>">
      </div>
      <div>
        <label class="form-label small">الترتيب</label>
        <select name="sort" class="form-select">
          <?php
          $sort = $opts['sort'] ?? 'id_asc';
          $options = [
            'id_asc' => 'الهوية (تصاعدي)',
            'diff_total_desc' => 'فرق الإجمالي (تنازلي)',
            'diff_total_asc'  => 'فرق الإجمالي (تصاعدي)',
          ];
          foreach ($options as $val => $label) {
              $sel = ($sort === $val) ? 'selected' : '';
              echo "<option value=\"$val\" $sel>$label</option>";
          }
          ?>
        </select>
      </div>
      <div class="form-check mt-4">
        <input class="form-check-input" type="checkbox" id="only_mismatched" name="only_mismatched" value="1"
               <?php echo !empty($opts['only_mismatched']) ? 'checked' : ''; ?>>
        <label class="form-check-label" for="only_mismatched">غير المطابق فقط</label>
      </div>
      <div class="ms-auto">
        <label class="form-label small d-block">&nbsp;</label>
        <button class="btn btn-primary">تطبيق</button>
        <a href="<?php echo site_url('reports/gosi_emp1_compare'); ?>" class="btn btn-light">إعادة ضبط</a>
      </div>
    </form>
  </div>

  <div class="legend mb-2">
    <ul class="m-0">
      <li>اسم الموظف من emp1 (<code>subscriber_name</code>) وإذا مفقود يؤخذ من gosi (<code>n1</code>).</li>
      <li>Other Allowance = GOSI (<code>n5+n6</code>) مقابل EMP1 (<code>n4..n12</code>).</li>
      <li>إجمالي GOSI = Base + Housing + Other (محسوب). إجمالي EMP1 من عمود <code>total_salary</code>.</li>
    </ul>
  </div>

  <!-- بطاقات ملخص -->
  <div class="row g-3 metrics mb-3">
    <div class="col-12 col-md-4">
      <div class="card p-3">
        <div class="small-text">الإجمالي</div>
        <div class="fs-3 fw-bold num"><?php echo (int)$stats['total']; ?></div>
      </div>
    </div>
    <div class="col-6 col-md-4">
      <div class="card p-3">
        <div class="small-text">مطابق</div>
        <div class="fs-3 fw-bold text-success num"><?php echo (int)$stats['matched']; ?></div>
      </div>
    </div>
    <div class="col-6 col-md-4">
      <div class="card p-3">
        <div class="small-text">غير مطابق</div>
        <div class="fs-3 fw-bold text-danger num"><?php echo (int)$stats['mismatched']; ?></div>
      </div>
    </div>
  </div>

  <div class="card p-3">
    <div class="table-responsive">
      <table id="cmpTable" class="table table-striped table-hover align-middle">
        <thead class="table-light sticky-top">
          <tr>
            <th>رقم الهوية</th>
            <th>اسم الموظف</th>

            <th class="text-center">الراتب الأساسي<br><span class="small-text">GOSI / EMP1</span></th>
            <th class="text-center">بدل السكن<br><span class="small-text">GOSI / EMP1</span></th>
            <th class="text-center">Other Allowance<br>
              <span class="small-text">GOSI (n5+n6) / EMP1 (n4..n12)</span>
            </th>

            <th class="text-center">الإجمالي<br>
              <span class="small-text">GOSI (محسوب) / EMP1 (total_salary)</span>
            </th>

            <th>الفرق بالإجمالي</th>
            <th>وجود السجل</th>

            <th>حالة</th>
            <th>تفاصيل الفروقات</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r):
            $rowHasDiff   = ($r['status'] !== 'مطابق');
            $hasTotalDiff = ((float)$r['diff_total'] != 0.0);

            // وجود السجل
            if (!empty($r['e_id_number']) && !empty($r['emp_name_gosi'])) {
                $presence_label = 'موجود في الجدولين';
                $presence_class = 'bg-primary';
            } elseif (empty($r['emp_name_gosi'])) {
                $presence_label = 'غير موجود في GOSI';
                $presence_class = 'bg-warning text-dark';
            } elseif (empty($r['e_id_number'])) {
                $presence_label = 'غير موجود في ملف الموظفين';
                $presence_class = 'bg-warning text-dark';
            } else {
                $presence_label = '—';
                $presence_class = 'bg-secondary';
            }
        ?>
          <tr class="<?php echo $rowHasDiff ? 'highlight-diff' : ''; ?>">
            <td class="num"><?php echo html_escape($r['id_number']); ?></td>
            <td><?php echo html_escape($r['emp_name_final'] ?: '—'); ?></td>

            <!-- Base -->
            <td class="text-center">
              <div class="num"><?php echo number_format((float)$r['g_base'],2); ?> / <?php echo number_format((float)$r['e_base'],2); ?></div>
              <?php if ((float)$r['diff_base'] != 0.0): ?>
                <span class="badge bg-warning text-dark diff-badge">فرق: <?php echo number_format((float)$r['diff_base'],2); ?></span>
              <?php endif; ?>
            </td>

            <!-- Housing -->
            <td class="text-center">
              <div class="num"><?php echo number_format((float)$r['g_housing'],2); ?> / <?php echo number_format((float)$r['e_housing'],2); ?></div>
              <?php if ((float)$r['diff_housing'] != 0.0): ?>
                <span class="badge bg-warning text-dark diff-badge">فرق: <?php echo number_format((float)$r['diff_housing'],2); ?></span>
              <?php endif; ?>
            </td>

            <!-- Other -->
            <td class="text-center">
              <div class="num"><?php echo number_format((float)$r['g_other'],2); ?> / <?php echo number_format((float)$r['e_other'],2); ?></div>
              <?php if ((float)$r['diff_other'] != 0.0): ?>
                <span class="badge bg-warning text-dark diff-badge">فرق: <?php echo number_format((float)$r['diff_other'],2); ?></span>
              <?php endif; ?>
            </td>

            <!-- Total -->
            <td class="text-center">
              <div class="num fw-semibold"><?php echo number_format((float)$r['g_total'],2); ?> / <?php echo number_format((float)$r['e_total'],2); ?></div>
              <?php if ((float)$r['diff_total'] != 0.0): ?>
                <span class="badge bg-info text-dark diff-badge">فرق الإجمالي: <?php echo number_format((float)$r['diff_total'],2); ?></span>
              <?php endif; ?>
            </td>

            <!-- الفرق بالإجمالي -->
            <td>
              <?php if ($hasTotalDiff): ?>
                <span class="badge bg-danger">يوجد فرق</span>
              <?php else: ?>
                <span class="badge bg-success">لا يوجد</span>
              <?php endif; ?>
            </td>

            <!-- وجود السجل -->
            <td>
              <span class="badge <?php echo $presence_class; ?>"><?php echo $presence_label; ?></span>
            </td>

            <!-- حالة شاملة + التفاصيل -->
            <td>
              <?php if ($r['status'] === 'مطابق'): ?>
                <span class="badge badge-status badge-match">مطابق</span>
              <?php else: ?>
                <span class="badge badge-status badge-mismatch">غير مطابق</span>
              <?php endif; ?>
            </td>

            <td><?php echo $r['diff_details'] ? html_escape($r['diff_details']) : '—'; ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
  <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
  <script>
    $(function () {
      $('#cmpTable').DataTable({
        pageLength: 25,
        order: [[0, 'asc']],
        language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json' }
      });
    });
  </script>
</body>
</html>
