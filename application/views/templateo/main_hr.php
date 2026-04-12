<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام الموارد البشرية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background: #f4f6f9; /* خلفية هادئة تعكس الجو العام للموقع */
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            margin-top: 50px;
        }

        .header {
            text-align: center;
            font-size: 35px;
            margin-bottom: 50px;
            font-weight: bold;
            color: #2c3e50;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #ffffff; /* خلفية بيضاء أنيقة لكل بطاقة */
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .card-body {
            text-align: center;
            padding: 30px;
        }

        .btn-custom {
            background-color: #3498db; /* لون أزرق هادئ */
            border-radius: 8px;
            color: white;
            font-size: 18px;
            padding: 20px 30px;
            width: 100%;
            border: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #2980b9; /* تفاعل مع تغيير اللون عند التمرير */
            transform: scale(1.05);
        }

        .btn-custom i {
            margin-left: 15px;
            font-size: 22px;
        }

        /* تأثير الأنيميشن */
        .btn-animation {
            animation: fadeInUp 0.8s ease-in-out;
        }

        @keyframes fadeInUp {
            0% {
                transform: translateY(30px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .col-md-4 {
            margin-bottom: 30px;
        }

        .card-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        /* تعديل الألوان لتحسين التناسق */
        .card-body button {
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        نظام الموارد البشرية
    </div>

    <div class="row justify-content-center">
        <!-- زر بيانات الموظفين -->
       <div class="col-md-4 col-sm-6 col-12">
    <div class="card btn-animation">
        <div class="card-body">
            <div class="card-title">بيانات الموظفين</div>
            <button class="btn btn-custom" onclick="window.location.href='<?php echo site_url('users1/emp_data'); ?>';">
                <span>عرض البيانات</span>
                <i class="fas fa-users"></i>
            </button>
        </div>
    </div>
</div>


        <!-- زر استيراد بيانات بصمة الحضور والانصراف -->
        <div class="col-md-4 col-sm-6 col-12">
            <div class="card btn-animation">
                <div class="card-body">
                    <div class="card-title">استيراد بيانات بصمة الحضور والانصراف</div>
                    <button class="btn btn-custom">
                        <span>استيراد البيانات</span>
                        <i class="fas fa-fingerprint"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- زر ملف المرتبات -->
        <div class="col-md-4 col-sm-6 col-12">
    <div class="card btn-animation">
        <div class="card-body">
            <div class="card-title"> الرواتب </div>
            <button class="btn btn-custom" onclick="window.location.href='<?php echo site_url('users1/salary_sheet'); ?>';">
                <span> عرض مسيرات الرواتب  </span>
                <i class="fas fa-file-invoice-dollar"></i>
            </button>
        </div>
    </div>
</div>


        <!-- زر الإجازات -->
        <div class="col-md-4 col-sm-6 col-12">
    <div class="card btn-animation">
        <div class="card-body">
            <div class="card-title">الإجازات</div>
            <button class="btn btn-custom" onclick="window.location.href='<?php echo site_url('users1/vacations'); ?>';">
                <span>عرض الإجازات</span>
                <i class="fas fa-calendar-day"></i>
            </button>
        </div>
    </div>
</div>


        <!-- زر الخصومات والجزاءات -->
        <div class="col-md-4 col-sm-6 col-12">
    <div class="card btn-animation">
        <div class="card-body">
            <div class="card-title">الخصومات والجزاءات</div>
            <button class="btn btn-custom" onclick="window.location.href='<?php echo site_url('users1/discounts'); ?>';">
                <span>عرض الخصومات</span>
                <i class="fas fa-percent"></i>
            </button>
        </div>
    </div>
</div>


        <!-- زر التعويضات -->
       <div class="col-md-4 col-sm-6 col-12">
    <div class="card btn-animation">
        <div class="card-body">
            <div class="card-title">التعويضات</div>
            <button class="btn btn-custom" onclick="window.location.href='<?php echo site_url('users1/reparations'); ?>';">
                <span>عرض التعويضات</span>
                <i class="fas fa-hand-holding-usd"></i>
            </button>
        </div>
    </div>
</div>

    </div>
</div>

<!-- روابط Bootstrap و jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
