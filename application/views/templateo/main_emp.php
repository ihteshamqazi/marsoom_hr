<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة الموارد البشرية | Marsom HR</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;500;600;700&family=Inter:wght@300;400;500;600&family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #001f3f;
            --primary-orange: #FF8C00;
            --secondary-blue: #0a3d62;
            --secondary-orange: #ff9f43;
            --light-blue: #4a69bd;
            --light-orange: #ffd166;
            --dark-bg: #0d1b2a;
            --darker-bg: #0a1929;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --card-bg: rgba(255, 255, 255, 0.07);
            --text-light: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.6);
            --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.4);
            --shadow-sm: 0 5px 15px rgba(0, 0, 0, 0.2);
            --gradient-blue: linear-gradient(135deg, #001f3f 0%, #0a3d62 100%);
            --gradient-orange: linear-gradient(135deg, #FF8C00 0%, #ff9f43 100%);
            --gradient-mix: linear-gradient(135deg, #001f3f 0%, #FF8C00 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, var(--darker-bg) 0%, var(--primary-blue) 30%, #1a1a2e 70%, var(--dark-bg) 100%);
            min-height: 100vh;
            color: var(--text-light);
            overflow-x: hidden;
            position: relative;
            background-attachment: fixed;
        }

        /* Animated Background Pattern */
        .bg-pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(255, 140, 0, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(0, 31, 63, 0.05) 0%, transparent 20%),
                linear-gradient(45deg, transparent 48%, rgba(255, 140, 0, 0.03) 50%, transparent 52%),
                linear-gradient(-45deg, transparent 48%, rgba(0, 31, 63, 0.03) 50%, transparent 52%);
            background-size: 400px 400px, 400px 400px, 100px 100px, 100px 100px;
            z-index: -2;
            animation: patternMove 20s linear infinite;
        }

        @keyframes patternMove {
            0% { background-position: 0 0, 0 0, 0 0, 0 0; }
            100% { background-position: 400px 400px, 400px 400px, 100px 100px, 100px 100px; }
        }

        /* Floating Orbs */
        .floating-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.15;
            animation: orbFloat 30s ease-in-out infinite;
            z-index: -1;
        }

        .orb-1 {
            width: 300px;
            height: 300px;
            background: var(--primary-orange);
            top: 10%;
            right: 5%;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 400px;
            height: 400px;
            background: var(--primary-blue);
            bottom: 10%;
            left: 5%;
            animation-delay: -10s;
            animation-duration: 40s;
        }

        .orb-3 {
            width: 200px;
            height: 200px;
            background: var(--secondary-orange);
            top: 50%;
            left: 20%;
            animation-delay: -5s;
            animation-duration: 25s;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(100px, -50px) scale(1.1); }
            50% { transform: translate(-50px, 100px) scale(0.9); }
            75% { transform: translate(-100px, -50px) scale(1.05); }
        }

        /* Main Container */
        .main-container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        /* Enhanced Header Navigation */
        .header-nav {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 15px 30px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .header-nav::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary-orange), transparent);
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logo-section img {
            height: 50px;
            width: auto;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.4));
            transition: transform 0.3s ease;
        }

        .logo-section img:hover {
            transform: scale(1.05);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            background: linear-gradient(135deg, rgba(255, 140, 0, 0.1), rgba(0, 31, 63, 0.1));
            padding: 10px 20px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        .user-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, transparent 30%, rgba(255, 255, 255, 0.05) 100%);
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: var(--gradient-orange);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            color: white;
            box-shadow: 0 4px 15px rgba(255, 140, 0, 0.3);
            z-index: 1;
        }

        .user-details {
            z-index: 1;
        }

        .user-details strong {
            font-size: 16px;
            font-weight: 600;
            display: block;
            margin-bottom: 2px;
        }

        .user-details .user-id {
            font-size: 12px;
            opacity: 0.8;
            background: rgba(255, 255, 255, 0.1);
            padding: 2px 8px;
            border-radius: 10px;
            display: inline-block;
        }

        .nav-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .nav-btn {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.05));
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: var(--text-light);
            padding: 10px 18px;
            border-radius: 15px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .nav-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .nav-btn:hover::before {
            left: 100%;
        }

        .nav-btn:hover {
            transform: translateY(-3px);
            border-color: var(--primary-orange);
            box-shadow: 0 8px 25px rgba(255, 140, 0, 0.2);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), rgba(255, 255, 255, 0.08));
        }

        .nav-btn i {
            font-size: 16px;
            transition: transform 0.3s ease;
        }

        .nav-btn:hover i {
            transform: scale(1.2);
        }

        /* Enhanced Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, rgba(0, 31, 63, 0.7), rgba(255, 140, 0, 0.7));
            border-radius: 25px;
            padding: 40px;
            text-align: center;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        }

        .welcome-card h1 {
            font-family: 'El Messiri', serif;
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 15px rgba(0,0,0,0.5);
            background: linear-gradient(135deg, #ffffff, #ffd166);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-card p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .welcome-decoration {
            position: absolute;
            bottom: -20px;
            right: -20px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            filter: blur(20px);
        }

        /* Compact Section Groups */
        .section-group {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 25px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }

        .section-group::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-orange), var(--primary-blue));
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .section-icon {
            width: 45px;
            height: 45px;
            background: var(--gradient-orange);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            box-shadow: 0 5px 15px rgba(255, 140, 0, 0.3);
            flex-shrink: 0;
        }

        .section-title {
            font-family: 'El Messiri', serif;
            font-size: 1.6rem;
            font-weight: 600;
            margin: 0;
            color: var(--text-light);
            flex-grow: 1;
        }

        .section-count {
            background: rgba(255, 255, 255, 0.1);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--primary-orange);
        }

        /* Compact Action Grid with Smaller Boxes */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 18px;
        }

        .action-card {
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 20px;
            text-decoration: none;
            color: var(--text-light);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
            height: 100%;
            backdrop-filter: blur(10px);
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.03) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .action-card:hover::before {
            opacity: 1;
        }

        .action-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-orange), var(--primary-blue));
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.4s ease;
        }

        .action-card:hover::after {
            transform: scaleX(1);
            transform-origin: left;
        }

        .action-card:hover {
            transform: translateY(-5px) scale(1.02);
            border-color: rgba(255, 140, 0, 0.3);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        }

        .action-icon {
            width: 60px;
            height: 60px;
            background: var(--gradient-blue);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 5px 15px rgba(0, 31, 63, 0.3);
            z-index: 1;
            position: relative;
        }

        .action-card:hover .action-icon {
            transform: translateY(-3px) scale(1.1);
            background: var(--gradient-orange);
            box-shadow: 0 8px 20px rgba(255, 140, 0, 0.4);
        }

        .action-title {
            font-family: 'El Messiri', serif;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
            line-height: 1.3;
            z-index: 1;
            position: relative;
        }

        .action-desc {
            font-size: 0.8rem;
            opacity: 0.8;
            line-height: 1.4;
            margin-bottom: 12px;
            z-index: 1;
            position: relative;
        }

        .action-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: var(--gradient-orange);
            color: white;
            font-size: 0.65rem;
            padding: 3px 10px;
            border-radius: 12px;
            font-weight: 600;
            z-index: 2;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .action-stat {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            font-size: 0.65rem;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 600;
            z-index: 2;
        }

        /* Enhanced Quick Stats */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-orange), var(--primary-blue));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 140, 0, 0.3);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary-orange), var(--secondary-orange));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .stat-content h4 {
            font-size: 0.9rem;
            margin-bottom: 5px;
            opacity: 0.8;
            font-weight: 500;
        }

        .stat-content p {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-light);
            background: linear-gradient(135deg, #ffffff, #ffd166);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Notification Badge */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: linear-gradient(135deg, #ff4757, #ff6b81);
            color: white;
            font-size: 0.6rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(255, 71, 87, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        /* Responsive Design */
        @media (max-width: 1400px) {
            .action-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }

        @media (max-width: 1200px) {
            .action-grid {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            }
            
            .welcome-card h1 {
                font-size: 2.3rem;
            }
        }

        @media (max-width: 992px) {
            .header-nav {
                flex-direction: column;
                gap: 20px;
                padding: 20px;
            }
            
            .logo-section {
                width: 100%;
                justify-content: center;
            }
            
            .nav-actions {
                width: 100%;
                justify-content: center;
            }
            
            .action-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 15px;
            }
            
            .quick-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .action-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .welcome-card {
                padding: 30px 20px;
            }
            
            .welcome-card h1 {
                font-size: 1.8rem;
            }
            
            .welcome-card p {
                font-size: 1rem;
            }
            
            .section-header {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
            
            .quick-stats {
                grid-template-columns: 1fr;
            }
            
            .action-icon {
                width: 50px;
                height: 50px;
                font-size: 1.3rem;
            }
            
            .action-title {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .action-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-btn {
                padding: 8px 15px;
                font-size: 13px;
            }
            
            .user-info {
                padding: 8px 15px;
            }
            
            .user-avatar {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
        }

        /* Animation Delays */
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
        .delay-5 { animation-delay: 0.5s; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--primary-orange), var(--primary-blue));
            border-radius: 5px;
            border: 2px solid transparent;
            background-clip: padding-box;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, var(--secondary-orange), var(--secondary-blue));
        }

        /* Floating Animation */
        @keyframes floatUp {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .floating {
            animation: floatUp 3s ease-in-out infinite;
        }

        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--primary-orange);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-pattern"></div>
    <div class="floating-orb orb-1"></div>
    <div class="floating-orb orb-2"></div>
    <div class="floating-orb orb-3"></div>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Enhanced Header Navigation -->
        <nav class="header-nav" data-aos="fade-down" data-aos-duration="800">
            <div class="logo-section">
                <img src="<?php echo base_url(); ?>assets/imeges/m2.PNG" alt="Marsom Logo" class="floating">
               <a href="https://services.marsoom.net/hr/users1/profile" class="user-profile-link" style="text-decoration: none; color: inherit;">
    <div class="user-info">
        <div class="user-avatar">
            <?php echo substr($this->session->userdata('name') ?? 'U', 0, 1); ?>
        </div>
        <div class="user-details">
            <strong><?php echo $this->session->userdata('name') ?? 'مستخدم'; ?></strong>
            <span class="user-id"><?php echo $this->session->userdata('username') ?? ''; ?></span>
        </div>
    </div>
</a>
            </div>
            
            <div class="nav-actions">
                <?php if(in_array($this->session->userdata('username') ?? '', array('1835', '2230', '2515', '2774', '2784', '2901'))): ?>
                <a href="<?php echo site_url('users1/main_hr1'); ?>" class="nav-btn" data-aos="fade-left" data-aos-delay="100">
                    <i class="fas fa-chart-line"></i>
                    <span>لوحة التحكم</span>
                </a>
                <?php endif; ?>
                
                <a href="#" class="nav-btn" id="btnNotifications" data-aos="fade-left" data-aos-delay="200">
                    <i class="fas fa-bell"></i>
                    <span>الإشعارات</span>
                    <span class="notification-badge">3</span>
                </a>
                
                <a href="<?= site_url('users/logout'); ?>" class="nav-btn" data-aos="fade-left" data-aos-delay="300"
                   style="background: linear-gradient(135deg, rgba(255, 71, 87, 0.2), rgba(255, 107, 129, 0.1));">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>تسجيل خروج</span>
                </a>
            </div>
        </nav>

        <!-- Welcome Card -->
        <div class="welcome-card" data-aos="fade-up" data-aos-duration="1000">
            <div class="welcome-decoration"></div>
             <h1>مرحباً في نظام الموارد البشرية </h1>
            <p>إدارة شاملة للطلبات، الحضور، المهام، والتقييمات في منصة واحدة</p>
        </div>

        <!-- Dashboard Sections -->
        <div class="dashboard-sections">
            <!-- Group 1: إدارة الطلبات والاعتمادات -->
            <div class="section-group" data-aos="fade-up" data-aos-delay="100">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h2 class="section-title">إدارة الطلبات والاعتمادات</h2>
                    <span class="section-count">8 خدمات</span>
                </div>
                <div class="action-grid">
                    <a href="<?php echo site_url('users1/orders_emp'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="150">
                        <div class="action-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h3 class="action-title">طلباتي</h3>
                        <p class="action-desc">عرض وتقديم الطلبات الشخصية</p>
                        <div class="action-badge">شخصي</div>
                        <div class="action-stat">2 جديد</div>
                    </a>
                    
                    <a href="<?php echo site_url('users1/orders_emp_app'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="action-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="action-title">طلبات الموظفين</h3>
                        <p class="action-desc">مراجعة واعتماد طلبات الفريق</p>
                        <div class="action-badge">فريق</div>
                        <div class="action-stat">5 قيد الانتظار</div>
                    </a>
                    
                    <a href="<?php echo site_url('users1/mandate_request'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="250">
                        <div class="action-icon">
                            <i class="fas fa-plane-departure"></i>
                        </div>
                        <h3 class="action-title">طلب انتداب</h3>
                        <p class="action-desc">تقديم طلب انتداب جديد</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/mandate_approvals'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="action-icon">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <h3 class="action-title">اعتماد الانتدابات</h3>
                        <p class="action-desc">مراجعة واعتماد طلبات الانتداب</p>
                        <div class="action-stat">3 جاهزة</div>
                    </a>
                    <a href="<?php echo site_url('ramadan/remote_work'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="400">
    <div class="action-icon">
        <i class="fas fa-laptop-house"></i>
    </div>
    <h3 class="action-title">العمل عن بعد</h3>
    <p class="action-desc">تقديم واعتماد طلبات العمل عن بعد للمحصلين</p>
    <div class="action-stat text-success">متاح الآن</div>
</a>
                    <?php 
// Define the allowed users array
$allowed_users = ['1835', '1127', '2230', '2774', '2515', '2901', '2784'];

// Get the current logged-in user ID from the session
// (Change 'username' to 'user_id' or whatever session key you use for the ID)
$current_user = $this->session->userdata('username'); 

// Check if the current user is in the allowed list
if (in_array($current_user, $allowed_users)): 
?>
    <a href="<?php echo site_url('users1/manage_employees_list'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="300">
        <div class="action-icon">
            <i class="fas fa-check-double"></i>
        </div>
        <h3 class="action-title"> تعديل بيانات الموظف </h3>
        <p class="action-desc"> تعديل بيانات الموظف </p>
    </a>
<?php endif; ?>
<?php 
// Define the allowed users array
$allowed_users = ['1835', '1127', '2230', '2774', '2515', '2901', '2784'];

// Get the current logged-in user ID from the session
$current_user = $this->session->userdata('username'); 

// Check if the current user is in the allowed list
if (in_array($current_user, $allowed_users)): 
?>
    <a href="<?php echo site_url('users1/manage_documents'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="400">
        <div class="action-icon">
            <i class="fas fa-folder-open"></i>
        </div>
        <h3 class="action-title"> إدارة المستندات (Documents) </h3>
        <p class="action-desc"> إضافة، تنزيل، ومتابعة المستندات المتعددة </p>
    </a>
<?php endif; ?>
                    <a href="<?php echo site_url('users1/renewal_system'); ?>" class="action-card" data-aos="fade-up">
    <div class="action-icon">
        <i class="fas fa-id-card"></i>
    </div>
    <h3 class="action-title">نظام تجديد الهويات</h3>
    <p class="action-desc">طلبات التجديد والمهام</p>
</a>
                    <?php 
// List of users allowed to see the Overtime Dashboard
$ot_allowed_users = ['2901', '2774', '1001', '2230', '1835', '1693', '2784','2909','1936','2833'];
$current_user_id = $this->session->userdata('username');

if(in_array($current_user_id, $ot_allowed_users)): 
?>
    <a href="<?php echo site_url('users1/overtime_dashboard'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="350">
        <div class="action-icon">
            <i class="fas fa-business-time"></i>
        </div>
        <h3 class="action-title">متابعة العمل الإضافي</h3>
        <p class="action-desc">مراجعة وصرف مستحقات الإضافي</p>
        <div class="action-stat">جديد</div>
    </a>
<?php endif; ?>
                    <a href="<?php echo site_url('users1/eos_approvals'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="350">
                        <div class="action-icon">
                            <i class="fas fa-check-to-slot"></i>
                        </div>
                        <h3 class="action-title">نهاية الخدمة</h3>
                        <p class="action-desc">اعتماد طلبات إنهاء الخدمة</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/insurance_approvals'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="action-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h3 class="action-title"> الموافقات التأمينية</h3>
                        <p class="action-desc">اعتماد طلبات التأمين الطبي</p>
                    </a>
                     <a href="<?php echo site_url('users1/new_insurance_request'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="action-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h3 class="action-title">طلبات التأمين</h3>
                        <p class="action-desc">اعتماد طلبات التأمين الطبي</p>
                    </a>
                   
                    
                    <a href="<?php echo site_url('users1/my_mandates'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="500">
                        <div class="action-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <h3 class="action-title">سجل الانتدابات</h3>
                        <p class="action-desc">عرض جميع انتداباتي السابقة</p>
                    </a>
                    <a href="<?php echo site_url('users1/my_insurance_requests'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="600">
    <div class="action-icon">
        <i class="fas fa-file-medical"></i>
    </div>
    <h3 class="action-title">سجل التأمين الطبي</h3>
    <p class="action-desc">عرض ومتابعة طلبات التأمين</p>
</a>
                </div>
            </div>

            <!-- Group 2: الحضور والرواتب -->
            <div class="section-group" data-aos="fade-up" data-aos-delay="150">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h2 class="section-title">الحضور والرواتب</h2>
                    <span class="section-count">2 خدمات</span>
                </div>
                <div class="action-grid">
                    <a href="<?php echo site_url('users1/attendance/'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="action-icon">
                            <i class="fas fa-fingerprint"></i>
                        </div>
                        <h3 class="action-title">الحضور والانصراف</h3>
                        <p class="action-desc">تسجيل الحضور ومشاهدة السجل</p>
                        <div class="action-stat">✅ اليوم</div>
                    </a>
                    
                    <a href="<?php echo site_url('users1/my_salary_slips'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="250">
                        <div class="action-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h3 class="action-title">قسائم الراتب</h3>
                        <p class="action-desc">عرض وتحميل قسائم الراتب الشهرية</p>
                        <div class="action-stat">12 قسيمة</div>
                    </a>
                </div>
            </div>

            <!-- Group 3: إدارة المهام -->
            <div class="section-group" data-aos="fade-up" data-aos-delay="200">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h2 class="section-title">إدارة المهام</h2>
                    <span class="section-count">3 خدمات</span>
                </div>
                <div class="action-grid">

                    <a href="https://services.marsoom.net/sla/SlaEmployeePortal" class="action-card" data-aos="fade-up" data-aos-delay="250">
    <div class="action-icon">
        <i class="fas fa-ticket-alt"></i>
    </div>
    <h3 class="action-title">منصة التذاكر (SLA)</h3>
    <p class="action-desc">إدارة طلبات الدعم الفني ومتابعة التذاكر</p>
    <div class="action-stat">عرض التذاكر الحالية</div>
</a>


                    <a href="<?php echo site_url('users1/task_manager_dashboard'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="250">
                        <div class="action-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h3 class="action-title">إدارة المهام</h3>
                        <p class="action-desc">إنشاء وإدارة مهام الفريق</p>
                        <div class="action-stat">15 نشطة</div>
                    </a>
                    
                    <a href="<?php echo site_url('users1/my_tasks_dashboard'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="action-icon">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <h3 class="action-title">مهامي</h3>
                        <p class="action-desc">عرض وإدارة مهامي الموكلة</p>
                        <div class="action-stat">5 مهام</div>
                    </a>
                    
                    <a href="<?php echo site_url('users1/my_clearance_tasks'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="350">
                        <div class="action-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3 class="action-title">مهام المخالصة</h3>
                        <p class="action-desc">إدارة مهام إنهاء الخدمة</p>
                    </a>
                    <a href="<?php echo site_url('users1/violations_list'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="600">
                        <div class="action-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h3 class="action-title">سجل المخالفات</h3>
                        <p class="action-desc">عرض ومتابعة سجل الملاحظات</p>
                    </a>
                    <?php if($this->session->userdata('username') == '1127' or $this->session->userdata('username') == '2901'): ?>
                     <a href="https://services.marsoom.net/collection/users/productivity_report" class="action-card" data-aos="fade-up" data-aos-delay="600">
                        <div class="action-icon">
                            <i class="fas fa-funnel-dollar"></i>
                        </div>
                        <h3 class="action-title">التحصيل</h3>
                        <p class="action-desc">تقارير إنتاجية التحصيل</p>
                        <div class="action-badge">خارجي</div>
                    </a>
                    <?php endif; ?>
<?PHP if ($this ->session->userdata('username') == '1859' or $this ->session->userdata('username') == '1835' or $this ->session->userdata('username') == '2803' or $this ->session->userdata('username') == '1140' or $this ->session->userdata('username') == '2230' or $this ->session->userdata('username') == '1146' or $this ->session->userdata('username') == '1291' or $this ->session->userdata('username') == '2403' or $this ->session->userdata('username') == '1001' or $this ->session->userdata('username') == '1060' or $this ->session->userdata('username') == '3100' or $this ->session->userdata('username') == '3118' or $this ->session->userdata('username') == '1190' or $this ->session->userdata('username') == '1034' or $this ->session->userdata('username') == '2219'):   
                                      ?>

                    <a href="https://services.marsoom.net/collection/meetings" class="action-card" data-aos="fade-up" data-aos-delay="600">
                        <div class="action-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="action-title">  الاجتماعات </h3>
                      
                    </a>

 <?PHP endif;?>


                    <?php
                     //   $CI =& get_instance();
                      //  $employee_no = $CI->session->userdata('username');

                    //    $exists = $CI->db
                    //        ->where('employee_no', $employee_no)
                     //       ->limit(1)
                    //        ->count_all_results('ai_departments') > 0;
                        ?>


                    <?php //if ($exists): ?>

                       <?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '1001' or $this->session->userdata('username') == '2803'or $this->session->userdata('username') == '2796'): ?>
                    <a href="https://services.marsoom.net/collection/ai/inbox" 
                       class="action-card" 
                       data-aos="fade-up" 
                       data-aos-delay="350">

                        <div class="action-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>

                        <h3 class="action-title">مستشار الذكاء الاصطناعي</h3>

                    </a>
                    <?php// endif; ?>

                    <?php endif; ?>

                    <?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '1001'): ?>

                     <a href="<?= site_url('AnnualIncentives'); ?>" 
                       class="action-card" 
                       data-aos="fade-up" 
                       data-aos-delay="350">

                        <div class="action-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>

                        <h3 class="action-title"> الحوافز السنوية </h3>

                    </a>


                 
                   
                    
                 
                <?php endif; ?>


                 <?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '2230' or $this->session->userdata('username') == '1291'or $this->session->userdata('username') == '2666'): ?>

                     <a href="https://services.marsoom.net/poll/TrainingControlPanel" 
                       class="action-card" 
                       data-aos="fade-up" 
                       data-aos-delay="350">

                        <div class="action-icon">
                           <i class="fas fa-chalkboard-user"></i>
                        </div>

                        <h3 class="action-title">   لوحة تحكم الدورات والاستبيانات  </h3>

                    </a>


                 
                   
                    
                 
                <?php endif; ?>



                 <?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '1859'): ?>

                     <a href="https://services.marsoom.net/recruitment2/MdPendingEvaluations" 
                       class="action-card" 
                       data-aos="fade-up" 
                       data-aos-delay="350">

                        <div class="action-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>

                        <h3 class="action-title"> طلبات العضو المنتدب المعلقة ( التوظيف )    </h3>

                    </a>


                 
                   
                    
                 
                <?php endif; ?>




                </div>
            </div>

            <!-- Group 4: المعلومات الشخصية -->
            <div class="section-group" data-aos="fade-up" data-aos-delay="250">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h2 class="section-title">المعلومات الشخصية</h2>
                    <span class="section-count">3 خدمات</span>
                </div>
                <div class="action-grid">
                    <a href="<?php echo site_url('users1/profile'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="action-icon">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <h3 class="action-title">ملف الموظف</h3>
                        <p class="action-desc">عرض وتعديل المعلومات الشخصية</p>
                        <div class="action-badge">👤</div>
                    </a>
                    
                    <a href="<?php echo site_url('users1/team_balances_dashboard'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="350">
                        <div class="action-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3 class="action-title">أرصدة الإجازات</h3>
                        <p class="action-desc">عرض أرصدة الإجازات الخاصة بي</p>
                        <div class="action-stat">21 يوم</div>
                    </a>
                    
                    <a href="<?php echo site_url('users1/leave_capacity_dashboard'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="action-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h3 class="action-title">أرصدة الفريق</h3>
                        <p class="action-desc">متابعة أرصدة إجازات الفريق</p>
                        <div class="action-badge">فريق</div>
                    </a>
                    
                    <?php if(in_array($this->session->userdata('username') ?? '', array('1835', '1127', '2901'))): ?>
                    <a href="<?php echo site_url('users1/letter_management'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="450">
                        <div class="action-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3 class="action-title">رسائل الموظفين</h3>
                        <p class="action-desc">إدارة المراسلات الرسمية</p>
                        <div class="action-badge">خاص</div>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Group 5: التقييم والأداء -->
            <div class="section-group" data-aos="fade-up" data-aos-delay="300">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h2 class="section-title">التقييم والأداء</h2>
                    <span class="section-count">3 خدمات</span>
                </div>
                <div class="action-grid">
                    <a href="<?php echo site_url('users1/employee_survey'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="350">
                        <div class="action-icon" style="background: linear-gradient(135deg, #6f42c1, #9c88ff);">
                            <i class="fas fa-poll-h"></i>
                        </div>
                        <h3 class="action-title">استبيان الرضا</h3>
                        <p class="action-desc">شاركنا رأيك لتطوير بيئة العمل</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/happiness_index'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="action-icon" style="background: linear-gradient(135deg, #ffc107, #ffd166);">
                            <i class="fas fa-smile-beam"></i>
                        </div>
                        <h3 class="action-title">مؤشر السعادة</h3>
                        <p class="action-desc">قياس مستوى السعادة الوظيفية</p>
                    </a>
                    
                    <a href="<?php echo site_url('users/user_report101'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="450" target="_blank">
                        <div class="action-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h3 class="action-title">تقارير التقييم</h3>
                        <p class="action-desc">عرض تقارير التقييم والأداء</p>
                        <div class="action-stat">📊</div>
                    </a>
                </div>
            </div>

             <div class="section-group" data-aos="fade-up" data-aos-delay="300">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h2 class="section-title">التقييم والأداء</h2>
                    <span class="section-count">3 خدمات</span>
                </div>
                <div class="action-grid">

                   <?php
$username = (string)$this->session->userdata('username');

$annual_employee_eval_users = [
    '1146','2803','1768','1001','1140','2230','2796','1622','1693','1291'
];

$self_eval_users = [
    '1061','2218','1835','1976','1768','1754','2674','2219','2342','2439',
    '2774','1146','2115','2403','1859','2443','2230','2140','1533','1109',
    '1480','2515','1195','1622','2909','2176','1703','2901','1140','1693',
    '2784','2795','2081','1936','2200','2666','2121','2796','1397','2803',
    '2694','1526','2104','1291','1127','1231','2833','1345','1130','1045',
    '2695','1501','2023','1136'
];
?>

<?php if (in_array($username, $annual_employee_eval_users, true)): ?>
<a href="<?php echo site_url('AnnualEvaluationSupervisor'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="350">
    <div class="action-icon" style="background: linear-gradient(135deg, #1d4ed8, #6366f1);">
        <i class="fas fa-user-check"></i>
    </div>
    <h3 class="action-title">التقييم السنوي للموظفين</h3>
    <p class="action-desc">
        تقييم أداء الموظفين التابعين لك مباشرة وفق نموذج التقييم السنوي المعتمد.
    </p>
</a>
<?php endif; ?>

<?php if (in_array($username, $self_eval_users, true)): ?>
<a href="<?php echo site_url('AnnualEvaluation'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="400">
    <div class="action-icon" style="background: linear-gradient(135deg, #059669, #14b8a6);">
        <i class="fas fa-clipboard-user"></i>
    </div>
    <h3 class="action-title">التقييم الذاتي</h3>
    <p class="action-desc">
        إدخال التقييم الذاتي السنوي الخاص بك وفق المعايير المعتمدة في النظام.
    </p>
</a>
<?php endif; ?>


                    <a href="<?php echo site_url('users1/employee_survey'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="350">
                        <div class="action-icon" style="background: linear-gradient(135deg, #6f42c1, #9c88ff);">
                            <i class="fas fa-poll-h"></i>
                        </div>
                        <h3 class="action-title">استبيان الرضا</h3>
                        <p class="action-desc">شاركنا رأيك لتطوير بيئة العمل</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/happiness_index'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="action-icon" style="background: linear-gradient(135deg, #ffc107, #ffd166);">
                            <i class="fas fa-smile-beam"></i>
                        </div>
                        <h3 class="action-title">مؤشر السعادة</h3>
                        <p class="action-desc">قياس مستوى السعادة الوظيفية</p>
                    </a>
                    
                    <a href="<?php echo site_url('users/user_report101'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="450" target="_blank">
                        <div class="action-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h3 class="action-title">تقارير التقييم</h3>
                        <p class="action-desc">عرض تقارير التقييم والأداء</p>
                        <div class="action-stat">📊</div>
                    </a>
                    <?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '2901'): ?>
    <a href="<?php echo site_url('emp_management/index'); ?>" 
       class="action-card" 
       data-aos="fade-up" 
       data-aos-delay="600">

        <div class="action-icon">
            <i class="fas fa-users-cog"></i>
        </div>

        <h3 class="action-title">إدارة الموظفين (EMP1)</h3>

    </a>
<?php endif; ?>
 <?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '2901'): ?>
    <a href="<?php echo site_url('emp_management/v_orders_emp'); ?>" 
       class="action-card" 
       data-aos="fade-up" 
       data-aos-delay="600">

        <div class="action-icon">
            <i class="fas fa-users-cog"></i>
        </div>

        <h3 class="action-title">إإدارة الطلبات (ORDERS_EMP)</h3>

    </a>
<?php endif; ?>
 <?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '2901'): ?>
    <a href="<?php echo site_url('emp_management/v_approval_workflow'); ?>" 
       class="action-card" 
       data-aos="fade-up" 
       data-aos-delay="600">

        <div class="action-icon">
            <i class="fas fa-users-cog"></i>
        </div>

        <h3 class="action-title">إدارة سير عمل الموافقة (APPROVAL_WORKFLOW)</h3>

    </a>
<?php endif; ?>
 <?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '2901'): ?>
    <a href="<?php echo site_url('emp_management/attendance_logs'); ?>" 
       class="action-card" 
       data-aos="fade-up" 
       data-aos-delay="600">

        <div class="action-icon">
            <i class="fas fa-users-cog"></i>
        </div>

        <h3 class="action-title">إدارة سجلات الحضور   (ATTENDANCE_LOGS)</h3>

    </a>
<?php endif; ?>
<?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '2901'): ?>
    <a href="<?php echo site_url('emp_management/mandate_requests'); ?>" 
       class="action-card" 
       data-aos="fade-up" 
       data-aos-delay="600">

        <div class="action-icon">
            <i class="fas fa-users-cog"></i>
        </div>

        <h3 class="action-title">إدارة طلبات التفويض     (MANDATE_REQUESTS)</h3>

    </a>
<?php endif; ?>
<?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '2901'): ?>

    <a href="<?php echo site_url('emp_management/v_attendance_summary'); ?>" 
       class="action-card" 
       data-aos="fade-up" 
       data-aos-delay="600">
        <div class="action-icon">
            <i class="fas fa-calendar-check"></i>
        </div>
        <h3 class="action-title">ملخص الحضور والانصراف (ATTENDANCE_SUMMARY)</h3>
    </a>

    <a href="<?php echo site_url('emp_management/v_payroll_process'); ?>" 
       class="action-card" 
       data-aos="fade-up" 
       data-aos-delay="650">
        <div class="action-icon">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <h3 class="action-title">مسير الرواتب (PAYROLL_PROCESS)</h3>
    </a>

    <a href="<?php echo site_url('emp_management/v_discounts'); ?>" 
       class="action-card" 
       data-aos="fade-up" 
       data-aos-delay="700">
        <div class="action-icon">
            <i class="fas fa-minus-circle"></i>
        </div>
        <h3 class="action-title">الخصومات (DISCOUNTS)</h3>
    </a>

    <a href="<?php echo site_url('emp_management/v_reparations'); ?>" 
       class="action-card" 
       data-aos="fade-up" 
       data-aos-delay="750">
        <div class="action-icon">
            <i class="fas fa-hand-holding-usd"></i>
        </div>
        <h3 class="action-title">التعويضات (REPARATIONS)</h3>
    </a>

    <a href="<?php echo site_url('emp_management/v_employee_violations'); ?>" 
       class="action-card" 
       data-aos="fade-up" 
       data-aos-delay="800">
        <div class="action-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 class="action-title">مخالفات الموظفين (EMPLOYEE_VIOLATIONS)</h3>
    </a>

    <a href="<?php echo site_url('emp_management/v_employee_leave_balances'); ?>" 
       class="action-card" 
       data-aos="fade-up" 
       data-aos-delay="850">
        <div class="action-icon">
            <i class="fas fa-plane-departure"></i>
        </div>
        <h3 class="action-title">أرصدة إجازات الموظفين (LEAVE_BALANCES)</h3>
    </a>
    <a href="<?php echo site_url('emp_management/v_end_of_service_settlements'); ?>" 
       class="action-card" 
       data-aos="fade-up" 
       data-aos-delay="900">
        <div class="action-icon">
            <i class="fas fa-handshake-slash"></i>
        </div>
        <h3 class="action-title">مخالصات نهاية الخدمة (EOS_SETTLEMENTS)</h3>
    </a>
    <a href="<?php echo site_url('emp_management/v_resignation_clearances'); ?>" 
       class="action-card" 
       data-aos="fade-up" 
       data-aos-delay="950">
        <div class="action-icon">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <h3 class="action-title">إخلاء طرف الاستقالات (RESIGNATION_CLEARANCES)</h3>
    </a>
<?php endif; ?>
<?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '2901'): ?>

    <a href="<?php echo site_url('emp_management/v_insurance_discount'); ?>" 
       class="action-card" data-aos="fade-up" data-aos-delay="1000">
        <div class="action-icon"><i class="fas fa-shield-alt"></i></div>
        <h3 class="action-title">خصم التأمين (INSURANCE_DISCOUNT)</h3>
    </a>

    <a href="<?php echo site_url('emp_management/v_new_employees'); ?>" 
       class="action-card" data-aos="fade-up" data-aos-delay="1050">
        <div class="action-icon"><i class="fas fa-user-plus"></i></div>
        <h3 class="action-title">الموظفين الجدد (NEW_EMPLOYEES)</h3>
    </a>

    <a href="<?php echo site_url('emp_management/v_stop_salary'); ?>" 
       class="action-card" data-aos="fade-up" data-aos-delay="1100">
        <div class="action-icon"><i class="fas fa-hand-paper"></i></div>
        <h3 class="action-title">إيقاف الراتب (STOP_SALARY)</h3>
    </a>

    <a href="<?php echo site_url('emp_management/v_work_restrictions'); ?>" 
       class="action-card" data-aos="fade-up" data-aos-delay="1150">
        <div class="action-icon"><i class="fas fa-user-lock"></i></div>
        <h3 class="action-title">قيود العمل (WORK_RESTRICTIONS)</h3>
    </a>

<?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Enhanced Quick Stats -->
        <div class="quick-stats" data-aos="fade-up" data-aos-delay="400">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h4>الحضور اليوم</h4>
                    <p>✅ مسجل</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <div class="stat-content">
                    <h4>طلبات قيد الانتظار</h4>
                    <p>8 طلبات</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h4>أيام الإجازة المتبقية</h4>
                    <p>26 يوم</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-content">
                    <h4>المهام النشطة</h4>
                    <p>5 مهام</p>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 50,
            easing: 'ease-out-cubic'
        });

        // Initialize animations
     
        function showToast(message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `toast-notification ${type}`;
            toast.innerHTML = `
                <div class="toast-content">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
                    <span>${message}</span>
                </div>
                <button class="toast-close"><i class="fas fa-times"></i></button>
            `;
            
            // Add styles
            toast.style.cssText = `
                position: fixed;
                top: 30px;
                left: 30px;
                background: rgba(0, 31, 63, 0.9);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 16px;
                padding: 18px 24px;
                color: white;
                display: flex;
                align-items: center;
                justify-content: space-between;
                min-width: 320px;
                max-width: 400px;
                z-index: 9999;
                animation: slideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                border-left: 4px solid ${type === 'success' ? '#00b894' : '#0984e3'};
            `;
            
            // Add close button functionality
            const closeBtn = toast.querySelector('.toast-close');
            closeBtn.addEventListener('click', () => {
                toast.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            });
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 5000);
            
            // Add to body
            document.body.appendChild(toast);
            
            // Add keyframes for animation
            if (!document.querySelector('#toast-styles')) {
                const style = document.createElement('style');
                style.id = 'toast-styles';
                style.textContent = `
                    @keyframes slideIn {
                        from { transform: translateX(-100%); opacity: 0; }
                        to { transform: translateX(0); opacity: 1; }
                    }
                    @keyframes slideOut {
                        from { transform: translateX(0); opacity: 1; }
                        to { transform: translateX(-100%); opacity: 0; }
                    }
                    .toast-notification .toast-content {
                        display: flex;
                        align-items: center;
                        gap: 12px;
                        font-family: 'Tajawal', sans-serif;
                    }
                    .toast-notification .toast-content i {
                        font-size: 1.2rem;
                    }
                    .toast-notification .toast-close {
                        background: rgba(255, 255, 255, 0.1);
                        border: none;
                        color: white;
                        width: 30px;
                        height: 30px;
                        border-radius: 50%;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 0.9rem;
                        transition: all 0.3s ease;
                    }
                    .toast-notification .toast-close:hover {
                        background: rgba(255, 255, 255, 0.2);
                        transform: rotate(90deg);
                    }
                `;
                document.head.appendChild(style);
            }
        }

        // Add real-time clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit'
            });
            const dateString = now.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            const clockElement = document.getElementById('live-clock');
            if (clockElement) {
                clockElement.innerHTML = `
                    <div style="text-align: center; font-family: 'Tajawal';">
                        <div style="font-size: 1.5rem; font-weight: 600; color: var(--primary-orange);">
                            ${timeString}
                        </div>
                        <div style="font-size: 0.9rem; opacity: 0.8;">
                            ${dateString}
                        </div>
                    </div>
                `;
            }
        }

        // Create and add live clock if not exists
        if (!document.getElementById('live-clock')) {
            const clockDiv = document.createElement('div');
            clockDiv.id = 'live-clock';
            clockDiv.style.cssText = `
                position: fixed;
                bottom: 20px;
                left: 20px;
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 15px;
                padding: 15px;
                z-index: 100;
                box-shadow: 0 5px 20px rgba(0,0,0,0.2);
                animation: slideInRight 0.5s ease;
            `;
            document.body.appendChild(clockDiv);
            updateClock();
            setInterval(updateClock, 1000);
        }
    </script>
</body>
</html>