<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DonHang;
use Illuminate\Support\Facades\DB;

$months = DonHang::where('TrangThai', 'Hoàn thành')
    ->whereYear('created_at', 2026)
    ->select(DB::raw('MONTH(created_at) as m'), DB::raw('count(*) as count'), DB::raw('SUM(TongTien) as revenue'))
    ->groupBy('m')
    ->get();

echo "COMPLETED ORDERS BY MONTH IN 2026:\n";
foreach ($months as $m) {
    echo "Month: " . $m->m . " - Count: " . $m->count . " - Revenue: " . number_format($m->revenue, 0, ',', '.') . " đ\n";
}
