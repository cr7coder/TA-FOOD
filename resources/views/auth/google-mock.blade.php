<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập bằng Google - Giả lập</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f4f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .google-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            border: 1px solid #dadce0;
        }
        .google-logo {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .google-logo img {
            height: 36px;
        }
        .google-title {
            font-size: 24px;
            font-weight: 400;
            color: #202124;
            text-align: center;
            margin-bottom: 8px;
        }
        .google-subtitle {
            font-size: 16px;
            color: #202124;
            text-align: center;
            margin-bottom: 24px;
        }
        .mock-badge {
            background-color: #fef7e0;
            border: 1px solid #fce8b2;
            color: #b06000;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 24px;
            text-align: center;
        }
        .form-floating > .form-control:focus ~ label::after {
            background-color: transparent !important;
        }
        .btn-google-next {
            background-color: #1a73e8;
            color: white;
            font-weight: 500;
            border: none;
            padding: 10px 24px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        .btn-google-next:hover {
            background-color: #1557b0;
            color: white;
        }
        .btn-google-cancel {
            color: #1a73e8;
            font-weight: 500;
            background: none;
            border: none;
            padding: 10px 24px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.2s;
        }
        .btn-google-cancel:hover {
            background-color: rgba(26, 115, 232, 0.04);
            color: #1557b0;
        }
        .terms-text {
            font-size: 12px;
            color: #5f6368;
            margin-top: 30px;
            line-height: 1.5;
        }
    </style>
</head>
<body>

    <div class="google-card">
        <div class="google-logo">
            <img src="https://developers.google.com/static/identity/images/g-logo.png" alt="Google Logo">
        </div>
        <h2 class="google-title">Đăng nhập</h2>
        <div class="google-subtitle">để tiếp tục đến TAFOOD</div>

        <div class="mock-badge">
            <strong>Chế độ giả lập Google Login</strong><br>
            File <code>.env</code> chưa cấu hình <code>GOOGLE_CLIENT_ID</code>. Hệ thống chuyển sang chế độ giả lập để bạn dễ dàng chạy thử nghiệm.
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 0.85rem; border-radius: 4px;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('auth.google.mock.callback') }}" method="POST">
            @csrf
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="floatingEmail" name="email" placeholder="name@example.com" value="test_google@gmail.com" required>
                <label for="floatingEmail">Email của bạn (Google Account)</label>
            </div>

            <div class="form-floating mb-4">
                <input type="text" class="form-control" id="floatingName" name="name" placeholder="Nguyễn Văn A" value="Google User Test" required>
                <label for="floatingName">Họ và tên</label>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('login') }}" class="btn-google-cancel">Hủy</a>
                <button type="submit" class="btn-google-next">Tiếp theo</button>
            </div>
        </form>

        <div class="terms-text">
            Để tiếp tục, Google sẽ chia sẻ tên, địa chỉ email, tùy chọn ngôn ngữ và ảnh hồ sơ của bạn với TAFOOD. Trước khi sử dụng ứng dụng này, bạn có thể xem lại chính sách bảo mật và điều khoản dịch vụ của TAFOOD.
        </div>
    </div>

</body>
</html>
