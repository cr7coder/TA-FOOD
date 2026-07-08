<?php
require __DIR__ . '/../../bootstrap/app.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DonHang;
use Illuminate\Support\Facades\DB;

$years = DonHang::select(DB::raw('YEAR(created_at) as y'), DB::raw('count(*) as count'))
    ->groupBy('y')
    ->get();

echo "ORDER YEARS IN DATABASE:\n";
foreach ($years as $y) {
    echo "Year: " . $y->y . " - Count: " . $y->count . "\n";
}

$completedYears = DonHang::where('TrangThai', 'Hoàn thành')
    ->select(DB::raw('YEAR(created_at) as y'), DB::raw('count(*) as count'))
    ->groupBy('y')
    ->get();

echo "\nCOMPLETED ORDER YEARS IN DATABASE:\n";
foreach ($completedYears as $y) {
    echo "Year: " . $y->y . " - Count: " . $y->count . "\n";
}
