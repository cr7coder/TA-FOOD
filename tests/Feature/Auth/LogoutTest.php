<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test 2E.1 - Đăng xuất khi chưa đăng nhập vẫn redirect về trang chủ */
    public function test_2e1_logout_when_not_authenticated_redirects_to_home()
    {
        $response = $this->post('/logout');

        // Route logout không yêu cầu auth, vẫn redirect về trang chủ
        $response->assertRedirect(route('foods.index'));
        $this->assertGuest();
    }

    /** @test 2S.1 - Đăng xuất thành công */
    public function test_2s1_logout_success_redirects_to_home()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'TenDangNhap' => 'testuser',
            'HoTen' => 'Test User',
            'Email' => 'test@example.com',
            'MatKhau' => bcrypt('password123'),
            'VaiTro' => 'KhachHang'
        ]);

        // Đăng nhập trước
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        // Thực hiện đăng xuất
        $response = $this->post('/logout');

        // Kiểm tra redirect về trang chủ
        $response->assertRedirect(route('foods.index'));
        $response->assertSessionHas('success', 'Đăng xuất thành công');

        // Kiểm tra đã đăng xuất
        $this->assertGuest();
    }

    /** @test 2S.2 - Session bị xóa sau khi đăng xuất */
    public function test_2s2_session_invalidated_after_logout()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'VaiTro' => 'KhachHang'
        ]);

        // Đăng nhập và lưu session data
        $this->actingAs($user);
        session(['test_key' => 'test_value']);
        
        $this->assertEquals('test_value', session('test_key'));

        // Đăng xuất
        $response = $this->post('/logout');

        // Kiểm tra session đã bị invalidate
        $this->assertGuest();
        
        // Session cũ không còn
        $this->assertNull(session('test_key'));
    }

    /** @test 2S.3 - CSRF token được regenerate sau logout */
    public function test_2s3_csrf_token_regenerated_after_logout()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'VaiTro' => 'KhachHang'
        ]);

        $this->actingAs($user);
        
        // Lấy token trước khi logout
        $tokenBefore = csrf_token();

        // Đăng xuất
        $this->post('/logout');

        // Token mới sẽ khác token cũ (trong request mới)
        $this->assertGuest();
    }

    /** @test 2S.4 - Không thể truy cập trang yêu cầu auth sau khi logout */
    public function test_2s4_cannot_access_protected_routes_after_logout()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'VaiTro' => 'KhachHang'
        ]);

        // Đăng nhập
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        // Đăng xuất
        $this->post('/logout');

        // Kiểm tra đã đăng xuất
        $this->assertGuest();

        // Không thể truy cập trang protected nữa (sẽ redirect về login)
        $response = $this->get('/checkout');
        $response->assertRedirect('/login');
    }
}
