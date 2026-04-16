<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي للموظف</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root{
            --marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;
            --glass-bg:rgba(255,255,255,.92);--glass-border:rgba(255,255,255,.3);--glass-shadow:rgba(0,0,0,.15)
        }
        body{font-family:'Tajawal',sans-serif;overflow-x:hidden;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .particles{position:fixed;inset:0;overflow:hidden;z-index:-1;pointer-events: none;}
        .particle{position:absolute;background:rgba(255,140,0,.1);clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);animation:float 25s infinite ease-in-out;opacity:0;filter:blur(2px)}
        .particle:nth-child(even){background:rgba(0,31,63,.1)}
        .particle:nth-child(1){width:40px;height:40px;left:10%;top:20%;animation-duration:18s} .particle:nth-child(2){width:70px;height:70px;left:25%;top:50%;animation-duration:22s;animation-delay:2s} .particle:nth-child(3){width:55px;height:55px;left:40%;top:10%;animation-duration:25s;animation-delay:5s} .particle:nth-child(4){width:80px;height:80px;left:60%;top:70%;animation-duration:20s;animation-delay:8s} .particle:nth-child(5){width:60px;height:60px;left:80%;top:30%;animation-duration:23s;animation-delay:10s} .particle:nth-child(6){width:45px;height:45px;left:5%;top:85%;animation-duration:19s;animation-delay:3s} .particle:nth-child(7){width:90px;height:90px;left:70%;top:5%;animation-duration:28s;animation-delay:6s} .particle:nth-child(8){width:35px;height:35px;left:90%;top:40%;animation-duration:17s;animation-delay:12s} .particle:nth-child(9){width:75px;height:75px;left:20%;top:75%;animation-duration:21s;animation-delay:1s} .particle:nth-child(10){width:65px;height:65px;left:50%;top:90%;animation-duration:24s;animation-delay:4s}
        @keyframes float{0%{transform:translateY(0) translateX(0) rotate(0);opacity:0}20%{opacity:1}80%{opacity:1}100%{transform:translateY(-100vh) translateX(50px) rotate(360deg);opacity:0}}
        .main-container{padding:2rem 2rem;position:relative;z-index:1;min-height:100vh}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.8rem;color:var(--text-light);margin-bottom:2rem;text-align:center;position:relative;display:inline-block;padding-bottom:10px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .page-title::after{content:'';position:absolute;width:100px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
        .content-card{background:var(--glass-bg);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);border:1px solid var(--glass-border);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px;height:100%}
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .top-actions a:hover{background:rgba(255,255,255,.2);color:var(--marsom-orange)}
        .profile-menu .menu-header{text-align:center;padding-bottom:20px;border-bottom:1px solid rgba(0,0,0,.1);margin-bottom:15px}
        .profile-menu .avatar{width:90px;height:90px;border-radius:50%;border:3px solid var(--marsom-orange);margin-bottom:10px}
        .profile-menu h5{font-family:'El Messiri',sans-serif;font-weight:700;margin-bottom:2px;font-size:1.1rem}
        .profile-menu p{font-size:.9rem;color:#6c757d}
        .profile-menu .list-group-item{background-color:transparent;border:none;color:var(--text-dark);font-weight:700;cursor:pointer;transition:all .2s ease;padding:15px 10px;border-radius:8px;margin-bottom:5px}
        .profile-menu .list-group-item:hover,.profile-menu .list-group-item.active{background-color:rgba(0,31,63,.08);color:var(--marsom-orange)}
        .profile-menu .list-group-item i{width:25px;margin-left:10px}
        .profile-menu .list-group-item.danger-item:hover{color:#dc3545;background-color:rgba(220,53,69,.1)}
        #details-container{min-height:650px;padding:20px}
        .content-title{font-family:'El Messiri',sans-serif;text-align:center;font-weight:700;color:var(--marsom-blue);position:relative;padding-bottom:15px;margin-bottom:40px;font-size:2rem}
        .content-title::after{content:'';position:absolute;width:80px;height:3px;background-color:var(--marsom-orange);bottom:0;left:50%;transform:translateX(-50%)}
        .info-box{text-align:center;padding:1rem;margin-bottom:1rem}
        .info-box .info-value{font-size:1.4rem;font-weight:700;color:var(--text-dark);margin-bottom:8px}
        .info-box .info-label{font-size:1rem;color:#6c757d;margin-bottom:0}
        .pie-chart-container{position:relative;margin:1rem auto;height:500px;max-width:500px}
        .status-badge{padding:6px 16px;border-radius:9999px;font-weight:700;font-size:1rem}
        .status-active{background-color:#d1fae5;color:#065f46}
        .status-inactive{background-color:#f3f4f6;color:#4b5563}
        .balance-card{background-color:#fff;border-radius:10px;padding:20px;text-align:center;box-shadow:0 4px 12px rgba(0,0,0,.08);transition:transform .2s ease,box-shadow .2s ease;border-top:4px solid var(--marsom-blue)}
        .balance-card:hover{transform:translateY(-5px);box-shadow:0 8px 20px rgba(0,0,0,.12)}
        .balance-card .balance-title{font-family:'El Messiri',sans-serif;font-weight:700;margin-bottom:15px;font-size:1.3rem;color:var(--marsom-blue)}
        .balance-card .balance-value{font-size:2.5rem;font-weight:800;color:var(--marsom-orange);line-height:1}
        .balance-card .balance-unit{font-size:.9rem;color:#6c757d}
        .balance-card .balance-details{margin-top:15px;font-size:.9rem;color:#495057}
        .salary-slip{background-color:#fff;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,.08);overflow:hidden}
        .slip-header{background-color:var(--marsom-blue);color:#fff;padding:15px 20px}
        .slip-header h5{font-family:'El Messiri',sans-serif;margin:0}
        .slip-body{padding:20px}
        .slip-section-title{font-weight:700;color:var(--marsom-blue);border-bottom:2px solid #eee;padding-bottom:8px;margin-bottom:15px;margin-top:20px;font-size:1.1rem}
        .slip-item{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f1f1}
        .slip-item .label{color:#6c757d}
        .slip-item .value{font-weight:700}
        .slip-total{display:flex;justify-content:space-between;padding:15px;margin-top:20px;background-color:#f8f9fa;border-radius:8px;font-size:1.2rem}
        .slip-total .label{font-family:'El Messiri',sans-serif;font-weight:700}
        .slip-total .value{color:var(--marsom-orange);font-weight:800}
    </style>
</head>
<body>
    <div class="particles">
        <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
    </div>

    <div class="top-actions">
        <a href="<?php echo site_url('users1/main_emp'); ?>" style="text-decoration: none;"><i class="fas fa-home"></i> الرئيسية</a>
        <a href="javascript:history.back()"><i class="fas fa-arrow-right"></i> رجوع</a>
    </div>

    <div class="main-container container-fluid">
        <div class="text-center">
            <h1 class="page-title">الملف الشخصي</h1>
        </div>
        
        <div class="row">
            <div class="col-lg-3 mb-4">
                <div class="content-card profile-menu">
                    <div class="menu-header">
                        <img src="https://placehold.co/100x100/001f3f/FFFFFF?text=SS" class="avatar">
                        <h5></h5>
                        <p></p>
                    </div>
                    <ul class="list-group list-group-flush" id="profile-menu-list">
                        <li class="list-group-item active" onclick="loadPersonalDetails()">
                            <i class="fas fa-user-check fa-fw"></i><span>المعلومات الشخصية</span>
                        </li>
                        <li class="list-group-item" onclick="loadJobDetails()">
                            <i class="fas fa-id-badge fa-fw"></i><span>البيانات الوظيفية</span>
                        </li>
                        <li class="list-group-item" onclick="loadFinancialDetails()">
                            <i class="fas fa-money-check-alt fa-fw"></i><span>الراتب والتفاصيل المالية</span>
                        </li>
                        <li class="list-group-item" onclick="loadLeaveBalances()">
                            <i class="fas fa-calendar-alt fa-fw"></i><span>أرصدة الإجازات</span>
                        </li>
                    <!--    <li class="list-group-item" onclick="loadLastSalarySlip()">
                            <i class="fas fa-receipt fa-fw"></i><span>آخر مسير راتب</span>
                        </li>-->
                        <li class="list-group-item" onclick="loadContractDetails()">
                            <i class="fas fa-file-signature fa-fw"></i><span>العقود</span>
                        </li>  
                        <a href="<?php echo site_url('users1/logout'); ?>" style="text-decoration: none;">
                            <li class="list-group-item danger-item">
                                <i class="fas fa-sign-out-alt fa-fw"></i><span>تسجيل الخروج</span>
                            </li>
                        </a>
                    </ul>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="content-card" id="details-container">
                    </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadPersonalDetails();
        });

        const detailsContainer = document.getElementById('details-container');

        function setActiveMenuItem(element) {
            document.querySelectorAll('#profile-menu-list .list-group-item').forEach(item => item.classList.remove('active'));
            if(element) element.classList.add('active');
        }

        document.querySelectorAll('#profile-menu-list .list-group-item').forEach(item => {
            item.addEventListener('click', function() { setActiveMenuItem(this); });
        });

        function showLoading() {
            detailsContainer.innerHTML = `
                <div class="d-flex justify-content-center align-items-center w-100 h-100" style="min-height: 400px;">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                </div>`;
        }
        
        async function loadPersonalDetails() {
            showLoading();
            try {
                // CORRECTED URL
                const response = await fetch('<?php echo site_url("users1/get_personal_details"); ?>');
                const result = await response.json();

                if (result.status === 'success' && result.data) {
                    const data = result.data;
                    document.querySelector('.menu-header h5').textContent = data.subscriber_name || 'اسم الموظف';
                    document.querySelector('.menu-header p').textContent = `ID: ${data.employee_id || 'غير محدد'}`;
                    let contentHtml = `
                        <div class="row">
                            <div class="col-12"><h3 class="content-title">المعلومات الشخصية</h3></div>
                            <div class="col-md-4 col-6"><div class="info-box"><p class="info-value">${data.subscriber_name || 'غير محدد'}</p><p class="info-label">اسم الموظف</p></div></div>
                            <div class="col-md-4 col-6"><div class="info-box"><p class="info-value">${data.employee_id || 'غير محدد'}</p><p class="info-label">رقم الموظف</p></div></div>
                            <div class="col-md-4 col-6"><div class="info-box"><p class="info-value">${data.id_number || 'غير محدد'}</p><p class="info-label">رقم الهوية/الإقامة</p></div></div>
                            <div class="col-md-4 col-6"><div class="info-box"><p class="info-value">${data.birth_date || 'غير محدد'}</p><p class="info-label">تاريخ الميلاد</p></div></div>
                            <div class="col-md-4 col-6"><div class="info-box"><p class="info-value">${data.gender || 'غير محدد'}</p><p class="info-label">الجنس</p></div></div>
                            <div class="col-md-4 col-6"><div class="info-box"><p class="info-value">${data.nationality || 'غير محدد'}</p><p class="info-label">الجنسية</p></div></div>
                            <div class="col-md-6"><div class="info-box"><p class="info-value">${data.email || 'غير محدد'}</p><p class="info-label">البريد الإلكتروني</p></div></div>
                            <div class="col-md-6"><div class="info-box"><p class="info-value">${data.phone || 'غير محدد'}</p><p class="info-label">رقم الهاتف</p></div></div>
                        </div>`;
                    detailsContainer.innerHTML = contentHtml;
                } else {
                    detailsContainer.innerHTML = `<div class="alert alert-warning">${result.message || 'لم يتم العثور على البيانات.'}</div>`;
                }
            } catch (error) {
                detailsContainer.innerHTML = `<div class="alert alert-danger">حدث خطأ في الاتصال بالخادم.</div>`;
            }
        }

        async function loadJobDetails() {
            showLoading();
            try {
                 // CORRECTED URL
                const response = await fetch('<?php echo site_url("users1/get_job_details"); ?>');
                const result = await response.json();
                if (result.status === 'success' && result.data) {
                    const data = result.data;
                    let contentHtml = `
                        <div class="row">
                            <div class="col-12"><h3 class="content-title">البيانات الوظيفية</h3></div>
                            <div class="col-md-4 col-6"><div class="info-box"><p class="info-value">${data.profession || 'غير محدد'}</p><p class="info-label">المسمى الوظيفي</p></div></div>
                            <div class="col-md-4 col-6"><div class="info-box"><p class="info-value">${data.n1 || 'غير محدد'}</p><p class="info-label">القسم</p></div></div>
                            <div class="col-md-4 col-6"><div class="info-box"><p class="info-value">${data.joining_date || 'غير محدد'}</p><p class="info-label">تاريخ التعيين</p></div></div>
                            <div class="col-md-4 col-6"><div class="info-box"><p class="info-value">${data.manager || 'غير محدد'}</p><p class="info-label">المدير المباشر</p></div></div>
                            <div class="col-md-4 col-6"><div class="info-box"><p class="info-value">${data.company_name || 'غير محدد'}</p><p class="info-label">الشركة</p></div></div>
                            <div class="col-md-4 col-6"><div class="info-box"><p class="info-value">${data.location || 'غير محدد'}</p><p class="info-label">الموقع</p></div></div>
                        </div>`;
                    detailsContainer.innerHTML = contentHtml;
                } else {
                    detailsContainer.innerHTML = `<div class="alert alert-warning">${result.message || 'لم يتم العثور على البيانات.'}</div>`;
                }
            } catch (error) {
                detailsContainer.innerHTML = `<div class="alert alert-danger">حدث خطأ في الاتصال بالخادم.</div>`;
            }
        }

       async function loadFinancialDetails() {
            showLoading();
            try {
                 // CORRECTED URL
                const response = await fetch('<?php echo site_url("users1/get_financial_details"); ?>');
                const result = await response.json();
                if (result.status === 'success' && result.data) {
                    const data = result.data;
                    const baseSalary = parseFloat(data.base_salary || 0);
                    const housingAllowance = parseFloat(data.housing_allowance || 0);
                    const transportAllowance = parseFloat(data.n4 || 0);
                    const otherAllowances = parseFloat(data.other_allowances || 0);
                    const totalSalary = parseFloat(data.total_salary || 0);

                    let contentHtml = `
                        <div class="row">
                            <div class="col-12"><h3 class="content-title">تفاصيل الراتب</h3></div>
                            <div class="col-lg-7">
                                <div class="row">
                                    <div class="col-6"><div class="info-box"><p class="info-value">${baseSalary.toLocaleString()} ريال</p><p class="info-label">الراتب الأساسي</p></div></div>
                                    <div class="col-6"><div class="info-box"><p class="info-value">${housingAllowance.toLocaleString()} ريال</p><p class="info-label">بدل السكن</p></div></div>
                                    <div class="col-6"><div class="info-box"><p class="info-value">${transportAllowance.toLocaleString()} ريال</p><p class="info-label">بدل النقل</p></div></div>
                                    <div class="col-6"><div class="info-box"><p class="info-value">${otherAllowances.toLocaleString()} ريال</p><p class="info-label">بدلات أخرى</p></div></div>
                                    <div class="col-12"><hr class="my-3"><div class="info-box"><p class="info-value">${totalSalary.toLocaleString()} ريال</p><p class="info-label">إجمالي الراتب </p></div></div>
                                </div>
                            </div>
                            <div class="col-lg-5 d-flex align-items-center justify-content-center">
                                <div class="pie-chart-container"><canvas id="salaryPieChart"></canvas></div>
                            </div>
                        </div>`;
                    detailsContainer.innerHTML = contentHtml;

                    const ctx = document.getElementById('salaryPieChart').getContext('2d');
                    if (window.mySalaryChart) window.mySalaryChart.destroy();
                    
                    window.mySalaryChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['الراتب الأساسي', 'بدل السكن', 'بدل النقل', 'بدلات أخرى'],
                            datasets: [{
                                data: [baseSalary, housingAllowance, transportAllowance, otherAllowances],
                                backgroundColor: ['#4299E1', '#48BB78', '#F6AD55', '#9F7AEA'],
                                borderColor: '#ffffff',
                                borderWidth: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: { display: true, text: 'توزيع الراتب', position: 'top', font: { family: "'El Messiri', sans-serif", size: 20, weight: 'bold' }, color: '#001f3f', padding: { bottom: 20 } },
                                legend: { display: true, position: 'bottom', labels: { usePointStyle: false, boxWidth: 12, padding: 25, font: { family: "'Tajawal', sans-serif", size: 14 } } }
                            },
                        }
                    });
                } else {
                    detailsContainer.innerHTML = `<div class="alert alert-warning">${result.message || 'لم يتم العثور على البيانات.'}</div>`;
                }
            } catch (error) {
                detailsContainer.innerHTML = `<div class="alert alert-danger">حدث خطأ في الاتصال بالخادم.</div>`;
            }
        }

        async function loadLeaveBalances() {
            showLoading();
            try {
                 // CORRECTED URL
                const response = await fetch('<?php echo site_url("users1/get_leave_balances"); ?>');
                const result = await response.json();
                if (result.status === 'success' && result.data) {
                    let contentHtml = `<div class="row g-4"><div class="col-12"><h3 class="content-title">أرصدة الإجازات</h3></div>`;
                    
                    if (result.data.length > 0) {
                        result.data.forEach(balance => {
                            contentHtml += `
                                <div class="col-lg-4 col-md-6">
                                    <div class="balance-card">
                                        <h4 class="balance-title">${balance.leave_type_name || 'N/A'}</h4>
                                        <p class="balance-value">${parseFloat(balance.remaining_balance).toFixed(1)} <span class="balance-unit">يوم</span></p>
                                        <p class="balance-details">
                                            المتاح: ${parseFloat(balance.balance_allotted).toFixed(1)} | 
                                            المستخدم: ${parseFloat(balance.balance_consumed).toFixed(1)}
                                        </p>
                                    </div>
                                </div>`;
                        });
                    } else {
                        contentHtml += '<div class="col-12"><div class="alert alert-info">لا توجد أرصدة إجازات مسجلة لهذا الموظف.</div></div>';
                    }
                    
                    contentHtml += `</div>`;
                    detailsContainer.innerHTML = contentHtml;
                } else {
                    detailsContainer.innerHTML = `<div class="alert alert-warning">${result.message || 'لم يتم العثور على البيانات.'}</div>`;
                }
            } catch (error) {
                detailsContainer.innerHTML = `<div class="alert alert-danger">حدث خطأ في الاتصال بالخادم.</div>`;
            }
        }

        async function loadLastSalarySlip() {
            showLoading();
            try {
                 // CORRECTED URL
                const response = await fetch('<?php echo site_url("users1/get_last_salary_slip"); ?>');
                const result = await response.json();
                if (result.status === 'success' && result.data) {
                    const data = result.data;
                    const formatCurrency = (num) => parseFloat(num || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    let contentHtml = `
                        <div class="row justify-content-center">
                            <div class="col-12"><h3 class="content-title">آخر مسير راتب</h3></div>
                            <div class="col-lg-8">
                                <div class="salary-slip">
                                    <div class="slip-header">
                                        <h5>مسير راتب شهر: ${data.n13 || 'غير محدد'}</h5>
                                    </div>
                                    <div class="slip-body">
                                        <div class="slip-section-title">المستحقات</div>
                                        <div class="slip-item"><span class="label">الراتب الأساسي</span><span class="value">${formatCurrency(data.base_salary)} ريال</span></div>
                                        <div class="slip-item"><span class="label">البدلات</span><span class="value">${formatCurrency(data.total_salary - data.base_salary)} ريال</span></div>
                                        <div class="slip-item"><span class="label"><strong>إجمالي المستحقات</strong></span><span class="value"><strong>${formatCurrency(data.total_salary)} ريال</strong></span></div>
                                        
                                        <div class="slip-section-title">الخصومات</div>
                                        <div class="slip-item"><span class="label">خصم التأخير والخروج المبكر</span><span class="value">${formatCurrency(data.late_early_deduction)} ريال</span></div>
                                        <div class="slip-item"><span class="label">خصم الغياب</span><span class="value">${formatCurrency(data.absence_deduction)} ريال</span></div>
                                        <div class="slip-item"><span class="label">التأمينات الاجتماعية</span><span class="value">${formatCurrency(data.insurance_deduction)} ريال</span></div>
                                        <div class="slip-item"><span class="label"><strong>إجمالي الخصومات</strong></span><span class="value"><strong>${formatCurrency(data.total_deductions)} ريال</strong></span></div>

                                        <div class="slip-total">
                                            <span class="label">صافي الراتب</span>
                                            <span class="value">${formatCurrency(data.net_salary)} ريال</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    detailsContainer.innerHTML = contentHtml;
                } else {
                    detailsContainer.innerHTML = `<div class="alert alert-warning">${result.message || 'لا يوجد مسير رواتب سابق مسجل.'}</div>`;
                }
            } catch (error) {
                detailsContainer.innerHTML = `<div class="alert alert-danger">حدث خطأ في الاتصال بالخادم.</div>`;
            }
        }
        
        async function loadContractDetails() {
            showLoading();
             try {
                 // CORRECTED URL
                const response = await fetch('<?php echo site_url("users2/get_contract_details"); ?>');
                const result = await response.json();
                if (result.status === 'success' && result.data) {
                    const data = result.data;
                    const statusClass = (data.status && data.status.toLowerCase() === 'active') ? 'status-active' : 'status-inactive';
                    const statusText = (data.status && data.status.toLowerCase() === 'active') ? 'ساري' : 'غير ساري';
                    
                    let contentHtml = `
                        <div class="row">
                            <div class="col-12"><h3 class="content-title">تفاصيل العقد</h3></div>
                            <div class="col-md-4 col-6"><div class="info-box"><p class="info-value"><span class="status-badge ${statusClass}">${statusText}</span></p><p class="info-label">حالة العقد</p></div></div>
                            <div class="col-md-4 col-6"><div class="info-box"><p class="info-value">${data.contract_period || 'غير محدد'}</p><p class="info-label">مدة العقد</p></div></div>
                            <div class="col-md-4 col-6"><div class="info-box"><p class="info-value">${data.remaining_renewal_period || 'غير محدد'}</p><p class="info-label">المدة المتبقية للتجديد</p></div></div>
                            <div class="col-md-6"><div class="info-box"><p class="info-value">${data.contract_start || 'غير محدد'}</p><p class="info-label">تاريخ بداية العقد</p></div></div>
                            <div class="col-md-6"><div class="info-box"><p class="info-value">${data.contract_end || 'غير محدد'}</p><p class="info-label">تاريخ نهاية العقد</p></div></div>
                        </div>`;
                    detailsContainer.innerHTML = contentHtml;
                } else {
                    detailsContainer.innerHTML = `<div class="alert alert-warning">${result.message || 'لم يتم العثور على البيانات.'}</div>`;
                }
            } catch (error) {
                detailsContainer.innerHTML = `<div class="alert alert-danger">حدث خطأ في الاتصال بالخادم.</div>`;
            }
        }
    </script>
</body>
</html>