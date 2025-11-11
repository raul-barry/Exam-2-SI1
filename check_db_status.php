<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';

// Make the app
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AsignacionHorario;
use App\Models\Asistencia;
use App\Models\ConflictoHorario;
use App\Models\User;

try {
    echo "=== DATABASE STATUS ===\n\n";
    
    $usuariosCount = User::count();
    echo "✓ Total de usuarios: " . $usuariosCount . "\n";
    
    $asignacionesCount = AsignacionHorario::count();
    echo "✓ Total de asignaciones: " . $asignacionesCount . "\n";
    
    $asistenciasCount = Asistencia::count();
    echo "✓ Total de asistencias: " . $asistenciasCount . "\n";
    
    $conflictosCount = ConflictoHorario::count();
    echo "✓ Total de conflictos: " . $conflictosCount . "\n";
    
    // Sample data
    if ($asignacionesCount > 0) {
        echo "\n=== SAMPLE DATA ===\n";
        $asignacion = AsignacionHorario::first();
        echo "First Assignment: " . json_encode($asignacion, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
