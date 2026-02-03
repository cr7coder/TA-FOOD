<?php

namespace Tests\Feature\Food;

use Tests\TestCase;
use App\Models\User;
use App\Models\NhaHang;
use App\Models\MonAn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EditFoodTest extends TestCase
{
    use RefreshDatabase;

    protected $seller;
    protected $restaurant;
    protected $food;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');

        $this->seller = User::factory()->create(['VaiTro' => 'NguoiBan']);
        $this->restaurant = NhaHang::factory()->create([
            'MaNguoiDung' => $this->seller->MaNguoiDung
        ]);
        $this->food = MonAn::factory()->create([
            'MaNhaHang' => $this->restaurant->MaNhaHang
        ]);

        Storage::fake('public');
    }

    /** @test 6E.1 - Món ăn không tồn tại */
    public function test_6e1_food_not_exists_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->putJson(route('seller.foods.update', 99999), [
            'TenMonAn' => 'Updated Food',
            'DanhMuc' => 'Cơm',
            'Gia' => 50000
        ]);

        $response->assertStatus(404)
            ->assertJsonFragment(['Món ăn không tồn tại (6E.1)']);
    }

    /** @test 6E.2 - Không có quyền sửa món ăn này */
    public function test_6e2_no_permission_to_edit_food_returns_error()
    {
        $otherSeller = User::factory()->create(['VaiTro' => 'NguoiBan']);
        $otherRestaurant = NhaHang::factory()->create(['MaNguoiDung' => $otherSeller->MaNguoiDung]);
        $otherFood = MonAn::factory()->create(['MaNhaHang' => $otherRestaurant->MaNhaHang]);

        $this->actingAs($this->seller);

        $response = $this->putJson(route('seller.foods.update', $otherFood->MaMonAn), [
            'TenMonAn' => 'Updated Food',
            'DanhMuc' => 'Cơm',
            'Gia' => 50000
        ]);

        $response->assertStatus(403)
            ->assertJsonFragment(['Không có quyền sửa món ăn này (6E.2)']);
    }

    /** @test 6E.3 - Tên món ăn không được bỏ trống */
    public function test_6e3_food_name_empty_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->putJson(route('seller.foods.update', $this->food->MaMonAn), [
            'TenMonAn' => '',
            'DanhMuc' => 'Cơm',
            'Gia' => 50000
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Tên món ăn không được bỏ trống (6E.3)']);
    }

    /** @test 6E.4 - Tên món ăn đã tồn tại (trừ chính nó) */
    public function test_6e4_food_name_already_exists_except_itself_returns_error()
    {
        $this->actingAs($this->seller);

        $existingFood = MonAn::factory()->create([
            'TenMonAn' => 'Existing Food',
            'MaNhaHang' => $this->restaurant->MaNhaHang
        ]);

        $response = $this->putJson(route('seller.foods.update', $this->food->MaMonAn), [
            'TenMonAn' => 'Existing Food',
            'DanhMuc' => 'Cơm',
            'Gia' => 50000
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Tên món ăn đã tồn tại (6E.4)']);
    }

    /** @test 6E.5 - Giá phải lớn hơn 0 */
    public function test_6e5_price_must_be_greater_than_zero_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->putJson(route('seller.foods.update', $this->food->MaMonAn), [
            'TenMonAn' => 'Updated Food',
            'DanhMuc' => 'Cơm',
            'Gia' => 0
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Giá phải lớn hơn 0 (6E.5)']);
    }

    /** @test 6E.6 - Không có thông tin nào được cập nhật */
    public function test_6e6_no_data_updated_returns_warning()
    {
        $this->actingAs($this->seller);

        $response = $this->putJson(route('seller.foods.update', $this->food->MaMonAn), [
            'TenMonAn' => $this->food->TenMonAn,
            'DanhMuc' => $this->food->DanhMuc,
            'Gia' => $this->food->Gia,
            'MoTa' => $this->food->MoTa,
            'TrangThai' => $this->food->TrangThai
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Không có thông tin nào được cập nhật (6E.6)']);
    }

    /** @test 6S.1 - Sửa món ăn thành công */
    public function test_6s1_edit_food_success_returns_success_message()
    {
        $this->actingAs($this->seller);

        $response = $this->putJson(route('seller.foods.update', $this->food->MaMonAn), [
            'TenMonAn' => 'Updated Food Name',
            'DanhMuc' => 'Nước uống',
            'Gia' => 75000,
            'MoTa' => 'Updated description',
            'TrangThai' => 'Hết hàng'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Sửa món ăn thành công!'
            ]);

        $this->assertDatabaseHas('mon_an', [
            'MaMonAn' => $this->food->MaMonAn,
            'TenMonAn' => 'Updated Food Name',
            'DanhMuc' => 'Nước uống',
            'Gia' => 75000
        ]);
    }
}
