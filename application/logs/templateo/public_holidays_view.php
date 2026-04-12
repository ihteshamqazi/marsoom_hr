<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة العطلات الرسمية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f4f7f6; }
        .container { max-width: 800px; }
        .card { border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">إدارة العطلات الرسمية</h3>
            </div>
            <div class="card-body">
                <h5 class="card-title">إضافة عطلة جديدة</h5>
                <?php if($this->session->flashdata('success')): ?>
                    <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
                <?php endif; ?>
                <?php if($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
                <?php endif; ?>

                <form action="<?= site_url('users1/add_holiday') ?>" method="post">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="holiday_name" class="form-label">اسم العطلة</label>
                            <input type="text" class="form-control" id="holiday_name" name="holiday_name" required>
                        </div>
                        <div class="col-md-4">
                            <label for="holiday_date" class="form-label">التاريخ</label>
                            <input type="date" class="form-control" id="holiday_date" name="holiday_date" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">إضافة</button>
                        </div>
                    </div>
                </form>

                <hr class="my-4">

                <h5 class="card-title">العطلات المسجلة</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>اسم العطلة</th>
                                <th>التاريخ</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($holidays)): ?>
                                <?php foreach ($holidays as $holiday): ?>
                                    <tr>
                                        <td><?= html_escape($holiday['holiday_name']) ?></td>
                                        <td><?= $holiday['holiday_date'] ?></td>
                                        <td>
                                            <a href="<?= site_url('users1/delete_holiday/' . $holiday['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">لا توجد عطلات مسجلة حالياً.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>