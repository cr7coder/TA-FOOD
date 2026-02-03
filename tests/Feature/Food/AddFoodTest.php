<?php

namespace Tests\Feature\Food;

use Tests\TestCase;
use App\Models\User;
use App\Models\NhaHang;
use App\Models\MonAn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AddFoodTest extends TestCase
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

        Storage::fake('public');
    }

    /** @test 5E.1 - Tên món ăn không được bỏ trống */
    public function test_5e1_food_name_empty_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->postJson(route('seller.foods.store'), [
            'TenMonAn' => '',
            'DanhMuc' => 'Cơm',
            'Gia' => 50000,
            'MoTa' => 'Mô tả món ăn',
            'TrangThai' => 'Còn bán',
            'HinhAnh' => UploadedFile::fake()->image('food.jpg')
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Tên món ăn không được bỏ trống (5E.1)']);
    }

    /** @test 5E.2 - Tên món ăn không quá 100 ký tự */
    public function test_5e2_food_name_exceeds_100_characters_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->postJson(route('seller.foods.store'), [
            'TenMonAn' => str_repeat('A', 101),
            'DanhMuc' => 'Cơm',
            'Gia' => 50000,
            'MoTa' => 'Mô tả món ăn',
            'TrangThai' => 'Còn bán',
            'HinhAnh' => UploadedFile::fake()->image('food.jpg')
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Tên món ăn không quá 100 ký tự (5E.2)']);
    }

    /** @test 5E.3 - Tên món ăn không chứa ký tự đặc biệt */
    public function test_5e3_food_name_contains_special_characters_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->postJson(route('seller.foods.store'), [
            'TenMonAn' => 'Cơm Gà @#$%',
            'DanhMuc' => 'Cơm',
            'Gia' => 50000,
            'MoTa' => 'Mô tả món ăn',
            'TrangThai' => 'Còn bán',
            'HinhAnh' => UploadedFile::fake()->image('food.jpg')
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Tên món ăn không chứa ký tự đặc biệt (5E.3)']);
    }

    /** @test 5E.4 - Tên món ăn đã tồn tại */
    public function test_5e4_food_name_already_exists_returns_error()
    {
        $this->actingAs($this->seller);

        MonAn::factory()->create([
            'TenMonAn' => 'Cơm Gà Existing',
            'MaNhaHang' => $this->restaurant->MaNhaHang
        ]);

        $response = $this->postJson(route('seller.foods.store'), [
            'TenMonAn' => 'Cơm Gà Existing',
            'DanhMuc' => 'Cơm',
            'Gia' => 50000,
            'MoTa' => 'Mô tả món ăn',
            'TrangThai' => 'Còn bán',
            'HinhAnh' => UploadedFile::fake()->image('food.jpg')
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Tên món ăn đã tồn tại (5E.4)']);
    }

    /** @test 5E.5 - Giá không được bỏ trống */
    public function test_5e5_price_empty_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->postJson(route('seller.foods.store'), [
            'TenMonAn' => 'Test Food',
            'DanhMuc' => 'Cơm',
            'Gia' => '',
            'MoTa' => 'Mô tả món ăn',
            'TrangThai' => 'Còn bán',
            'HinhAnh' => UploadedFile::fake()->image('food.jpg')
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Giá không được bỏ trống (5E.5)']);
    }

    /** @test 5E.6 - Giá phải lớn hơn 0 */
    public function test_5e6_price_must_be_greater_than_zero_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->postJson(route('seller.foods.store'), [
            'TenMonAn' => 'Test Food',
            'DanhMuc' => 'Cơm',
            'Gia' => 0,
            'MoTa' => 'Mô tả món ăn',
            'TrangThai' => 'Còn bán',
            'HinhAnh' => UploadedFile::fake()->image('food.jpg')
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Giá phải lớn hơn 0 (5E.6)']);
    }

    /** @test 5E.7 - Giá phải là số */
    public function test_5e7_price_must_be_numeric_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->postJson(route('seller.foods.store'), [
            'TenMonAn' => 'Test Food',
            'DanhMuc' => 'Cơm',
            'Gia' => 'not-a-number',
            'MoTa' => 'Mô tả món ăn',
            'TrangThai' => 'Còn bán',
            'HinhAnh' => UploadedFile::fake()->image('food.jpg')
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Giá phải là số (5E.7)']);
    }

    /** @test 5E.8 - Hình ảnh không được bỏ trống */
    public function test_5e8_image_empty_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->postJson(route('seller.foods.store'), [
            'TenMonAn' => 'Test Food',
            'DanhMuc' => 'Cơm',
            'Gia' => 50000,
            'MoTa' => 'Mô tả món ăn',
            'TrangThai' => 'Còn bán'
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Hình ảnh không được bỏ trống (5E.8)']);
    }

    /** @test 5E.9 - Định dạng hình ảnh không hợp lệ */
    public function test_5e9_image_invalid_format_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->postJson(route('seller.foods.store'), [
            'TenMonAn' => 'Test Food',
            'DanhMuc' => 'Cơm',
            'Gia' => 50000,
            'MoTa' => 'Mô tả món ăn',
            'TrangThai' => 'Còn bán',
            'HinhAnh' => UploadedFile::fake()->create('document.pdf', 1000)
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Định dạng hình ảnh không hợp lệ (5E.9)']);
    }

    /** @test 5E.10 - Kích thước hình ảnh quá lớn */
    public function test_5e10_image_size_too_large_returns_error()
    {
        $this->actingAs($this->seller);

        $response = $this->postJson(route('seller.foods.store'), [
            'TenMonAn' => 'Test Food',
            'DanhMuc' => 'Cơm',
            'Gia' => 50000,
            'MoTa' => 'Mô tả món ăn',
            'TrangThai' => 'Còn bán',
            'HinhAnh' => UploadedFile::fake()->create('large.jpg', 6000) // 6MB
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Kích thước hình ảnh quá lớn (5E.10)']);
    }

    /** @test 5S.1 - Thêm món ăn thành công */
    public function test_5s1_add_food_success_returns_success_message()
    {
        $this->actingAs($this->seller);

        $response = $this->postJson(route('seller.foods.store'), [
            'TenMonAn' => 'Cơm Gà Hải Nam',
            'DanhMuc' => 'Cơm',
            'Gia' => 45000,
            'MoTa' => 'Cơm gà thơm ngon',
            'TrangThai' => 'Còn bán',
            'HinhAnh' => UploadedFile::fake()->image('food.jpg')
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Thêm món ăn thành công!'
            ]);

        $this->assertDatabaseHas('mon_an', [
            'TenMonAn' => 'Cơm Gà Hải Nam',
            'DanhMuc' => 'Cơm',
            'Gia' => 45000,
            'MaNhaHang' => $this->restaurant->MaNhaHang
        ]);
    }
}
