<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Khôi phục mật khẩu - TAFOOD</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f5f7;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .header {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            letter-spacing: 1px;
        }
        .content {
            padding: 40px 30px;
            line-height: 1.6;
            font-size: 16px;
        }
        .otp-box {
            background: #f8fafc;
            border: 2px dashed #ffbe33;
            border-radius: 8px;
            text-align: center;
            padding: 20px;
            margin: 30px 0;
            font-size: 36px;
            font-weight: bold;
            color: #e31837;
            letter-spacing: 8px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #888888;
            border-top: 1px solid #eeeeee;
        }
        p {
            margin: 0 0 15px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>TAFOOD</h1>
        </div>
        <div class="content">
            <p>Chào bạn,</p>
            <p>Chúng tôi nhận được yêu cầu khôi phục mật khẩu cho tài khoản TAFOOD của bạn. Vui lòng sử dụng mã OTP dưới đây để tiến hành đặt lại mật khẩu:</p>
            
            <div class="otp-box">
                {{ $otp }}
            </div>
            
            <p>Mã OTP này có hiệu lực trong vòng <strong>5 phút</strong>. Tuyệt đối không chia sẻ mã này cho bất kỳ ai để đảm bảo an toàn tài khoản.</p>
            <p>Nếu bạn không yêu cầu thay đổi mật khẩu, vui lòng bỏ qua email này hoặc liên hệ với chúng tôi nếu nghi ngờ có sự truy cập trái phép.</p>
            
            <p style="margin-top: 30px;">Trân trọng,<br><strong>Đội ngũ TAFOOD</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} TAFOOD. All rights reserved.<br>
            Hotline hỗ trợ: 1900-xxxx
        </div>
    </div>
</body>
</html>
