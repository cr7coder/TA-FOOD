<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileAvatarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $mockApiResponse = \Mockery::mock(\Cloudinary\Api\ApiResponse::class);
        $mockApiResponse->shouldReceive('offsetGet')
            ->with('secure_url')
            ->andReturn('https://res.cloudinary.com/drevbj1wg/image/upload/v12345/ta-food/avatars/avatar.jpg');

        $mockUploadApi = \Mockery::mock(\Cloudinary\Api\Upload\UploadApi::class);
        $mockUploadApi->shouldReceive('upload')
            ->andReturn($mockApiResponse);

        \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::shouldReceive('uploadApi')
            ->andReturn($mockUploadApi);
    }

    public function test_user_can_retrieve_profile_with_avatar_fields(): void
    {
        $user = User::factory()->create([
            'HoTen' => 'Nguyen Van A',
            'TenDangNhap' => 'nguyenvana',
            'google_id' => '123456789',
            'AnhDaiDien' => 'https://google.com/avatar.jpg'
        ]);

        $response = $this->actingAs($user)->getJson('/api/v1/profile');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'HoTen' => 'Nguyen Van A',
                    'TenDangNhap' => 'nguyenvana',
                    'google_id' => '123456789',
                    'AnhDaiDien' => 'https://google.com/avatar.jpg',
                    'avatar_url' => 'https://google.com/avatar.jpg'
                ]
            ]);
    }

    public function test_user_can_upload_custom_avatar_image(): void
    {
        $user = User::factory()->create([
            'HoTen' => 'Nguyen Van A',
            'TenDangNhap' => 'nguyenvana',
            'AnhDaiDien' => null
        ]);

        $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);

        // Put request simulated via POST with method spoofing
        $response = $this->actingAs($user)->postJson('/api/v1/profile', [
            '_method' => 'PUT',
            'HoTen' => 'Nguyen Van B',
            'Email' => $user->Email,
            'SoDienThoai' => '0987654321',
            'AnhDaiDien' => $file
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Cập nhật thông tin thành công'
            ]);

        $user->refresh();
        $this->assertNotNull($user->AnhDaiDien);
        $this->assertStringStartsWith('https://res.cloudinary.com', $user->AnhDaiDien);
    }

    public function test_uploading_new_avatar_deletes_old_avatar(): void
    {
        $user = User::factory()->create([
            'HoTen' => 'Nguyen Van A',
            'TenDangNhap' => 'nguyenvana',
            'AnhDaiDien' => 'avatars/old_avatar.jpg'
        ]);

        // Put fake file on the disk
        Storage::disk('public')->put('avatars/old_avatar.jpg', 'fake content');
        Storage::disk('public')->assertExists('avatars/old_avatar.jpg');

        $newFile = UploadedFile::fake()->image('new_avatar.jpg', 100, 100);

        $response = $this->actingAs($user)->postJson('/api/v1/profile', [
            '_method' => 'PUT',
            'HoTen' => 'Nguyen Van A',
            'Email' => $user->Email,
            'AnhDaiDien' => $newFile
        ]);

        $response->assertStatus(200);

        // Old avatar must be deleted from storage
        Storage::disk('public')->assertMissing('avatars/old_avatar.jpg');
        
        $user->refresh();
        $this->assertNotNull($user->AnhDaiDien);
        $this->assertStringStartsWith('https://res.cloudinary.com', $user->AnhDaiDien);
    }
}
