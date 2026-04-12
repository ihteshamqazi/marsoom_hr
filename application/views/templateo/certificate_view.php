<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="rtl" lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Experience Certificate</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style type="text/css">
        /* Force font in clients that support <style> */
        body, table, td, p, h1, h2, span, div {
            font-family: 'Tajawal', Arial, sans-serif !important;
        }
        
        /* Button Styles - Only for Browser View */
        .print-btn {
            background-color: #001f3f; 
            color: white; 
            padding: 10px 20px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 16px;
            font-family: 'Tajawal', sans-serif;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
        .print-btn:hover {
            background-color: #003366;
        }

        /* HIDE Button when actually printing on paper */
        @media print {
            .no-print { display: none !important; }
            body { background-color: #ffffff; }
            table { border: none !important; } /* Optional: Remove outer border when printing */
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f5f5f5; font-family: 'Tajawal', Arial, sans-serif;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f5f5f5; padding: 20px;">
        <tr>
            <td align="center">

                <div class="no-print" style="text-align: center; margin-bottom: 20px;">
                    <button onclick="window.print();" class="print-btn">
                        🖨️ طباعة الشهادة (Print)
                    </button>
                </div>
                
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border: 10px solid #001f3f; border-radius: 4px; direction: rtl; text-align: right;">
                    
                    <tr>
                        <td align="center" style="background-color: #fff; padding: 30px 20px 10px 20px; border-bottom: 2px solid #001f3f;">
                            <img src="https://services.marsoom.net/hr/assets/images/m2.PNG" 
                                 alt="Marsoom Logo" 
                                 width="200" 
                                 style="display: block; border: 0; max-width: 100%; height: auto;" 
                                 class="floating">
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 30px 20px 10px 20px;">
                            <div style="border: 2px solid #FF8C00; display: inline-block; padding: 10px 30px; border-radius: 8px;">
                                <h2 style="margin: 0; color: #001f3f; font-size: 24px; font-family: 'Tajawal', Arial, sans-serif;">شهادة خبرة مهنية</h2>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 20px 40px; color: #333333; font-size: 16px; line-height: 1.8; font-family: 'Tajawal', Arial, sans-serif;">
                            
                            <p style="border-bottom: 1px solid #cccccc; padding-bottom: 10px; font-size: 13px; color: #666;">
                                <strong>رقم الشهادة:</strong> <?php echo isset($certificate_id) ? $certificate_id : 'CERT-' . date('Ymd') . '-001'; ?> <br>
                                <strong>تاريخ الإصدار:</strong> <?php echo date('Y-m-d'); ?>
                            </p>

                            <p style="font-size: 18px; font-weight: bold; color: #001f3f; margin-top: 20px;">إلى من يهمه الأمر،،،</p>

                            <table border="0" cellpadding="10" cellspacing="0" width="100%" style="background-color: #f8f9fa; margin: 20px 0; border-right: 5px solid #001f3f;">
                                <tr>
                                    <td width="30%" style="font-weight: bold; color: #001f3f; font-family: 'Tajawal', Arial, sans-serif;">اسم الموظف:</td>
                                    <td style="color: #000; font-weight: bold; background-color: #fff; border: 1px solid #eee; font-family: 'Tajawal', Arial, sans-serif;"><?php echo isset($full_name_ar) ? $full_name_ar : '---'; ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; color: #001f3f; font-family: 'Tajawal', Arial, sans-serif;">المسمى الوظيفي:</td>
                                    <td style="color: #000; font-weight: bold; background-color: #fff; border: 1px solid #eee; font-family: 'Tajawal', Arial, sans-serif;"><?php echo isset($job_title) ? $job_title : '---'; ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; color: #001f3f; font-family: 'Tajawal', Arial, sans-serif;">تاريخ الالتحاق:</td>
                                    <td style="color: #000; font-weight: bold; background-color: #fff; border: 1px solid #eee; font-family: 'Tajawal', Arial, sans-serif;"><?php echo isset($join_date) ? $join_date : '---'; ?></td>
                                </tr>
                            </table>

                            <p style="text-align: justify; margin-bottom: 20px;">
                                تشهد <strong>شركة مرسوم للتجارة والمقاولات</strong> بأن الموظف المذكور أعلاه قد عمل لدينا، وقد تميز خلال فترة عمله بالأمانة والجدية في أداء مهامه.
                            </p>
                            <p style="text-align: justify; margin-bottom: 20px;">
                                ونحن نؤكد صحة المعلومات الواردة أعلاه، ونتقدم بالشكر والتقدير له على جهوده المبذولة.
                            </p>
                            <p style="text-align: justify;">
                                وتعطى هذه الشهادة بناءً على طلبه، دون أدنى مسؤولية على الشركة.
                            </p>

                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" width="33%" valign="top" style="font-family: 'Tajawal', Arial, sans-serif;">
                                        <div style="border-bottom: 1px solid #000; width: 80%; margin: 0 auto 10px auto;"></div>
                                        <strong>مدير الموارد البشرية</strong><br>
                                        <span style="font-size: 12px; color: #666;">HR Manager</span>
                                    </td>
                                    
                                    <td align="center" width="33%" valign="middle" style="font-family: 'Tajawal', Arial, sans-serif;">
                                        <div style="width: 100px; height: 100px; border: 3px solid #001f3f; border-radius: 50%; line-height: 100px; color: #001f3f; font-weight: bold; font-size: 12px;">
                                            ختم الشركة
                                        </div>
                                    </td>

                                    <td align="center" width="33%" valign="top" style="font-family: 'Tajawal', Arial, sans-serif;">
                                        <div style="border-bottom: 1px solid #000; width: 80%; margin: 0 auto 10px auto;"></div>
                                        <strong>المدير التنفيذي</strong><br>
                                        <span style="font-size: 12px; color: #666;">Executive Director</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="background-color: #f0f0f0; color: #666666; font-size: 12px; padding: 15px; border-top: 1px solid #ddd; font-family: 'Tajawal', Arial, sans-serif;">
                            <p style="margin: 0 0 5px 0;">هذه الشهادة صادرة آلياً من نظام مرسوم</p>
                            <p style="margin: 0;">الرياض - المملكة العربية السعودية | hr@marsoom.com</p>
                        </td>
                    </tr>

                </table>
                </td>
        </tr>
    </table>

</body>
</html>