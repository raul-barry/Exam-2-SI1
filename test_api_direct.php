<?php

require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Simular una solicitud
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$request = Request::create('/api/dashboard', 'GET');
$response = $kernel->handle($request);

echo "Status: " . $response->status() . "\n";
echo "Content:\n";
echo $response->getContent();
?>
