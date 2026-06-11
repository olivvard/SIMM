<?php

use App\Models\MaintenanceLog;
use Illuminate\Support\Facades\View;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$logs = MaintenanceLog::with(['motor', 'schedule', 'admin', 'activityDetails.activity'])->get();
$month = now()->format('Y-m');
$motorLabel = 'All Motors';

$html = View::make('reports.pdf', compact('logs', 'month', 'motorLabel'))->render();
file_put_contents(__DIR__ . '/pdf_preview.html', $html);

echo "Preview generated successfully at pdf_preview.html\n";
