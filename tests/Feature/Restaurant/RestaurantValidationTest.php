<?php
// tests/Feature/Restaurant/RestaurantValidationTest.php

namespace Tests\Feature\Restaurant;

use Tests\TestCase;
use App\Models\User;
use App\Models\NhaHang;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RestaurantValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $seller;
    protected $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');

        $this->seller = User::factory()->create(['VaiTro' => 'NguoiBan']);
        $this->restaurant = NhaHang::factory()->create([
            'MaNguoiDung' => $this->seller->MaNguoiDung
        ]);
    }

    /** @test A1 - Tên nhà hàng không được bỏ trống */
    public function test_a1_restaurant_name_empty_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->putJson(route('seller.restaurant.update'), [
            'TenNhaHang' => '',
            'DiaChi' => '123 Test Street',
            'SoDienThoai' => '0912345678',
            'GioMoCua' => '08:00',
            'GioDongCua' => '22:00'
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Tên nhà hàng không được bỏ trống']);
    }

    /** @test A2 - Tên nhà hàng không quá 100 ký tự */
    public function test_a2_restaurant_name_exceeds_100_characters_returns_error()
    {
        $this->actingAs($this->seller);

        $longName = str_repeat('A', 101);

        $response = $this->putJson(route('seller.restaurant.update'), [
            'TenNhaHang' => $longName,
            'DiaChi' => '123 Test Street',
            'SoDienThoai' => '0912345678',
            'GioMoCua' => '08:00',
            'GioDongCua' => '22:00'
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Tên nhà hàng không quá 100 ký tự']);
    }

    /** @test A3 - Tên nhà hàng không chứa ký tự đặc biệt */
    public function test_a3_restaurant_name_contains_special_characters_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->putJson(route('seller.restaurant.update'), [
            'TenNhaHang' => 'Nhà hàng @#$%^&*()',
            'DiaChi' => '123 Test Street',
            'SoDienThoai' => '0912345678',
            'GioMoCua' => '08:00',
            'GioDongCua' => '22:00'
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Tên nhà hàng không chứa ký tự đặc biệt']);
    }

    /** @test A4 - Địa chỉ không được bỏ trống */
    public function test_a4_address_empty_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->putJson(route('seller.restaurant.update'), [
            'TenNhaHang' => 'Test Restaurant',
            'DiaChi' => '',
            'SoDienThoai' => '0912345678',
            'GioMoCua' => '08:00',
            'GioDongCua' => '22:00'
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Địa chỉ không được bỏ trống']);
    }

    /** @test A5 - Địa chỉ không quá 200 ký tự */
    public function test_a5_address_exceeds_200_characters_returns_error()
    {
        $this->actingAs($this->seller);

        $longAddress = str_repeat('A', 201);

        $response = $this->putJson(route('seller.restaurant.update'), [
            'TenNhaHang' => 'Test Restaurant',
            'DiaChi' => $longAddress,
            'SoDienThoai' => '0912345678',
            'GioMoCua' => '08:00',
            'GioDongCua' => '22:00'
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Địa chỉ không quá 200 ký tự']);
    }

    /** @test A6 - Số điện thoại không được bỏ trống */
    public function test_a6_phone_empty_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->putJson(route('seller.restaurant.update'), [
            'TenNhaHang' => 'Test Restaurant',
            'DiaChi' => '123 Test Street',
            'SoDienThoai' => '',
            'GioMoCua' => '08:00',
            'GioDongCua' => '22:00'
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Số điện thoại không được bỏ trống']);
    }

    /** @test A7 - Số điện thoại chỉ chấp nhận chữ số */
    public function test_a7_phone_contains_non_digits_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->putJson(route('seller.restaurant.update'), [
            'TenNhaHang' => 'Test Restaurant',
            'DiaChi' => '123 Test Street',
            'SoDienThoai' => '091-234-5678',
            'GioMoCua' => '08:00',
            'GioDongCua' => '22:00'
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Số điện thoại chỉ chấp nhận chữ số']);
    }

    /** @test A8 - Số điện thoại phải có 10–11 chữ số */
    public function test_a8_phone_invalid_length_returns_error()
    {
        $this->actingAs($this->seller);

        // Test với số quá ngắn
        $response = $this->putJson(route('seller.restaurant.update'), [
            'TenNhaHang' => 'Test Restaurant',
            'DiaChi' => '123 Test Street',
            'SoDienThoai' => '091234',
            'GioMoCua' => '08:00',
            'GioDongCua' => '22:00'
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Số điện thoại phải có 10–11 chữ số']);

        // Test với số quá dài
        $response = $this->putJson(route('seller.restaurant.update'), [
            'TenNhaHang' => 'Test Restaurant',
            'DiaChi' => '123 Test Street',
            'SoDienThoai' => '091234567890',
            'GioMoCua' => '08:00',
            'GioDongCua' => '22:00'
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Số điện thoại phải có 10–11 chữ số']);
    }

    /** @test A9 - Số điện thoại phải bắt đầu bằng số 0 */
    public function test_a9_phone_not_start_with_zero_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->putJson(route('seller.restaurant.update'), [
            'TenNhaHang' => 'Test Restaurant',
            'DiaChi' => '123 Test Street',
            'SoDienThoai' => '1912345678',
            'GioMoCua' => '08:00',
            'GioDongCua' => '22:00'
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Số điện thoại phải bắt đầu bằng số 0']);
    }

    /** @test A10 - Giờ mở cửa không được bỏ trống */
    public function test_a10_opening_time_empty_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->putJson(route('seller.restaurant.update'), [
            'TenNhaHang' => 'Test Restaurant',
            'DiaChi' => '123 Test Street',
            'SoDienThoai' => '0912345678',
            'GioMoCua' => '',
            'GioDongCua' => '22:00'
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Giờ mở cửa không được bỏ trống']);
    }

    /** @test A11 - Giờ mở cửa không hợp lệ */
    public function test_a11_opening_time_invalid_format_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->putJson(route('seller.restaurant.update'), [
            'TenNhaHang' => 'Test Restaurant',
            'DiaChi' => '123 Test Street',
            'SoDienThoai' => '0912345678',
            'GioMoCua' => '25:00',
            'GioDongCua' => '22:00'
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Giờ mở cửa không hợp lệ']);
    }

    /** @test A12 - Giờ đóng cửa không được bỏ trống */
    public function test_a12_closing_time_empty_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->putJson(route('seller.restaurant.update'), [
            'TenNhaHang' => 'Test Restaurant',
            'DiaChi' => '123 Test Street',
            'SoDienThoai' => '0912345678',
            'GioMoCua' => '08:00',
            'GioDongCua' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Giờ đóngcửa không được bỏ trống']);
    }

    /** @test A13 - Giờ đóng cửa không hợp lệ */
    public function test_a13_closing_time_invalid_format_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->putJson(route('seller.restaurant.update'), [
            'TenNhaHang' => 'Test Restaurant',
            'DiaChi' => '123 Test Street',
            'SoDienThoai' => '0912345678',
            'GioMoCua' => '08:00',
            'GioDongCua' => '99:99'
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Giờ đóngcửa không hợp lệ']);
    }

    /** @test A14 - Cập nhật thông tin nhà hàng thành công */
    public function test_a14_update_restaurant_success_returns_success_message()
    {
        $this->actingAs($this->seller);

        $response = $this->putJson(route('seller.restaurant.update'), [
            'TenNhaHang' => 'Updated Restaurant Name',
            'DiaChi' => '456 New Address',
            'SoDienThoai' => '0987654321',
            'GioMoCua' => '09:00',
            'GioDongCua' => '23:00'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Cập nhật thông tin nhà hàng thành công'
            ]);

        $this->assertDatabaseHas('nha_hang', [
            'MaNguoiDung' => $this->seller->MaNguoiDung,
            'TenNhaHang' => 'Updated Restaurant Name',
            'DiaChi' => '456 New Address',
            'SoDienThoai' => '0987654321'
        ]);
    }
}
