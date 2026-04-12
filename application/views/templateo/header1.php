<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? $title : 'نظام مرسوم للموارد البشرية' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --marsom-blue: #001f3f;
            --marsom-orange: #FF8C00;
            --glass-bg: rgba(255, 255, 255, 0.90);
            --glass-border: 1px solid rgba(255,255,255,0.5);
        }
        body { font-family: 'Tajawal', sans-serif; background: #f4f6f9; padding-top: 80px; min-height: 100vh; display: flex; flex-direction: column; }
        
        /* NAVBAR */
        .navbar-glass {
            background: var(--glass-bg); backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 30px rgba(0,0,0,0.03);
            height: 75px; transition: 0.3s;
        }
        .nav-link { color: #444 !important; font-weight: 600; padding: 10px 15px !important; border-radius: 8px; transition:0.2s; }
        .nav-link:hover, .nav-link.active { background: rgba(0,31,63,0.05); color: var(--marsom-blue) !important; }
        .nav-link i { color: var(--marsom-orange); margin-left: 6px; }
        
        /* SUB-HEADER (Breadcrumb & Back) */
        .sub-header {
            background: white; padding: 10px 0; border-bottom: 1px solid #e9ecef; margin-bottom: 25px;
        }
        .btn-back {
            background: #fff; border: 1px solid #ddd; color: #555; padding: 5px 15px;
            border-radius: 50px; font-size: 0.9rem; text-decoration: none; transition: 0.2s; display: inline-flex; align-items: center; gap: 5px;
        }
        .btn-back:hover { border-color: var(--marsom-blue); color: var(--marsom-blue); transform: translateX(3px); }

        /* USER AVATAR */
        .user-circle {
            width: 38px; height: 38px; background: linear-gradient(135deg, var(--marsom-orange), #ff9f43);
            color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-glass fixed-top">
    <div class="container-fluid px-lg-5">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= site_url('users1/main_hr1') ?>">
            <img src="<?= base_url('assets/images/logo.png') ?>" height="40" alt="Logo" onerror="this.style.display='none'">
            <span style="color:var(--marsom-blue); font-weight:800;">مرسوم HR</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= site_url('users1/main_hr1') ?>"><i class="fas fa-home"></i> الرئيسية</a></li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="fas fa-bolt"></i> إجراء سريع</a>
                    <ul class="dropdown-menu shadow-lg border-0 rounded-3 p-2">
                        <li><a class="dropdown-item p-2 rounded" href="<?= site_url('users1/mandate_request') ?>"><i class="fas fa-plane-departure text-primary me-2"></i> انتداب جديد</a></li>
                        <li><a class="dropdown-item p-2 rounded" href="<?= site_url('users1/new_insurance_request') ?>"><i class="fas fa-heartbeat text-danger me-2"></i> طلب تأمين</a></li>
                        <li><a class="dropdown-item p-2 rounded" href="<?= site_url('users1/eos_request') ?>"><i class="fas fa-door-open text-warning me-2"></i> استقالة</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="fas fa-check-circle"></i> الموافقات</a>
                    <ul class="dropdown-menu shadow-lg border-0 rounded-3 p-2">
                        <li><a class="dropdown-item p-2 rounded" href="<?= site_url('users1/orders_emp_app') ?>">صندوق الوارد</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item p-2 rounded" href="<?= site_url('users1/mandate_approvals') ?>">انتدابات</a></li>
                        <li><a class="dropdown-item p-2 rounded" href="<?= site_url('users1/insurance_approvals') ?>">تأمين طبي</a></li>
                        <li><a class="dropdown-item p-2 rounded" href="<?= site_url('users1/labor_case_approvals') ?>">قضايا عمالية</a></li>
                        <li><a class="dropdown-item p-2 rounded" href="<?= site_url('users1/eos_approvals') ?>">نهاية خدمة</a></li>
                    </ul>
                </li>
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                        <div class="user-circle"><?= substr($this->session->userdata('username'), 0, 1) ?></div>
                        <span class="d-none d-lg-block small fw-bold text-dark"><?= $this->session->userdata('username') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3">
                        <li><a class="dropdown-item" href="<?= site_url('users1/my_mandates') ?>"><i class="fas fa-list me-2"></i> طلباتي</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('users1/profile') ?>"><i class="fas fa-user-cog me-2"></i> الملف الشخصي</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= site_url('users/logout') ?>"><i class="fas fa-power-off me-2"></i> خروج</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="sub-header no-print">
    <div class="container-fluid px-lg-5 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="m-0 fw-bold text-primary"><?= isset($title) ? $title : 'النظام' ?></h5>
        </div>
        <a href="javascript:history.back()" class="btn-back">
            <i class="fas fa-arrow-right"></i> عودة للخلف
        </a>
    </div>
</div>

<div class="container-fluid px-lg-5 flex-grow-1">