<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Pengaturan\LogAktivitas;

try {
    echo "Laravel DB connection check...\n";
    $count = LogAktivitas::count();
    echo "Total LogAktivitas: " . $count . "\n";
    
    echo "Latest 5 logs:\n";
    $logs = LogAktivitas::latest()->take(5)->get();
    foreach ($logs as $log) {
        echo "- ID: {$log->id}, Action: {$log->action}, Created: {$log->created_at}, Desc: {$log->description}\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
