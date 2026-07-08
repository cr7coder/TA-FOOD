<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Api\Admin\DashboardController;
use Illuminate\Http\Request;
use App\Models\User;

// Authenticate as admin user3 (Lê Văn C)
$admin = User::where('TenDangNhap', 'user3')->first();
if (!$admin) {
    echo "Admin user not found.\n";
    exit(1);
}
auth()->login($admin);

$controller = new DashboardController();
$request = Request::create('/api/v1/admin/dashboard', 'GET');
$response = $controller->index($request);

echo "STATUS: " . $response->getStatusCode() . "\n";
echo "RESPONSE:\n";
echo json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
