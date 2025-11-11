<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application  
$app = require_once __DIR__ . '/bootstrap/app.php';

// Get the kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Usuario;
use App\Http\Controllers\Monitoreo_y_Reportes\DashboardController;
use Illuminate\Http\Request;

try {
    echo "=== TESTING BITÁCORA WITH ADMIN USER ===\n\n";
    
    // Get first admin user
    $admin = Usuario::whereHas('rol', function($q) {
        $q->where('nombre', 'Administrador');
    })->with(['rol', 'persona'])->first();
    
    if (!$admin) {
        echo "❌ No admin user found. Creating test...\n";
        
        // Try with any user first to understand structure
        $anyUser = Usuario::with('rol', 'persona')->first();
        if ($anyUser) {
            echo "Available user: CI=" . $anyUser->ci_persona . " | Rol=" . ($anyUser->rol->nombre ?? 'No role') . "\n";
            echo "All roles in database:\n";
            $roles = \App\Models\Rol::all();
            foreach ($roles as $rol) {
                echo "  - " . $rol->nombre . " (ID: " . $rol->id . ")\n";
            }
        }
        exit(1);
    }
    
    echo "✓ Testing with ADMIN user: " . $admin->ci_persona . " (Rol: " . ($admin->rol->nombre ?? 'N/A') . ")\n\n";
    
    // Authenticate the admin user
    $token = $admin->createToken('test-token')->plainTextToken;
    
    // Create a controller instance
    $controller = new DashboardController();
    
    // Create request
    $request = new Request();
    $request->setUserResolver(function() use ($admin) {
        return $admin;
    });
    $request->server->set('HTTP_AUTHORIZATION', "Bearer {$token}");
    
    // Set auth manually
    auth('sanctum')->setUser($admin);
    
    // Test Bitácora
    echo "--- Testing getBitacora() with Admin ---\n";
    try {
        $response = $controller->getBitacora($request);
        $data = json_decode($response->getContent(), true);
        
        echo "✓ Response Status: " . $response->status() . "\n";
        if ($response->status() === 200) {
            echo "✓ Bitácora Data:\n";
            echo "  - Total registros: " . ($data['total_registros'] ?? 0) . "\n";
            if (isset($data['registros_por_tipo'])) {
                echo "  - Tipos de operación:\n";
                foreach ($data['registros_por_tipo'] as $tipo => $count) {
                    echo "    • $tipo: $count\n";
                }
            }
            
            // Show sample records
            if (isset($data['bitacora']) && count($data['bitacora']) > 0) {
                echo "\n  - Sample records (primeros módulos):\n";
                $samples = array_slice($data['bitacora'], 0, 3);
                foreach ($samples as $i => $group) {
                    echo "    [{$i}] Módulo: {$group['modulo']} - {$group['cantidad']} registros\n";
                    if (isset($group['registros']) && count($group['registros']) > 0) {
                        $primer = $group['registros'][0];
                        echo "       └─ Última acción: {$primer['accion']} ({$primer['fecha']})\n";
                    }
                }
            }
        } else {
            echo "Error Status " . $response->status() . ": " . json_encode($data) . "\n";
        }
    } catch (\Exception $e) {
        echo "❌ Exception: " . $e->getMessage() . "\n";
    }
    
    // Also test with limits
    echo "\n--- Testing getBitacora() with limit=5 ---\n";
    try {
        $request->query->set('limite', 5);
        $response = $controller->getBitacora($request);
        $data = json_decode($response->getContent(), true);
        
        echo "✓ Response Status: " . $response->status() . "\n";
        if ($response->status() === 200) {
            echo "✓ Returned " . count($data['bitacora'] ?? []) . " registros (Límite: 5)\n";
        } else {
            echo "Error: " . json_encode($data) . "\n";
        }
    } catch (\Exception $e) {
        echo "❌ Exception: " . $e->getMessage() . "\n";
    }
    
    echo "\n✅ BITÁCORA TESTS COMPLETED!\n";
    
} catch (\Exception $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
