
<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';

// Make the app
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AsignacionHorario;
use App\Models\Asistencia;

try {
    echo "=== CHECKING DASHBOARD DATA ===\n\n";
    
    // Check assignments
    $asignaciones = AsignacionHorario::where('estado', 'ACTIVO')->get();
    echo "✓ Total de asignaciones activas: " . count($asignaciones) . "\n";
    
    if (count($asignaciones) > 0) {
        echo "\nPrimera asignación:\n";
        $primera = $asignaciones->first();
        echo json_encode($primera->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
    
    // Check périodos
    $periodos = AsignacionHorario::select('periodo_academico')
        ->distinct()
        ->orderBy('periodo_academico', 'desc')
        ->pluck('periodo_academico');
    echo "\n✓ Períodos disponibles: " . count($periodos) . "\n";
    if (count($periodos) > 0) {
        echo "Períodos: " . $periodos->implode(', ') . "\n";
    }
    
    // Check attendance
    $asistencias = Asistencia::count();
    echo "\n✓ Total de asistencias: " . $asistencias . "\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
