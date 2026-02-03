<?php

namespace Tests\Feature\Voucher;

use Tests\TestCase;
use App\Models\User;
use App\Models\GiamGia;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoucherTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');

        $this->admin = User::factory()->create(['VaiTro' => 'Admin']);
    }

    /** @test 9E.1 - Mã voucher không được bỏ trống */
    public function test_9e1_voucher_code_empty_returns_error()
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.vouchers.store'), [
            'MaCode' => '',
            'TenVoucher' => 'Voucher test',
            'LoaiGiam' => 'percent',
            'GiaTriGiam' => 10,
            'GiaTriDonHangToiThieu' => 100000,
            'NgayBatDau' => now()->format('Y-m-d'),
            'NgayKetThuc' => now()->addDays(7)->format('Y-m-d'),
            'SoLuongToiDa' => 100
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Mã voucher không được bỏ trống (9E.1)']);
    }

    /** @test 9E.2 - Mã voucher đã tồn tại */
    public function test_9e2_voucher_code_already_exists_returns_error()
    {
        $this->actingAs($this->admin);

        GiamGia::factory()->create(['MaCode' => 'EXISTING_CODE']);

        $response = $this->postJson(route('admin.vouchers.store'), [
            'MaCode' => 'EXISTING_CODE',
            'TenVoucher' => 'Voucher test',
            'LoaiGiam' => 'percent',
            'GiaTriGiam' => 10,
            'GiaTriDonHangToiThieu' => 100000,
            'NgayBatDau' => now()->format('Y-m-d'),
            'NgayKetThuc' => now()->addDays(7)->format('Y-m-d'),
            'SoLuongToiDa' => 100
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Mã voucher đã tồn tại (9E.2)']);
    }

    /** @test 9E.3 - Tên voucher không được bỏ trống */
    public function test_9e3_voucher_name_empty_returns_error()
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.vouchers.store'), [
            'MaCode' => 'TEST_CODE',
            'TenVoucher' => '',
            'LoaiGiam' => 'percent',
            'GiaTriGiam' => 10,
            'GiaTriDonHangToiThieu' => 100000,
            'NgayBatDau' => now()->format('Y-m-d'),
            'NgayKetThuc' => now()->addDays(7)->format('Y-m-d'),
            'SoLuongToiDa' => 100
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Tên voucher không được bỏ trống (9E.3)']);
    }

    /** @test 9E.4 - Giá trị giảm phải lớn hơn 0 */
    public function test_9e4_discount_value_must_be_greater_than_zero_returns_error()
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.vouchers.store'), [
            'MaCode' => 'TEST_CODE',
            'TenVoucher' => 'Voucher test',
            'LoaiGiam' => 'percent',
            'GiaTriGiam' => 0,
            'GiaTriDonHangToiThieu' => 100000,
            'NgayBatDau' => now()->format('Y-m-d'),
            'NgayKetThuc' => now()->addDays(7)->format('Y-m-d'),
            'SoLuongToiDa' => 100
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Giá trị giảm phải lớn hơn 0 (9E.4)']);
    }

    /** @test 9E.5 - Phần trăm giảm không được vượt quá 100% */
    public function test_9e5_percent_discount_cannot_exceed_100_returns_error()
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.vouchers.store'), [
            'MaCode' => 'TEST_CODE',
            'TenVoucher' => 'Voucher test',
            'LoaiGiam' => 'percent',
            'GiaTriGiam' => 150,
            'GiaTriDonHangToiThieu' => 100000,
            'NgayBatDau' => now()->format('Y-m-d'),
            'NgayKetThuc' => now()->addDays(7)->format('Y-m-d'),
            'SoLuongToiDa' => 100
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Phần trăm giảm không được vượt quá 100% (9E.5)']);
    }

    /** @test 9E.6 - Ngày kết thúc phải sau ngày bắt đầu */
    public function test_9e6_end_date_must_be_after_start_date_returns_error()
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.vouchers.store'), [
            'MaCode' => 'TEST_CODE',
            'TenVoucher' => 'Voucher test',
            'LoaiGiam' => 'percent',
            'GiaTriGiam' => 10,
            'GiaTriDonHangToiThieu' => 100000,
            'NgayBatDau' => now()->addDays(7)->format('Y-m-d'),
            'NgayKetThuc' => now()->format('Y-m-d'),
            'SoLuongToiDa' => 100
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Ngày kết thúc phải sau ngày bắt đầu (9E.6)']);
    }

    /** @test 9E.7 - Số lượng phải lớn hơn 0 */
    public function test_9e7_quantity_must_be_greater_than_zero_returns_error()
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.vouchers.store'), [
            'MaCode' => 'TEST_CODE',
            'TenVoucher' => 'Voucher test',
            'LoaiGiam' => 'percent',
            'GiaTriGiam' => 10,
            'GiaTriDonHangToiThieu' => 100000,
            'NgayBatDau' => now()->format('Y-m-d'),
            'NgayKetThuc' => now()->addDays(7)->format('Y-m-d'),
            'SoLuongToiDa' => 0
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Số lượng phải lớn hơn 0 (9E.7)']);
    }

    /** @test 9S.1 - Thêm voucher thành công */
    public function test_9s1_create_voucher_success_returns_success_message()
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.vouchers.store'), [
            'MaCode' => 'NEW_VOUCHER',
            'TenVoucher' => 'Voucher giảm giá 10%',
            'LoaiGiam' => 'percent',
            'GiaTriGiam' => 10,
            'GiaTriDonHangToiThieu' => 100000,
            'NgayBatDau' => now()->format('Y-m-d'),
            'NgayKetThuc' => now()->addDays(7)->format('Y-m-d'),
            'SoLuongToiDa' => 100,
            'MoTa' => 'Voucher dành cho khách hàng mới'
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Thêm voucher thành công!'
            ]);

        $this->assertDatabaseHas('giam_gia', [
            'MaCode' => 'NEW_VOUCHER',
            'TenVoucher' => 'Voucher giảm giá 10%',
            'LoaiGiam' => 'percent',
            'GiaTriGiam' => 10
        ]);
    }
}
