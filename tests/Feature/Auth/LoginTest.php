<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test 1E.1 - Tên đăng nhập hoặc email không được bỏ trống */
    public function test_1e1_login_field_empty_returns_error()
    {
        $response = $this->postJson('/login', [
            'login' => '',
            'password' => 'password123'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors' => [
                    'login' => ['Vui lòng nhập email hoặc tên đăng nhập']
                ]
            ]);
    }

    /** @test 1E.2 - Tên đăng nhập không tồn tại */
    public function test_1e2_username_not_exists_returns_error()
    {
        $response = $this->postJson('/login', [
            'login' => 'userkhongtontai',
            'password' => 'password123'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Tài khoản không tồn tại trong hệ thống'
            ]);
    }

    /** @test 1E.3 - Mật khẩu không được bỏ trống */
    public function test_1e3_password_empty_returns_error()
    {
        $user = User::factory()->create(['TenDangNhap' => 'testuser']);

        $response = $this->postJson('/login', [
            'login' => 'testuser',
            'password' => ''
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors' => [
                    'password' => ['Vui lòng nhập mật khẩu']
                ]
            ]);
    }

    /** @test 1E.4 - Mật khẩu không đúng */
    public function test_1e4_password_incorrect_returns_error()
    {
        $user = User::factory()->create([
            'TenDangNhap' => 'testuser',
            'MatKhau' => bcrypt('correctpassword')
        ]);

        $response = $this->postJson('/login', [
            'login' => 'testuser',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Mật khẩu không chính xác'
            ]);
    }

    /** @test 1E.5 - Email không tồn tại */
    public function test_1e5_email_not_exists_returns_error()
    {
        $response = $this->postJson('/login', [
            'login' => 'khongtontai@email.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Tài khoản không tồn tại trong hệ thống'
            ]);
    }

    /** @test 1E.6 - Email không được bỏ trống khi chọn đăng nhập bằng email */
    public function test_1e6_email_empty_when_using_email_login_returns_error()
    {
        // Test giống 1E.1 vì logic hiện tại không phân biệt login_type
        $response = $this->postJson('/login', [
            'login' => '',
            'password' => 'password123'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors' => [
                    'login' => ['Vui lòng nhập email hoặc tên đăng nhập']
                ]
            ]);
    }

    /** @test 1S.1 - Đăng nhập thành công với username */
    public function test_1s1_login_success_with_username_returns_success_message()
    {
        $user = User::factory()->create([
            'TenDangNhap' => 'testuser',
            'HoTen' => 'Test User',
            'Email' => 'test@example.com',
            'MatKhau' => bcrypt('password123'),
            'VaiTro' => 'KhachHang'
        ]);

        $response = $this->postJson('/login', [
            'login' => 'testuser',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đăng nhập thành công'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'user' => ['id', 'name', 'email', 'role'],
                'redirect'
            ]);

        $this->assertAuthenticatedAs($user);
    }

    /** @test 1S.2 - Đăng nhập thành công với email */
    public function test_1s2_login_success_with_email_returns_success_message()
    {
        $user = User::factory()->create([
            'TenDangNhap' => 'testuser',
            'HoTen' => 'Test User',
            'Email' => 'test@example.com',
            'MatKhau' => bcrypt('password123'),
            'VaiTro' => 'KhachHang'
        ]);

        $response = $this->postJson('/login', [
            'login' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'user' => [
                    'id' => $user->MaNguoiDung,
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'role' => 'KhachHang'
                ]
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'user' => ['id', 'name', 'email', 'role'],
                'redirect'
            ]);

        $this->assertAuthenticatedAs($user);
    }

    /** @test 1S.3 - Đăng nhập với remember me */
    public function test_1s3_login_with_remember_me()
    {
        $user = User::factory()->create([
            'TenDangNhap' => 'testuser',
            'HoTen' => 'Test User',
            'Email' => 'test@example.com',
            'MatKhau' => bcrypt('password123'),
            'VaiTro' => 'KhachHang'
        ]);

        $response = $this->postJson('/login', [
            'login' => 'testuser',
            'password' => 'password123',
            'remember' => true
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đăng nhập thành công'
            ]);

        $this->assertAuthenticatedAs($user);
    }

    /** @test 1S.4 - Redirect về trang chủ khi đã đăng nhập và truy cập login form */
    public function test_1s4_redirect_to_home_when_already_logged_in()
    {
        $user = User::factory()->create([
            'VaiTro' => 'KhachHang'
        ]);

        $this->actingAs($user);

        $response = $this->get('/login');

        $response->assertRedirect(route('foods.index'));
    }
}
