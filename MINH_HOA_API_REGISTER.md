# 2.1.2 Minh họa thực tế - API Đăng ký

## 2.1.2.1 API

```php
public function register(Request $request)
{
    // Validate input
    $validator = Validator::make($request->all(), [
        'username' => 'required|string|max:50|unique:nguoi_dung,TenDangNhap',
        'password' => 'required|string|min:6|confirmed',
        'name' => 'required|string|max:100',
        'email' => 'required|email|max:100|unique:nguoi_dung,Email',
        'phone' => 'nullable|string|max:15',
        'device_name' => 'nullable|string',
    ]);
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $validator->errors()
        ], 422);
    }
    // Tạo user mới
    $user = User::create([
        'TenDangNhap' => $request->username,
        'MatKhau' => Hash::make($request->password),
        'HoTen' => $request->name,
        'Email' => $request->email,
        'SoDienThoai' => $request->phone,
        'VaiTro' => 'KhachHang',
    ]);
    // Đăng nhập session
    Auth::login($user, true);
    // Tạo API token
    $deviceName = $request->input('device_name', 'api-client');
    $token = $user->createToken($deviceName)->plainTextToken;
    return response()->json([
        'success' => true,
        'message' => 'Đăng ký tài khoản thành công',
        'data' => [
            'user' => [
                'id' => $user->MaNguoiDung,
                'username' => $user->TenDangNhap,
                'name' => $user->HoTen,
                'email' => $user->Email,
                'phone' => $user->SoDienThoai,
                'role' => $user->VaiTro,
            ],
            'token' => $token,
            'token_type' => 'Bearer'
        ]
    ], 201);
}
```

Là API đơn giản được code từ Back End, đoạn API trên có tên là register, được dùng để xử lý đăng ký tài khoản người dùng mới và trả về thông tin user cùng token xác thực.

## 2.1.2.1 Tạo Route API

```php
Route::prefix('v1')->group(callback: function (): void {
    Route::post(uri: '/register', action: 'AuthController@register');
});
```

Tạo router API bằng cách sử dụng thư viện Route có sẵn trong Laravel với đường dẫn và gọi hàm register ở trong controller (AuthController).

## 2.1.2.1 Gọi API từ frontend bằng JavaScript

```javascript
// Handle form submission
registerForm.addEventListener('submit', async function (e) {
    e.preventDefault();
    const submitBtn = this.querySelector('.btn-register');
    const btnText = submitBtn.querySelector('.btn-text');
    const loadingSpinner = submitBtn.querySelector('.loading-spinner');
    submitBtn.disabled = true;
    btnText.style.opacity = '0';
    loadingSpinner.style.display = 'block';
    // Validate client-side trước
    const valid = await validateRegisterForm();
    if (!valid) {
        submitBtn.disabled = false;
        btnText.style.opacity = '1';
        loadingSpinner.style.display = 'none';
        return;
    }
    try {
        // Chuẩn bị data theo RESTful API format (JSON)
        const registerData = {
            username: document.getElementById('username').value,
            password: document.getElementById('password').value,
            password_confirmation: document.getElementById('password_confirmation').value,
            name: document.getElementById('fullname').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            device_name: 'web-browser'
        };
        // Gọi RESTful API endpoint
        const response = await fetch('/api/v1/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(registerData)
        });
        const data = await response.json();
        if (data.success) {
            showAlert('success', data.message);
            // Lưu thông tin user và token vào localStorage
            if (data.data) {
                localStorage.setItem('user', JSON.stringify(data.data.user));
                localStorage.setItem('auth_token', data.data.token);
                localStorage.setItem('token_type', data.data.token_type);
            }
            // Redirect về trang chủ sau 1.5 giây
            setTimeout(() => {
                window.location.href = '/foods';
            }, 1500);
        } else {
            if (data.errors) {
                let errorMessage = '';
                Object.values(data.errors).forEach(errors => {
                    errors.forEach(error => {
                        errorMessage += error + '<br>';
                    });
                });
                showAlert('danger', errorMessage);
            } else {
                showAlert('danger', data.message || 'Có lỗi xảy ra');
            }
            submitBtn.disabled = false;
            btnText.style.opacity = '1';
            loadingSpinner.style.display = 'none';
        }
    } catch (error) {
        console.error('Register error:', error);
        showAlert('danger', 'Có lỗi xảy ra. Vui lòng thử lại!');
        submitBtn.disabled = false;
        btnText.style.opacity = '1';
        loadingSpinner.style.display = 'none';
    }
});
```

Dùng phương thức Fetch API để gọi API bất đồng bộ từ phía Back End.
