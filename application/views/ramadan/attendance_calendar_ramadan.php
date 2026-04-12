<?php
// This block receives variables from your controller
$daysInMonth  = (int)date('t', strtotime(sprintf('%04d-%02d-01', $year, $month)));
$firstWeekday = (int)date('w', strtotime(sprintf('%04d-%02d-01', $year, $month))); // 0=Sun..6=Sat

$monthNameAr = [1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'];
$weekDaysAr  = ['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];

// Weekend mapping
$WEEKEND_MAP = [];
for ($d=1; $d <= $daysInMonth; $d++){
    $date = sprintf('%04d-%02d-%02d',$year,$month,$d);
    $dow  = (int)date('w', strtotime($date));
    $WEEKEND_MAP[$date] = ($dow === 5 || $dow === 6); // Friday or Saturday
}
?>
<?php
// Test for January 1st in current view
$testDate = sprintf('%04d-01-01', $year);
echo "<!-- Testing date: {$testDate} -->\n";
echo "<!-- Is this date in current month? " . (date('m', strtotime($testDate)) == $month ? 'YES' : 'NO') . " -->\n";
if (date('m-d', strtotime($testDate)) == '01-01' && date('m', strtotime($testDate)) == $month) {
    echo "<!-- January 1st IS in this month view -->\n";
    echo "<!-- New Year holiday status for employee: " . var_export($new_year_holiday_status, true) . " -->\n";
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>الحضور</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet" crossorigin="anonymous">
<link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>

<style>
:root{
    --marsom-blue:#001f3f; --marsom-orange:#FF8C00; --text-light:#fff;
    --glass-bg:rgba(255,255,255,.08); --glass-border:rgba(255,255,255,.2); --glass-shadow:rgba(0,0,0,.5);
    --vac:#1abc9c;    --half:#8e44ad;  --corr:#f39c12; --abs:#dc3545; --week:#6c757d;
    --holiday: #3498db;
}
body{
    font-family:'Tajawal',sans-serif;
    background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);
    background-size:400% 400%; animation:gradientAnimation 20s ease infinite;
    display:flex; justify-content:center; align-items:center; min-height:100vh; margin:0; padding:15px; position:relative; color:var(--text-light);
}
@keyframes gradientAnimation{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
.particles{position:absolute; inset:0; overflow:hidden; z-index:0}
.particle{position:absolute; background:rgba(255,140,0,.1); clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%); animation:float 25s infinite ease-in-out; opacity:0; filter:blur(2px)}
.particle:nth-child(even){background:rgba(0,31,63,.1)}
@keyframes float{0%{transform:translateY(0) rotate(0);opacity:0}20%{opacity:1}80%{opacity:1}100%{transform:translateY(-100vh) rotate(360deg);opacity:0}}
.top-fixed-nav{position:fixed;top:20px;right:20px;z-index:100;display:flex;gap:10px;align-items:center}
.top-fixed-nav .btn{background-color:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.3); color:#fff; border-radius:10px; padding:6px 10px; font-weight:500; box-shadow:0 4px 10px rgba(0,0,0,.2)}
.top-fixed-nav img{height:36px;width:auto}
.main-screen-container{
    background:var(--glass-bg);backdrop-filter:blur(15px);-webkit-backdrop-filter:blur(15px);
    border:1px solid var(--glass-border);box-shadow:0 10px 50px 0 var(--glass-shadow);
    border-radius:20px; padding:20px; width:100%; max-width:1000px; z-index:1;
}
/* New Year holiday badge */
.badge[style*="background-color: #ffc107"] {
    background-color: #ffc107 !important;
    color: #000 !important;
    font-weight: bold;
}
h2{font-weight:700;text-align:center;margin-bottom:12px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
.month-nav{display:flex;justify-content:space-between;align-items:center;gap:8px;margin-bottom:8px}
.month-title{font-size:1.05rem;font-weight:700}
.btn-nav{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;border-radius:10px;padding:6px 10px}
.legend{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:8px;font-size:.88rem}
.legend .chip{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.1);border:1px solid var(--glass-border);padding:4px 8px;border-radius:10px}
.legend .dot{width:10px;height:10px;border-radius:50%}
.dot-vac{background:var(--vac)} .dot-half{background:var(--half)} .dot-corr{background:var(--corr)} .dot-abs{background:var(--abs)} .dot-week{background:var(--week)} .dot-holiday{background:var(--holiday)}
.calendar{width:100%;border-collapse:separate;border-spacing:6px}
.calendar th{text-align:center;color:#fff;font-size:.95rem}
.day-cell{height:82px;border:1px solid var(--glass-border);border-radius:14px;background:rgba(255,255,255,.06);position:relative;padding:6px;cursor:pointer;transition:.2s;box-shadow:0 4px 12px rgba(0,0,0,.15)}
.day-cell:hover{transform:translateY(-2px);background:rgba(255,255,255,.12)}
.day-num{position:absolute;top:6px;left:6px;font-weight:700;font-size:.95rem}
.today{outline:2px solid var(--marsom-orange)}
.has-attendance::after{content:'';position:absolute;width:8px;height:8px;border-radius:50%;bottom:6px;left:6px;background:#0dcaf0;box-shadow:0 0 8px rgba(13,202,240,.8)}
.selected{box-shadow:0 0 0 2px #fff inset}
.violation-dot{position:absolute;bottom:6px;right:6px;width:9px;height:9px;border-radius:50%;background:#dc3545;box-shadow:0 0 8px rgba(220,53,69,.8)}
.event-dots{position:absolute;bottom:6px;right:20px;display:flex;gap:4px;align-items:center}
.event-dots .e{width:9px;height:9px;border-radius:50%}
.e.vac{background:var(--vac)} .e.half{background:var(--half)} .e.corr{background:var(--corr)}
.details-card{margin-top:10px;background:rgba(255,255,255,.08);border:1px solid var(--glass-border);border-radius:14px;padding:12px;box-shadow:0 6px 18px rgba(0,0,0,.25)}
.scroll-area{max-height:75vh;overflow:auto;padding-right:4px}
.absent{background: rgba(220,53,69,.18) !important; border-color: rgba(220,53,69,.55) !important;}
.absent:hover{background: rgba(220,53,69,.28) !important;}
.weekend{background: rgba(108,117,125,.15); border-color: rgba(108,117,125,.45);}
.weekend:hover{background: rgba(108,117,125,.25);}
.public-holiday{background: rgba(52, 152, 219, 0.2) !important; border-color: rgba(52, 152, 219, 0.6) !important;}
.public-holiday:hover{background: rgba(52, 152, 219, 0.3) !important;}
</style>
</head>
<body>

<div class="particles">
    <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
    <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
</div>

<div class="top-fixed-nav">
    <button class="btn btn-secondary">مرحباً <?= html_escape($name) ?: 'ضيف' ?></button>
    <a href="<?= site_url('users/logout'); ?>" class="btn btn-secondary"><i class="fas fa-right-from-bracket me-2"></i> خروج</a>
    <a href="<?= site_url('users1/main_emp'); ?>" class="btn btn-secondary"><i class="fas fa-home"></i> الرئيسية</a>
</div>

<div class="main-screen-container">
    <h2>الحضور لـ: <span class="text-warning"><?= html_escape($target_name) ?></span></h2>

   <?php if ($is_hr_user || $is_manager || $is_abha_supervisor || $is_company_2_supervisor): // <-- MODIFIED THIS LINE ?>
    <div class="search-box p-3 mb-3 rounded" style="background: rgba(0,0,0,0.2);">
    <form method="get" action="<?= site_url('users1/attendance') ?>" class="d-flex gap-2">
            
            <?php if ($is_hr_user): ?>
               <input type="text" name="emp_id" class="form-control" value="<?= html_escape($target_username) ?>" placeholder="أدخل الرقم الوظيفي للموظف">
            
            <?php elseif ($is_abha_supervisor): // <-- NEW BLOCK ?>
                <select name="emp_id" class="form-select">
                    <option value="<?= html_escape($this->session->userdata('username')) ?>" <?= ($target_username == $this->session->userdata('username')) ? 'selected' : '' ?>>
                        عرض التقويم الخاص بي (<?= html_escape($this->session->userdata('name')) ?>)
                    </option>
                    <?php foreach ($abha_employees as $emp): ?>
                        <option value="<?= html_escape($emp['username']) ?>" <?= ($target_username == $emp['username']) ? 'selected' : '' ?>>
                            <?= html_escape($emp['name']) ?> (<?= html_escape($emp['username']) ?>) - أبها
                        </option>
                    <?php endforeach; ?>
                </select>
                    <?php elseif ($is_company_2_supervisor): // <--- ADD THIS BLOCK ?>
    <select name="emp_id" class="form-select">
        <option value="<?= html_escape($this->session->userdata('username')) ?>" <?= ($target_username == $this->session->userdata('username')) ? 'selected' : '' ?>>
            عرض التقويم الخاص بي (<?= html_escape($this->session->userdata('name')) ?>)
        </option>
        <?php foreach ($company_2_employees as $emp): ?>
            <option value="<?= html_escape($emp['username']) ?>" <?= ($target_username == $emp['username']) ? 'selected' : '' ?>>
                <?= html_escape($emp['name']) ?> (<?= html_escape($emp['username']) ?>) 
            </option>
        <?php endforeach; ?>
    </select>
            <?php elseif ($is_manager): ?>
                <select name="emp_id" class="form-select">
                    <option value="<?= html_escape($this->session->userdata('username')) ?>" <?= ($target_username == $this->session->userdata('username')) ? 'selected' : '' ?>>
                        عرض التقويم الخاص بي (<?= html_escape($this->session->userdata('name')) ?>)
                    </option>
                    <?php foreach ($subordinates as $sub): ?>
                        <option value="<?= html_escape($sub['username']) ?>" <?= ($target_username == $sub['username']) ? 'selected' : '' ?>>
                            <?= html_escape($sub['name']) ?> (<?= html_escape($sub['username']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

    <button type="submit" class="btn btn-primary">بحث</button>
  </form>
    </div>
    <?php endif; ?>

    <div class="scroll-area">
        <div class="month-nav">
            <a class="btn btn-nav" href="<?= site_url('users1/attendance?y='.$prevY.'&m='.$prevM.'&emp_id='.$target_username) ?>"><i class="fa-solid fa-angles-right"></i> السابق</a>
            <div class="month-title"><?= $monthNameAr[$month] . ' ' . $year ?></div>
            <a class="btn btn-nav" href="<?= site_url('users1/attendance?y='.$nextY.'&m='.$nextM.'&emp_id='.$target_username) ?>">التالي <i class="fa-solid fa-angles-left"></i></a>
        </div>

        <div class="legend">
            <span class="chip"><span class="dot dot-holiday"></span> عطلة رسمية</span>
            <span class="chip"><span class="dot dot-vac"></span> إجازة</span>
            <span class="chip"><span class="dot dot-half"></span> نصف يوم</span>
            <span class="chip"><span class="dot dot-corr"></span> تصحيح بصمة</span>
            <span class="chip"><span class="dot" style="background:#0dcaf0"></span> توجد بصمات</span>
            <span class="chip"><span class="dot dot-abs"></span> غياب</span>
            <span class="chip"><span class="dot dot-week"></span> نهاية الأسبوع</span>
            <span class="chip"><span class="dot" style="background:#dc3545"></span> يوم يحوي مخالفة</span>
        </div>

        <div class="table-responsive">
            <table class="calendar w-100">
                <thead>
                    <tr><?php foreach($weekDaysAr as $wd): ?><th><?= $wd ?></th><?php endforeach; ?></tr>
                </thead>
                <tbody>
             <?php
             echo "<!-- DEBUG INFO -->\n";
echo "<!-- Target Username: " . html_escape($target_username) . " -->\n";
echo "<!-- New Year Holiday Status: " . var_export($new_year_holiday_status, true) . " -->\n";
echo "<!-- Today: " . date('Y-m-d') . " -->\n";
echo "<!-- Current Month: " . $month . " -->\n";
echo "<!-- Current Year: " . $year . " -->\n";
                    $w = $firstWeekday; echo '<tr>';
                    for ($i=0;$i<$w;$i++) echo '<td></td>';
                    $today = date('Y-m-d');
                    for ($d=1;$d<=$daysInMonth;$d++){
                        $date = sprintf('%04d-%02d-%02d',$year,$month,$d);
                        $has = isset($daysMap[$date]);
                        $evs = $eventsByDay[$date] ?? [];
                        $hasViolation = !empty($violationsByDay[$date]);
                        $isWeekend = !empty($WEEKEND_MAP[$date]);
                        $isHoliday = isset($holidaysMap[$date]);
                        $hasApprovedEvent = isset($approvedEventsByDay[$date]);
                        
                        // =================================================================
                        // [START] NEW YEAR HOLIDAY CHECK - MUST BE FIRST
                        // =================================================================
                        $isNewYearHoliday = false;
                        $isNewYearDate = false;
                        
                        // Check if this date is January 1st
                        if (date('m-d', strtotime($date)) == '01-01') {
                            $isNewYearDate = true;
                            // Check if employee has New Year holiday status
                            if (isset($new_year_holiday_status) && $new_year_holiday_status === 1) {
                                $isNewYearHoliday = true;
                            }
                        }

                        // Determine if absent - NEW CORRECTED LOGIC
                        if ($isNewYearHoliday) {
                            // Employee has New Year holiday - NOT absent
                            $isAbsent = false;
                        } elseif ($isHoliday || $isWeekend || $has || $hasApprovedEvent) {
                            // Not absent if it's any other holiday, weekend, has attendance, or approved event
                            $isAbsent = false;
                        } elseif ($date > $today) {
                            // Future date - not absent
                            $isAbsent = false;
                        } else {
                            // Otherwise, it's absent
                            $isAbsent = true;
                        }
                        // =================================================================
                        // [END] NEW YEAR HOLIDAY CHECK
                        // =================================================================
                        
                        $classes = 'day-cell';
                        if ($has) $classes .= ' has-attendance';
                        if ($date === $today) $classes .= ' today';
                        if ($isAbsent) $classes .= ' absent';
                        if ($isHoliday || $isNewYearHoliday) $classes .= ' public-holiday';
                        if ($isWeekend && !$has && empty($evs) && !$isHoliday && !$isNewYearHoliday) $classes .= ' weekend';

                        echo '<td>';
                        echo '<div class="'.$classes.'" data-date="'.$date.'">';
                        echo '<div class="day-num">'.$d.'</div>';
                        
                        // Display logic - New Year holiday should show FIRST
                        if ($isNewYearHoliday) {
                            echo '<div class="mt-4 text-center"><span class="badge" style="background-color: #ffc107; color: #000;">رأس السنة</span></div>';
                        } elseif ($isHoliday) {
                            echo '<div class="mt-4 text-center small"><strong>'.html_escape($holidaysMap[$date]).'</strong></div>';
                        } elseif ($has) {
                            $firstDisp = !empty($daysMap[$date]['first_in']) ? date('H:i', strtotime($daysMap[$date]['first_in'])) : '—';
                            $lastDisp  = !empty($daysMap[$date]['last_out']) ? date('H:i', strtotime($daysMap[$date]['last_out'])) : '—';
                            echo '<div class="mt-4 small text-center" style="opacity:.95">';
                            echo '<div>دخول: <strong>'.$firstDisp.'</strong></div>';
                            echo '<div>خروج: <strong>'.$lastDisp.'</strong></div>';
                            echo '</div>';
                        } elseif ($isAbsent) {
                            echo '<div class="mt-4 text-center"><span class="badge bg-danger">غياب</span></div>';
                        } elseif ($isWeekend) {
                            echo '<div class="mt-4 text-center"><span class="badge bg-secondary">إجازة نهاية الأسبوع</span></div>';
                        } else {
                            echo '<div class="mt-4 small text-center" style="opacity:.6">—</div>';
                        }

                        if (!empty($evs)) {
                            echo '<div class="event-dots">';
                            $count = 0;
                            foreach ($evs as $ev) {
                                $type = $ev['type']==='vac_half'?'half':($ev['type']==='vac_full'?'vac':'corr');
                                echo '<span class="e '.$type.'" title="'.html_escape($ev['title']).'"></span>';
                                if (++$count>=4) break;
                            }
                            echo '</div>';
                        }
                        if ($hasViolation) { echo '<span class="violation-dot" title="يوجد مخالفة"></span>'; }

                        echo '</div></td>';
                        if (++$w>6){ $w=0; echo '</tr><tr>'; }
                    }
                    if ($w!==0){ for ($i=$w;$i<=6;$i++) echo '<td></td>'; echo '</tr>'; }
                ?>
                </tbody>
            </table>
        </div>

        <div class="details-card" id="detailsCard" style="display:none">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fa-solid fa-calendar-day" style="font-size:.95rem"></i>
                <div id="detailsDate" class="fw-bold"></div>
            </div>
            <div id="detailsNote" class="mb-2">—</div>
            <div id="detailsEvents"></div>
            <div class="mt-2 text-warning small">تنبيه: إذا تساوى وقت الدخول والخروج فهذا يعني وجود بصمة واحدة فقط.</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>
    const EVENTS      = <?= json_encode($eventsByDay, JSON_UNESCAPED_UNICODE) ?>;
    const VIOLATIONS  = <?= json_encode($violationsByDay, JSON_UNESCAPED_UNICODE) ?>;
    
    function renderEvents(date) {
        const eventsForDay = EVENTS[date] || [];
        const container = document.getElementById('detailsEvents');
        let html = '';
        
        if (eventsForDay.length > 0) {
            html += '<h6><strong>الأحداث المسجلة:</strong></h6><ul class="list-unstyled mb-0">';
            eventsForDay.forEach(ev => {
                let eventTitle = ev.title;
                let isHalfDay = (ev.type === 'vac_half');
                let statusNum = Number(ev.status);
                let badge;

                if (statusNum === 2) badge = '<span class="badge bg-success">معتمد</span>';
                else if (statusNum === 3) badge = '<span class="badge bg-danger">مرفوض</span>';
                else if (statusNum === -2) badge = '<span class="badge bg-dark">ملغى</span>';
                else badge = '<span class="badge bg-warning text-dark">بالإنتظار</span>';
                
                if (isHalfDay) {
                    let periodText = ev.period ? ` (${ev.period})` : '';
                    html += `<li class="mb-1">إجازة نصف يوم${periodText} ${badge}</li>`;
                } else {
                    html += `<li class="mb-1">${eventTitle} ${badge}</li>`;
                }
            });
            html += '</ul><hr style="margin: 8px 0; opacity:0.2">';
        }
        container.innerHTML = html;
    }

    function renderViolations(date) {
        const violationsForDay = VIOLATIONS[date] || [];
        let html = '';
        if (violationsForDay.length > 0) {
            html += '<div class="mt-2"><h6 class="text-danger" style="font-size:0.9rem"><strong>المخالفات:</strong></h6><ul class="list-unstyled mb-0">';
            violationsForDay.forEach(v => {
                html += `<li class="mb-1"><span class="badge bg-danger">${v.label}: ${v.minutes} دقيقة</span></li>`;
            });
            html += '</ul></div>';
        }
        return html;
    }

    // --- NEW FUNCTION TO RENDER PUNCH DETAILS ---
    function renderPunchDetails(details) {
        if (!details || details.length === 0) return '';
        
        let html = '<div class="mt-3"><h6 style="font-size:0.9rem"><strong>تفاصيل البصمات:</strong></h6>';
        html += '<div class="table-responsive"><table class="table table-sm table-bordered" style="font-size:0.85rem; color:#fff;">';
        html += '<thead style="background:rgba(255,255,255,0.1)"><tr><th>الوقت</th><th>الحالة</th><th>المصدر</th><th>الجهاز</th></tr></thead>';
        html += '<tbody>';
        
        details.forEach(punch => {
            html += `<tr>
                <td class="ltr" style="font-weight:bold">${punch.time}</td>
                <td>${punch.type}</td>
                <td>${punch.source_html}</td>
                <td style="font-size:0.8rem">${punch.device_name}</td>
            </tr>`;
        });
        
        html += '</tbody></table></div></div>';
        return html;
    }
    // -------------------------------------------

    document.querySelectorAll('.day-cell').forEach(cell=>{
        cell.addEventListener('click', function(){
            document.querySelectorAll('.day-cell.selected').forEach(c=>c.classList.remove('selected'));
            this.classList.add('selected');

            const date = this.getAttribute('data-date');
            if (!date) return;

            document.getElementById('detailsCard').style.display = 'block';
            document.getElementById('detailsDate').textContent = date;
            document.getElementById('detailsNote').innerHTML = '<div class="spinner-border spinner-border-sm text-light" role="status"></div> جاري التحميل...';
            
            renderEvents(date);

            const formData = new FormData();
            formData.append('date', date);
            formData.append('emp_id', '<?= html_escape($target_username) ?>');
            <?php if (!empty($csrf_token_name) && !empty($csrf_hash)): ?>
                formData.append('<?= $csrf_token_name ?>', '<?= $csrf_hash ?>');
            <?php endif; ?>

            fetch('<?= site_url('users1/attendance_day'); ?>', {
                method: 'POST',
                body: formData,
                headers: {'X-Requested-With':'XMLHttpRequest'}
            })
            .then(r=>r.json())
            .then(data=>{
                if (!data.ok){
                    document.getElementById('detailsNote').innerHTML = `<span class="text-danger">${data.msg||'حدث خطأ'}</span>` + renderViolations(date);
                    return;
                }
                
                // Combine Summary + Punch Table + Violations
                let content = data.note || '';
                content += renderPunchDetails(data.punch_details); // Add the new table
                content += renderViolations(date);
                
                document.getElementById('detailsNote').innerHTML = content;
            })
            .catch(()=>{
                document.getElementById('detailsNote').innerHTML = '<span class="text-danger">تعذّر الاتصال بالخادم</span>' + renderViolations(date);
            });
        });
    });
</script>
</body>
</html>