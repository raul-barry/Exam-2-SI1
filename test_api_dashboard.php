<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';

// Make the app
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;

try {
    echo "=== TESTING DASHBOARD API ===\n\n";
    
    // Create a request
    $request = Request::create('/api/dashboard', 'GET', [], [], [], [
        'HTTP_AUTHORIZATION' => 'Bearer test_token',
        'HTTP_ACCEPT' => 'application/json'
    ]);
    
    // Handle the request
    $response = $kernel->handle($request);
    
    echo "Status: " . $response->status() . "\n";
    echo "Content:\n";
    echo $response->getContent() . "\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " at line " . $e->getLine() . "\n";
}
?>
