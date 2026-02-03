<?php

namespace Tests\Feature\Restaurant;

use Tests\TestCase;
use App\Models\User;
use App\Models\NhaHang;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EditSellerTest extends TestCase
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

        $response = $this->putJson(route('seller.restaurant.update'), [
            'TenNhaHang' => str_repeat('A', 101),
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
            'TenNhaHang' => 'Nhà hàng @#$%',
            'DiaChi' => '123 Test Street',
            'SoDienThoai' => '0912345678',
            'GioMoCua' => '08:00',
            'GioDongCua' => '22:00'
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Tên nhà hàng không chứa ký tự đặc biệt']);
    }

    /** @test A14 - Cập nhật thông tin nhà hàng thành công */
    public function test_a14_update_restaurant_success_returns_success_message()
    {
        $this->actingAs($this->seller);

        $response = $this->putJson(route('seller.restaurant.update'), [
            'TenNhaHang' => 'Updated Restaurant',
            'DiaChi' => 'Updated Address',
            'SoDienThoai' => '0987654321',
            'GioMoCua' => '09:00',
            'GioDongCua' => '23:00'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Cập nhật thông tin nhà hàng thành công'
            ]);
    }
}
