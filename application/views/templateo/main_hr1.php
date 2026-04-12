<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم الرئيسية | مرسوم</title>
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

        /* Search Container */
        .search-container {
            position: relative;
            margin-bottom: 30px;
            max-width: 600px;
            margin-right: auto;
            margin-left: auto;
        }

        .search-input {
            width: 100%;
            padding: 15px 25px 15px 55px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            color: white;
            font-size: 16px;
            font-family: 'Tajawal', sans-serif;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .search-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.12);
            border-color: var(--primary-orange);
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.3), 0 0 0 3px rgba(255, 140, 0, 0.1);
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .search-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-orange);
            font-size: 18px;
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

        /* Compact Action Grid */
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
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-details">
                        <strong>نظام إدارة الموارد البشرية</strong>
                        <span class="user-id">مرسوم</span>
                    </div>
                </div>
            </div>
            
            <div class="nav-actions">
                <a href="javascript:history.back()" class="nav-btn" data-aos="fade-left" data-aos-delay="100">
                    <i class="fas fa-arrow-right"></i>
                    <span>رجوع</span>
                </a>
                
                <a href="<?php echo site_url('users1/main_emp'); ?>" class="nav-btn" data-aos="fade-left" data-aos-delay="200">
                    <i class="fas fa-home"></i>
                    <span>الرئيسية</span>
                </a>
            </div>
        </nav>

        <!-- Welcome Card -->
        <div class="welcome-card" data-aos="fade-up" data-aos-duration="1000">
            <div class="welcome-decoration"></div>
            <h1>مرحباً بك في نظام إدارة مرسوم</h1>
            <p>نظام متكامل لإدارة الموارد البشرية والعمليات التشغيلية للمؤسسة</p>
        </div>

        <!-- Search Bar -->
        <div class="search-container" data-aos="fade-up" data-aos-delay="200">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="ابحث عن خدمة، نموذج، أو تقرير...">
        </div>

        <!-- Dashboard Sections -->
        <div class="dashboard-sections">
            <!-- Group 1: إدارة الموظفين -->
            <div class="section-group" data-aos="fade-up" data-aos-delay="100">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h2 class="section-title">إدارة الموظفين</h2>
                    <span class="section-count">15 خدمة</span>
                </div>
                <div class="action-grid">
                    <a href="<?php echo site_url('users1/emp_data101'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="150">
                        <div class="action-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="action-title">الموظفين</h3>
                        <p class="action-desc">إدارة بيانات الموظفين والملفات الشخصية</p>
                        <div class="action-badge">أساسي</div>
                        <div class="action-stat">150 موظف</div>
                    </a>
                    
                    <a href="<?php echo site_url('users1/new_employees_list'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="action-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h3 class="action-title">الموظفين الجدد</h3>
                        <p class="action-desc">إدارة وتتبع الموظفين الجدد في النظام</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/hr_comprehensive_report'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="250">
                        <div class="action-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h3 class="action-title">تقرير شامل</h3>
                        <p class="action-desc">تقارير شاملة للموارد البشرية</p>
                        <div class="action-stat">📊</div>
                    </a>
                    <a href="<?php echo site_url('users1/delegation_report'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="300">
    <div class="action-icon">
        <i class="fas fa-id-badge"></i>
    </div>
    <h3 class="action-title">تقرير التفويضات</h3>
    <p class="action-desc">عرض ومتابعة المهام والطلبات المفوضة للموظفين</p>
    <div class="action-stat">🤝</div>
</a>
                    <a href="<?php echo site_url('users1/attendance_report'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="250">
    <div class="action-icon">
        <i class="fas fa-file-invoice-dollar"></i> </div>
    <h3 class="action-title">تقرير الحضور الشامل</h3>
    <p class="action-desc">تحليل الحضور، التأخير، الإضافي، والغياب</p>
    <div class="action-stat">📊</div>
</a>
                    <a href="<?php echo site_url('users1/employee_requests_report'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="action-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3 class="action-title">طلبات الموظفين</h3>
                        <p class="action-desc">عرض وتتبع طلبات الموظفين المعلقة</p>
                        <div class="action-stat">8 طلبات</div>
                    </a>
                    
                    <a href="<?php echo site_url('users2/manage_branches'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="350">
                        <div class="action-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3 class="action-title">مواقع الفروع</h3>
                        <p class="action-desc">إدارة مواقع وعناوين الفروع</p>
                    </a>
                    
                    <a href="https://services.marsoom.net/recruitment/users/rec_data" class="action-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="action-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3 class="action-title">التوظيف</h3>
                        <p class="action-desc">إدارة عملية التوظيف والمرشحين</p>
                        <div class="action-badge">خارجي</div>
                    </a>
                    
                    <a href="<?php echo site_url('users1/org_pyramid'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="450">
                        <div class="action-icon">
                            <i class="fas fa-sitemap"></i>
                        </div>
                        <h3 class="action-title">الهيكل التنظيمي</h3>
                        <p class="action-desc">عرض الهيكل التنظيمي للمؤسسة</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/org_structure_management'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="500">
                        <div class="action-icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <h3 class="action-title">تحديث الهيكل</h3>
                        <p class="action-desc">تحديث الهيكل التنظيمي</p>
                        <div class="action-badge">مدير</div>
                    </a>

                     <a href="<?php echo site_url('OrgStructureEditor'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="500">
                        <div class="action-icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <h3 class="action-title">تحديث الهيكل   (السحب والافلات) </h3>
                        <p class="action-desc">تحديث الهيكل التنظيمي</p>
                        <div class="action-badge">مدير</div>
                    </a>
                </div>
            </div>

            <!-- Group 2: الانتدابات -->
            <div class="section-group" data-aos="fade-up" data-aos-delay="150">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-plane-departure"></i>
                    </div>
                    <h2 class="section-title">الانتدابات</h2>
                    <span class="section-count">5 خدمات</span>
                </div>
                <div class="action-grid">
                    <a href="<?php echo site_url('users1/mandate_request'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="action-icon">
                            <i class="fas fa-plane-departure"></i>
                        </div>
                        <h3 class="action-title">طلب انتداب جديد</h3>
                        <p class="action-desc">تقديم طلب انتداب جديد للموظفين</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/my_mandates'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="250">
                        <div class="action-icon">
                            <i class="fas fa-list-alt"></i>
                        </div>
                        <h3 class="action-title">سجلي للانتداب</h3>
                        <p class="action-desc">عرض سجل الانتدابات الشخصية</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/mandate_approvals'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="action-icon">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <h3 class="action-title">اعتماد الانتدابات</h3>
                        <p class="action-desc">مراجعة واعتماد طلبات الانتداب</p>
                        <div class="action-stat">3 قيد الانتظار</div>
                    </a>
                    
                    <a href="<?php echo site_url('users1/mandate_settings'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="350">
                        <div class="action-icon">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <h3 class="action-title">إعدادات الانتداب</h3>
                        <p class="action-desc">إعدادات نظام الانتدابات</p>
                        <div class="action-badge">مدير</div>
                    </a>
                    
                    <a href="<?php echo site_url('users1/labor_case_request'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="action-icon">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <h3 class="action-title">قضايا عمالية</h3>
                        <p class="action-desc">إدارة القضايا العمالية</p>
                    </a>
                    <a href="<?php echo site_url('users1/add_violation_note'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="500">
    <div class="action-icon">
        <i class="fas fa-file-circle-exclamation"></i>
    </div>
    <h3 class="action-title">تسجيل ملاحظة</h3>
    <p class="action-desc">إضافة مخالفة أو ملاحظة إدارية</p>
</a>
                </div>
            </div>

            <!-- Group 3: الرواتب والمالية -->
            <div class="section-group" data-aos="fade-up" data-aos-delay="200">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-hand-holding-dollar"></i>
                    </div>
                    <h2 class="section-title">الرواتب والمالية</h2>
                    <span class="section-count">8 خدمات</span>
                </div>
                <div class="action-grid">
                    <a href="<?php echo site_url('users1/main_salary'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="250">
                        <div class="action-icon">
                            <i class="fas fa-hand-holding-dollar"></i>
                        </div>
                        <h3 class="action-title">الرواتب</h3>
                        <p class="action-desc">إدارة وصرف الرواتب الشهرية</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/stop_salary_management'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="action-icon">
                            <i class="fas fa-ban"></i>
                        </div>
                        <h3 class="action-title">إيقاف الرواتب</h3>
                        <p class="action-desc">إدارة إيقاف الرواتب المؤقت</p>
                        <div class="action-badge">خاص</div>
                    </a>
                    
                    <a href="<?php echo site_url('users1/salary_sheets_list'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="350">
                        <div class="action-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h3 class="action-title">مسير الرواتب</h3>
                        <p class="action-desc">أرشيف مسيرات الرواتب الشهرية</p>
                        <div class="action-stat">12 مسير</div>
                    </a>
                     <a href="<?php echo site_url('users1/salary_sheets_list_ramadan'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="350">
                        <div class="action-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                       <h3 class="action-title">مسير الرواتب رمضان</h3>
                        <p class="action-desc">أرشيف مسيرات الرواتب الشهرية</p>
                        <div class="action-stat">12 مسير</div>
                    </a>
                    <a href="<?php echo site_url('users1/payroll_compare'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="action-icon">
                            <i class="fas fa-scale-balanced"></i>
                        </div>
                        <h3 class="action-title">مقارنات الرواتب</h3>
                        <p class="action-desc">مقارنة الرواتب بين الفترات</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/gosi_emp_compare'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="450">
                        <div class="action-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <h3 class="action-title">مقارنات التأمينات</h3>
                        <p class="action-desc">مقارنة بيانات التأمينات الاجتماعية</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/insurance_discounts'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="500">
                        <div class="action-icon">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <h3 class="action-title">خصومات التأمين</h3>
                        <p class="action-desc">إدارة خصومات التأمين الصحي</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/upload_gosi_csv_page'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="550">
                        <div class="action-icon">
                            <i class="fas fa-upload"></i>
                        </div>
                        <h3 class="action-title">تحميل بيانات التأمين</h3>
                        <p class="action-desc">تحميل بيانات التأمين الاجتماعي (CSV)</p>
                        <div class="action-badge">CSV</div>
                    </a>
                    
                    <a href="https://services.marsoom.net/collection/users/productivity_report" class="action-card" data-aos="fade-up" data-aos-delay="600">
                        <div class="action-icon">
                            <i class="fas fa-funnel-dollar"></i>
                        </div>
                        <h3 class="action-title">التحصيل</h3>
                        <p class="action-desc">تقارير إنتاجية التحصيل</p>
                        <div class="action-badge">خارجي</div>
                    </a>

                    <a href="https://services.marsoom.net/collection/EmployeeProductivityReport" class="action-card" data-aos="fade-up" data-aos-delay="600">
                        <div class="action-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="action-title"> التقرير الشامل للتحصيل </h3>
                        <p class="action-desc">تقارير إنتاجية التحصيل</p>
                        <div class="action-badge">خارجي</div>
                    </a>

                     <a href="https://services.marsoom.net/bills11/ExchangeIncentives" class="action-card" data-aos="fade-up" data-aos-delay="600">
                        <div class="action-icon">
                           <i class="fas fa-hand-holding-usd"></i>

                        </div>
                        <h3 class="action-title">   الحوافز والمكافئات   </h3>
                        <p class="action-desc">   طلبات الصرف  </p>
                        <div class="action-badge">خارجي</div>
                    </a>



                </div>
            </div>

            <!-- Group 4: الحضور والإجازات -->
            <div class="section-group" data-aos="fade-up" data-aos-delay="250">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-fingerprint"></i>
                    </div>
                    <h2 class="section-title">الحضور والإجازات</h2>
                    <span class="section-count">6 خدمات</span>
                </div>
                <div class="action-grid">
                    <a href="<?php echo site_url('users1/attendance_overview/'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="action-icon">
                            <i class="fas fa-fingerprint"></i>
                        </div>
                        <h3 class="action-title">لوحة معلومات الحضور</h3>
                        <p class="action-desc">لوحة معلومات شاملة للحضور والانصراف</p>
                        <div class="action-stat">✅ 98%</div>
                    </a>
                    
             <!--       <a href="<?php echo site_url('users1/manual_attendance_edit'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="350">
                        <div class="action-icon">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <h3 class="action-title">تعديل الحضور</h3>
                        <p class="action-desc">تعديل سجلات الحضور يدوياً</p>
                        <div class="action-badge">مدير</div>
                    </a>
                    -->
                    <a href="<?php echo site_url('users1/saturday_work_management'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="action-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <h3 class="action-title">إدارة عمل السبت</h3>
                        <p class="action-desc">إدارة أيام العمل في السبت</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/employee_balances_report'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="450">
                        <div class="action-icon">
                            <i class="fas fa-umbrella-beach"></i>
                        </div>
                        <h3 class="action-title">الإجازات</h3>
                        <p class="action-desc">إدارة طلبات وتقارير الإجازات</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/leave_balances_up'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="500">
                        <div class="action-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <h3 class="action-title">أرصدة الإجازات</h3>
                        <p class="action-desc">عرض وتحديث أرصدة الإجازات</p>
                    </a>
                            <a href="<?php echo site_url('users1/duplicate_punches_report'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="500">
    <div class="action-icon">
        <i class="fas fa-fingerprint"></i>
    </div>
    <h3 class="action-title">تقرير تكرار البصمة</h3>
    <p class="action-desc">كشف وحذف البصمات المكررة</p>
</a>
                    
                    <a href="<?php echo site_url('users1/public_holidays'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="550">
                        <div class="action-icon">
                            <i class="fas fa-umbrella-beach"></i>
                        </div>
                        <h3 class="action-title">العطلات الرسمية</h3>
                        <p class="action-desc">إدارة العطلات الرسمية للمؤسسة</p>
                    </a>
                </div>
            </div>

            <!-- Group 5: نهاية الخدمة -->
            <div class="section-group" data-aos="fade-up" data-aos-delay="300">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-handshake-slash"></i>
                    </div>
                    <h2 class="section-title">نهاية الخدمة</h2>
                    <span class="section-count">7 خدمات</span>
                </div>
                <div class="action-grid">
                    <a href="<?php echo site_url('users1/end_of_service'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="350">
                        <div class="action-icon">
                            <i class="fas fa-handshake-slash"></i>
                        </div>
                        <h3 class="action-title">مستحقات نهاية الخدمة</h3>
                        <p class="action-desc">حساب مستحقات نهاية الخدمة</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/eos_approvals'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="action-icon">
                            <i class="fas fa-check-to-slot"></i>
                        </div>
                        <h3 class="action-title">موافقات ن. الخدمة</h3>
                        <p class="action-desc">اعتماد طلبات نهاية الخدمة</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/clearance_form'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="450">
                        <div class="action-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <h3 class="action-title">إخلاء الطرف</h3>
                        <p class="action-desc">نموذج إخلاء الطرف للموظفين</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/my_clearance_tasks'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="500">
                        <div class="action-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h3 class="action-title">مهام إخلاء الطرف</h3>
                        <p class="action-desc">إدارة مهام إخلاء الطرف الموكلة</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/clearance_parameters_list'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="550">
                        <div class="action-icon">
                            <i class="fas fa-list-check"></i>
                        </div>
                        <h3 class="action-title">إدارة مهام المخالصة</h3>
                        <p class="action-desc">إعدادات مهام إخلاء الطرف</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/resignation_process_report'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="600">
                        <div class="action-icon">
                            <i class="fas fa-person-walking-arrow-right"></i>
                        </div>
                        <h3 class="action-title">طلبات الاستقالة</h3>
                        <p class="action-desc">إدارة وتتبع طلبات الاستقالة</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/list_eos_settlements'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="650">
                        <div class="action-icon">
                            <i class="fas fa-list-check"></i>
                        </div>
                        <h3 class="action-title">قائمة نهاية الخدمة</h3>
                        <p class="action-desc">قائمة كاملة بنهاية الخدمة</p>
                    </a>
                </div>
            </div>

            <!-- Group 6: الطلبات والموافقات -->
            <div class="section-group" data-aos="fade-up" data-aos-delay="350">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h2 class="section-title">الطلبات والموافقات</h2>
                    <span class="section-count">3 خدمات</span>
                </div>
                <div class="action-grid">
                    <a href="<?php echo site_url('users1/orders_emp'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="action-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h3 class="action-title">طلباتي</h3>
                        <p class="action-desc">عرض وتقديم الطلبات الشخصية</p>
                        <div class="action-stat">2 جديد</div>
                    </a>
                    
                    <a href="<?php echo site_url('users1/orders_emp_app'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="450">
                        <div class="action-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3 class="action-title">صندوق الموافقات</h3>
                        <p class="action-desc">مراجعة واعتماد الطلبات الموكلة</p>
                        <div class="action-stat">5 قيد الانتظار</div>
                    </a>
                    
                    <a href="<?php echo site_url('users1/models_emp'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="500">
                        <div class="action-icon">
                            <i class="fas fa-file-signature"></i>
                        </div>
                        <h3 class="action-title">النماذج</h3>
                        <p class="action-desc">النماذج الإلكترونية للمؤسسة</p>
                    </a>
                </div>
            </div>

            <!-- Group 7: التقييم والإعدادات -->
            <div class="section-group" data-aos="fade-up" data-aos-delay="400">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h2 class="section-title">التقييم والإعدادات</h2>
                    <span class="section-count">4 خدمات</span>
                </div>
                <div class="action-grid">
                    <a href="<?php echo site_url('users/employee_evaluation'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="450" target="_blank" rel="noopener noreferrer">
                        <div class="action-icon">
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <h3 class="action-title">التقييم</h3>
                        <p class="action-desc">نظام تقييم أداء الموظفين</p>
                        <div class="action-badge">جديد</div>
                    </a>
                    
                    <a href="<?php echo site_url('users1/series_of_approvals'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="500">
                        <div class="action-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h3 class="action-title">الإعدادات</h3>
                        <p class="action-desc">إعدادات نظام الموارد البشرية</p>
                    </a>
                    
                    <a href="<?php echo site_url('users1/residents'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="550">
                        <div class="action-icon">
                            <i class="fas fa-passport"></i>
                        </div>
                        <h3 class="action-title">المقيمين</h3>
                        <p class="action-desc">إدارة بيانات المقيمين والجنسيات</p>
                    </a>
                    
                    <a href="<?php echo site_url('users2/main1'); ?>" class="action-card" data-aos="fade-up" data-aos-delay="600">
                        <div class="action-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3 class="action-title">تطبيق مرسوم</h3>
                        <p class="action-desc">الوصول إلى تطبيق مرسوم للجوال</p>
                        <div class="action-badge">📱</div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Enhanced Quick Stats -->
        <div class="quick-stats" data-aos="fade-up" data-aos-delay="450">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h4>إجمالي الموظفين</h4>
                    <p>150</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-plane"></i>
                </div>
                <div class="stat-content">
                    <h4>الانتدابات النشطة</h4>
                    <p>3</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h4>طلبات قيد الانتظار</h4>
                    <p>12</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-content">
                    <h4>مهام المخالصة</h4>
                    <p>5</p>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 50,
            easing: 'ease-out-cubic'
        });

        // Search functionality
        $(document).ready(function() {
            $('.search-input').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                
                if (searchTerm.length === 0) {
                    // Show all
                    $('.section-group').show();
                    $('.action-card').show();
                    return;
                }
                
                let foundAny = false;
                
                $('.section-group').each(function() {
                    const section = $(this);
                    const sectionTitle = section.find('.section-title').text().toLowerCase();
                    let sectionHasMatch = false;
                    
                    // Search in action cards within this section
                    section.find('.action-card').each(function() {
                        const card = $(this);
                        const title = card.find('.action-title').text().toLowerCase();
                        const desc = card.find('.action-desc').text().toLowerCase();
                        
                        if (title.includes(searchTerm) || desc.includes(searchTerm)) {
                            card.show();
                            sectionHasMatch = true;
                        } else {
                            card.hide();
                        }
                    });
                    
                    // Show/hide section based on matches
                    if (sectionHasMatch || sectionTitle.includes(searchTerm)) {
                        section.show();
                        foundAny = true;
                    } else {
                        section.hide();
                    }
                });
                
                // If no matches found, show message
                if (!foundAny) {
                    $('.dashboard-sections').after(`
                        <div class="no-results" style="text-align: center; padding: 40px;">
                            <i class="fas fa-search" style="font-size: 3rem; opacity: 0.3; margin-bottom: 20px;"></i>
                            <h3 style="color: var(--text-light); margin-bottom: 10px;">لم يتم العثور على نتائج</h3>
                            <p style="color: var(--text-muted);">جرب استخدام كلمات بحث مختلفة</p>
                        </div>
                    `);
                } else {
                    $('.no-results').remove();
                }
            });

            // Add hover effects to cards
            const cards = document.querySelectorAll('.action-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px) scale(1.02)';
                    this.style.zIndex = '10';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                    this.style.zIndex = '1';
                });
            });

            // Welcome message
            setTimeout(() => {
                showToast(`مرحباً بك في نظام إدارة مرسوم 👋`, 'success');
            }, 1000);

            // Add ripple effect to cards
            cards.forEach(card => {
                card.addEventListener('click', function(e) {
                    const rect = this.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    const ripple = document.createElement('span');
                    ripple.className = 'ripple-effect';
                    ripple.style.cssText = `
                        position: absolute;
                        border-radius: 50%;
                        background: rgba(255, 255, 255, 0.4);
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        width: 100px;
                        height: 100px;
                        left: ${x - 50}px;
                        top: ${y - 50}px;
                        pointer-events: none;
                        z-index: 1;
                    `;
                    
                    this.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 600);
                });
            });

            // Add CSS for ripple animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
                .ripple-effect {
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.3);
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    pointer-events: none;
                }
            `;
            document.head.appendChild(style);
        });

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