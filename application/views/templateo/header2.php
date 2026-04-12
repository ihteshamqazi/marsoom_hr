<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? $title : 'مرسوم HR' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root { --marsom-blue: #001f3f; --marsom-orange: #FF8C00; }
        body { font-family: 'Tajawal'; background: #eef2f5; min-height: 100vh; display: flex; flex-direction: column; }
        
        /* DARK HEADER */
        .navbar-dark-custom {
            background: linear-gradient(90deg, var(--marsom-blue) 0%, #0a3d62 100%);
            padding: 12px 0; color: white;
            box-shadow: 0 4px 15px rgba(0,31,63,0.3);
        }
        .navbar-brand { color: white !important; font-weight: 800; font-size: 1.4rem; }
        .nav-link { color: rgba(255,255,255,0.85) !important; transition: 0.3s; padding: 8px 15px; border-radius: 5px; }
        .nav-link:hover, .show > .nav-link { color: #fff !important; background: rgba(255,255,255,0.1); }
        .dropdown-menu { border-top: 3px solid var(--marsom-orange); border-radius: 0 0 8px 8px; }
        
        /* ACTION BAR */
        .action-bar { background: white; padding: 15px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.03); margin-bottom: 30px; }
        .breadcrumb-item a { color: var(--marsom-blue); text-decoration: none; }
        .page-title { font-weight: 800; color: var(--marsom-blue); margin: 0; font-size: 1.5rem; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark-custom sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('users1/main_hr1') ?>"><i class="fas fa-layer-group me-2"></i> نظام مرسوم</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navDark">
            <span class="navbar-toggler-icon text-white"></span>
        </button>

        <div class="collapse navbar-collapse" id="navDark">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= site_url('users1/main_hr1') ?>">الرئيسية</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">الخدمات الذاتية</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= site_url('users1/mandate_request') ?>">انتداب</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('users1/new_insurance_request') ?>">تأمين</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('users1/labor_case_request') ?>">تظلم</a></li>
                    </ul>
                </li>
            </ul>
            
            <div class="d-flex align-items-center gap-3">
                <a href="<?= site_url('users1/my_mandates') ?>" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="fas fa-user me-1"></i> حسابي
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="action-bar no-print">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"><?= isset($title) ? $title : '' ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('users1/main_hr1') ?>">الرئيسية</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= isset($title) ? $title : 'الصفحة الحالية' ?></li>
                </ol>
            </nav>
        </div>
        <a href="javascript:history.back()" class="btn btn-outline-dark rounded-pill px-4">
            <i class="fas fa-arrow-right me-2"></i> رجوع
        </a>
    </div>
</div>

<div class="container flex-grow-1">