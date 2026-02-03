<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test 4E.1 - Tên đăng nhập không được bỏ trống */
    public function test_4e1_username_empty_returns_error()
    {
        $response = $this->postJson('/register', [
            'username' => '',
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '0912345678'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors' => [
                    'username' => ['Vui lòng nhập tên đăng nhập']
                ]
            ]);
    }

    /** @test 4E.2 - Tên đăng nhập đã tồn tại */
    public function test_4e2_username_already_exists_returns_error()
    {
        /** @var User $user */
        $user = User::factory()->create(['TenDangNhap' => 'existinguser']);

        $response = $this->postJson('/register', [
            'username' => 'existinguser',
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '0912345678'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false
            ])
            ->assertJsonPath('errors.username', function($messages) {
                return in_array('The username has already been taken.', $messages);
            });
    }

    /** @test 4E.3 - Họ tên không được bỏ trống */
    public function test_4e3_fullname_empty_returns_error()
    {
        $response = $this->postJson('/register', [
            'username' => 'testuser',
            'fullname' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '0912345678'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors' => [
                    'fullname' => ['Vui lòng nhập họ và tên']
                ]
            ]);
    }

    /** @test 4E.4 - Email không được bỏ trống */
    public function test_4e4_email_empty_returns_error()
    {
        $response = $this->postJson('/register', [
            'username' => 'testuser',
            'fullname' => 'Test User',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '0912345678'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors' => [
                    'email' => ['Vui lòng nhập email']
                ]
            ]);
    }

    /** @test 4E.5 - Email chưa hợp lệ */
    public function test_4e5_email_invalid_format_returns_error()
    {
        $response = $this->postJson('/register', [
            'username' => 'testuser',
            'fullname' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '0912345678'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors' => [
                    'email' => ['Email không hợp lệ']
                ]
            ]);
    }

    /** @test 4E.6 - Email đã tồn tại */
    public function test_4e6_email_already_exists_returns_error()
    {
        /** @var User $user */
        $user = User::factory()->create(['Email' => 'existing@example.com']);

        $response = $this->postJson('/register', [
            'username' => 'testuser',
            'fullname' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '0912345678'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors' => [
                    'email' => ['Email đã tồn tại']
                ]
            ]);
    }

    /** @test 4E.7 - Mật khẩu không được bỏ trống */
    public function test_4e7_password_empty_returns_error()
    {
        $response = $this->postJson('/register', [
            'username' => 'testuser',
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
            'phone' => '0912345678'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors' => [
                    'password' => ['Vui lòng nhập mật khẩu']
                ]
            ]);
    }

    /** @test 4E.8 - Mật khẩu tối thiểu 6 ký tự */
    public function test_4e8_password_too_short_returns_error()
    {
        $response = $this->postJson('/register', [
            'username' => 'testuser',
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123',
            'password_confirmation' => '123',
            'phone' => '0912345678'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors' => [
                    'password' => ['Mật khẩu tối thiểu 6 ký tự']
                ]
            ]);
    }

    /** @test 4E.9 - Xác nhận mật khẩu không khớp */
    public function test_4e9_password_confirmation_not_match_returns_error()
    {
        $response = $this->postJson('/register', [
            'username' => 'testuser',
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
            'phone' => '0912345678'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors' => [
                    'password' => ['Xác nhận mật khẩu không khớp']
                ]
            ]);
    }

    /** @test 4S.1 - Đăng ký thành công */
    public function test_4s1_register_success_returns_success_message()
    {
        $response = $this->postJson('/register', [
            'username' => 'newuser',
            'fullname' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '0912345678'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đăng ký thành công'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'user' => ['id', 'name', 'email', 'role'],
                'redirect'
            ]);

        $this->assertDatabaseHas('nguoi_dung', [
            'TenDangNhap' => 'newuser',
            'Email' => 'newuser@example.com',
            'VaiTro' => 'KhachHang'
        ]);
    }
}
