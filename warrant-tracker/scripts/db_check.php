<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Laravel default connection: " . config('database.default') . PHP_EOL;
$cfg = config('database.connections.' . config('database.default'));
echo "Connection config: " . json_encode($cfg) . PHP_EOL . PHP_EOL;

try {
    $tables = Illuminate\Support\Facades\DB::select("SELECT tablename FROM pg_tables WHERE schemaname = current_schema();");
    echo "Tables in current schema:\n";
    foreach ($tables as $t) {
        echo $t->tablename . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Query error: ' . $e->getMessage() . PHP_EOL;
}
