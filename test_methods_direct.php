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
    echo "=== TESTING NEW DASHBOARD ENDPOINTS (Direct Method Call) ===\n\n";
    
    // Get first admin or coordinator user
    $user = Usuario::whereHas('rol', function($q) {
        $q->whereIn('nombre', ['Administrador', 'Coordinador Académico']);
    })->with(['rol', 'persona'])->first();
    
    if (!$user) {
        echo "❌ No admin/coordinator users found\n";
        exit(1);
    }
    
    echo "✓ Testing with user: " . $user->ci_persona . " (Rol: " . ($user->rol->nombre ?? 'N/A') . ")\n\n";
    
    // Authenticate the user
    $token = $user->createToken('test-token')->plainTextToken;
    
    // Create a controller instance
    $controller = new DashboardController();
    
    // Test 1: KPIs
    echo "--- Testing getKPIs() ---\n";
    try {
        $request = new Request();
        $request->setUserResolver(function() use ($user) {
            return $user;
        });
        $request->server->set('HTTP_AUTHORIZATION', "Bearer {$token}");
        
        // Manually set auth in sanctum
        auth('sanctum')->setUser($user);
        
        $response = $controller->getKPIs($request);
        $data = json_decode($response->getContent(), true);
        
        echo "✓ Response Status: " . $response->status() . "\n";
        if ($response->status() === 200) {
            echo "✓ KPIs Data:\n";
            if (isset($data['kpis'])) {
                foreach ($data['kpis'] as $key => $value) {
                    echo "  - $key: " . json_encode($value) . "\n";
                }
            }
        } else {
            echo "Error: " . json_encode($data) . "\n";
        }
    } catch (\Exception $e) {
        echo "❌ Exception: " . $e->getMessage() . "\n";
    }
    
    echo "\n--- Testing getCoordinacionHorario() ---\n";
    try {
        $response = $controller->getCoordinacionHorario($request);
        $data = json_decode($response->getContent(), true);
        
        echo "✓ Response Status: " . $response->status() . "\n";
        if ($response->status() === 200) {
            echo "✓ Coordinación Data:\n";
            if (isset($data['coordinacion'])) {
                echo "  - Docentes coordinados: " . ($data['coordinacion']['docentes_coordinados'] ?? 0) . "\n";
                echo "  - Grupos coordinados: " . ($data['coordinacion']['grupos_coordinados'] ?? 0) . "\n";
                echo "  - Aulas utilizadas: " . ($data['coordinacion']['aulas_utilizadas'] ?? 0) . "\n";
                if (isset($data['coordinacion']['por_periodo'])) {
                    echo "  - Períodos en coordinación: " . count($data['coordinacion']['por_periodo']) . "\n";
                }
            }
        } else {
            echo "Error: " . json_encode($data) . "\n";
        }
    } catch (\Exception $e) {
        echo "❌ Exception: " . $e->getMessage() . "\n";
    }
    
    echo "\n--- Testing getBitacora() ---\n";
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
        } elseif ($response->status() === 403) {
            echo "⚠ Acceso denegado: El usuario no es administrador\n";
            echo "  (Usuario actual es: " . ($user->rol->nombre ?? 'Sin rol') . ")\n";
        } else {
            echo "Error: " . json_encode($data) . "\n";
        }
    } catch (\Exception $e) {
        echo "❌ Exception: " . $e->getMessage() . "\n";
    }
    
    echo "\n✅ ALL METHOD TESTS COMPLETED!\n";
    
} catch (\Exception $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
