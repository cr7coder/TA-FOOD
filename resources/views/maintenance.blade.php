<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Đang Bảo Trì - TAFOOD</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background: radial-gradient(circle at 50% 50%, #2b1f1d 0%, #110e0d 100%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
            position: relative;
        }

        /* Decorative glowing circles */
        .glow-circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(150px);
            z-index: 1;
            opacity: 0.15;
        }

        .glow-orange {
            width: 400px;
            height: 400px;
            background: #ffbe33;
            top: -100px;
            left: -100px;
            animation: pulse-glow 8s infinite alternate;
        }

        .glow-yellow {
            width: 300px;
            height: 300px;
            background: #ff9800;
            bottom: -50px;
            right: -50px;
            animation: pulse-glow 6s infinite alternate-reverse;
        }

        @keyframes pulse-glow {
            0% { transform: scale(1); opacity: 0.1; }
            100% { transform: scale(1.2); opacity: 0.2; }
        }

        .maintenance-container {
            position: relative;
            z-index: 2;
            max-width: 600px;
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px;
            padding: 50px 40px;
            text-align: center;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
            animation: slide-up 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slide-up {
            from { transform: translateY(40px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .icon-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 30px;
        }

        .icon-bg {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(255,190,51,0.2) 0%, rgba(255,152,0,0.2) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            border: 1px solid rgba(255, 190, 51, 0.3);
            animation: pulse-border 2s infinite;
        }

        .icon-bg i {
            font-size: 42px;
            color: #ffbe33;
            animation: rotate-gear 12s infinite linear;
        }

        @keyframes rotate-gear {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes pulse-border {
            0% { box-shadow: 0 0 0 0 rgba(255, 190, 51, 0.4); }
            70% { box-shadow: 0 0 0 20px rgba(255, 190, 51, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 190, 51, 0); }
        }

        h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
            background: linear-gradient(to right, #ffffff 0%, #ffe4a0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        p {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
            margin-bottom: 35px;
            font-weight: 300;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .info-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.04);
            border-radius: 16px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .info-card:hover {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 190, 51, 0.2);
            transform: translateY(-3px);
        }

        .info-card i {
            font-size: 20px;
            color: #ffbe33;
            margin-bottom: 10px;
            display: block;
        }

        .info-card strong {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 4px;
        }

        .info-card span {
            font-size: 15px;
            color: #ffffff;
            font-weight: 600;
        }

        .brand-footer {
            margin-top: 40px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.3);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        @media (max-width: 576px) {
            .maintenance-container {
                padding: 40px 25px;
            }
            h1 { font-size: 26px; }
            p { font-size: 14px; }
            .info-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Background Glows -->
    <div class="glow-circle glow-orange"></div>
    <div class="glow-circle glow-yellow"></div>

    <!-- Main Container -->
    <div class="maintenance-container">
        <!-- Icon -->
        <div class="icon-wrapper">
            <div class="icon-bg">
                <i class="fa fa-cog"></i>
            </div>
        </div>

        <!-- Heading -->
        <h1>Hệ Thống Đang Nâng Cấp</h1>
        
        <!-- Text -->
        <p>Chúng tôi đang thực hiện một số cải tiến định kỳ để mang lại trải nghiệm dịch vụ ẩm thực tốt nhất và nhanh nhất cho bạn. Hệ thống sẽ trở lại hoạt động bình thường trong giây lát. Rất mong quý khách thông cảm vì sự bất tiện này!</p>

        <!-- Dynamic Info Grid -->
        <div class="info-grid">
            <div class="info-card">
                <i class="fa fa-phone-alt"></i>
                <strong>Hotline Hỗ Trợ</strong>
                <span>{{ App\Services\SettingService::get('hotline', '1900-8888') }}</span>
            </div>
            <div class="info-card">
                <i class="fa fa-envelope"></i>
                <strong>Email Hỗ Trợ</strong>
                <span>{{ App\Services\SettingService::get('support_email', 'support@cabafood.vn') }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="brand-footer">
            &copy; 2026 TAFOOD Premium
        </div>
    </div>
</body>
</html>
