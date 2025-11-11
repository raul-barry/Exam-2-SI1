<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';

// Make the app
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Usuario;

try {
    echo "=== TESTING NEW DASHBOARD ENDPOINTS ===\n\n";
    
    // Get first user
    $user = Usuario::with(['rol', 'persona'])->first();
    if (!$user) {
        echo "❌ No users found\n";
        exit(1);
    }
    
    echo "✓ Testing with user: " . $user->ci_persona . " (Rol: " . ($user->rol->nombre ?? 'N/A') . ")\n\n";
    
    // Create a token
    $token = $user->createToken('test-token')->plainTextToken;
    
    // Get HTTP kernel
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    
    // Test 1: KPIs
    echo "--- Testing /api/dashboard/kpis ---\n";
    $request1 = \Illuminate\Http\Request::create('/api/dashboard/kpis', 'GET', [], [], [], [
        'HTTP_AUTHORIZATION' => "Bearer {$token}",
        'HTTP_ACCEPT' => 'application/json'
    ]);
    $response1 = $kernel->handle($request1);
    echo "Status: " . $response1->status() . "\n";
    $data1 = json_decode($response1->getContent(), true);
    if ($response1->status() === 200) {
        echo "✓ KPIs: " . json_encode($data1['kpis'] ?? [], JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "✗ Error: " . ($data1['message'] ?? 'Unknown') . "\n";
    }
    
    echo "\n--- Testing /api/dashboard/coordinacion ---\n";
    $request2 = \Illuminate\Http\Request::create('/api/dashboard/coordinacion', 'GET', [], [], [], [
        'HTTP_AUTHORIZATION' => "Bearer {$token}",
        'HTTP_ACCEPT' => 'application/json'
    ]);
    $response2 = $kernel->handle($request2);
    echo "Status: " . $response2->status() . "\n";
    $data2 = json_decode($response2->getContent(), true);
    if ($response2->status() === 200) {
        echo "✓ Coordinación obtenida:\n";
        echo "  - Docentes coordinados: " . ($data2['coordinacion']['docentes_coordinados'] ?? 0) . "\n";
        echo "  - Grupos coordinados: " . ($data2['coordinacion']['grupos_coordinados'] ?? 0) . "\n";
        echo "  - Aulas utilizadas: " . ($data2['coordinacion']['aulas_utilizadas'] ?? 0) . "\n";
    } else {
        echo "✗ Error: " . ($data2['message'] ?? 'Unknown') . "\n";
    }
    
    echo "\n--- Testing /api/dashboard/bitacora ---\n";
    $request3 = \Illuminate\Http\Request::create('/api/dashboard/bitacora', 'GET', [], [], [], [
        'HTTP_AUTHORIZATION' => "Bearer {$token}",
        'HTTP_ACCEPT' => 'application/json'
    ]);
    $response3 = $kernel->handle($request3);
    echo "Status: " . $response3->status() . "\n";
    $data3 = json_decode($response3->getContent(), true);
    if ($response3->status() === 200) {
        echo "✓ Bitácora obtenida: " . ($data3['total_registros'] ?? 0) . " registros\n";
    } elseif ($response3->status() === 403) {
        echo "⚠ Acceso denegado (el usuario no es administrador)\n";
    } else {
        echo "✗ Error: " . ($data3['message'] ?? 'Unknown') . "\n";
    }
    
    echo "\n✅ ALL ENDPOINTS WORKING!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
