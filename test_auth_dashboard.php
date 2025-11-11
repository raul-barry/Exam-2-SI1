<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';

// Make the app
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Usuario;
use Illuminate\Http\Request;

try {
    echo "=== TESTING AUTHENTICATED DASHBOARD API ===\n\n";
    
    // Get first user
    $user = Usuario::with(['rol', 'persona'])->first();
    if (!$user) {
        echo "❌ No users found in database\n";
        exit(1);
    }
    
    echo "✓ Testing with user: " . $user->ci_persona . " (Rol: " . ($user->rol->nombre ?? 'N/A') . ")\n";
    
    // Create a token for the user
    $token = $user->createToken('test-token')->plainTextToken;
    echo "✓ Generated token\n";
    
    // Simulate a request to the dashboard
    $request = Request::create('/api/dashboard', 'GET', [], [], [], [
        'HTTP_AUTHORIZATION' => "Bearer {$token}",
        'HTTP_ACCEPT' => 'application/json'
    ]);
    
    // Get the HTTP kernel
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    
    // Handle the request
    $response = $kernel->handle($request);
    
    echo "\n✓ Status: " . $response->status() . "\n";
    echo "✓ Content:\n";
    
    $content = json_decode($response->getContent(), true);
    echo json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " at line " . $e->getLine() . "\n";
}
?>
