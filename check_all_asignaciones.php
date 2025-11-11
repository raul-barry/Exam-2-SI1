<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';

// Make the app
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AsignacionHorario;

try {
    echo "=== ALL ASIGNACIONES (regardless of state) ===\n\n";
    
    $todasLasAsignaciones = AsignacionHorario::all();
    echo "✓ Total de asignaciones: " . count($todasLasAsignaciones) . "\n";
    
    if (count($todasLasAsignaciones) > 0) {
        echo "\nDetalle de cada asignación:\n";
        foreach ($todasLasAsignaciones as $asignacion) {
            echo "  - ID: {$asignacion->id_asignacion}, Docente: {$asignacion->codigo_doc}, Estado: {$asignacion->estado}, Período: {$asignacion->periodo_academico}\n";
        }
    }
    
    // Count by estado
    $porEstado = AsignacionHorario::selectRaw('estado, COUNT(*) as total')->groupBy('estado')->get();
    echo "\n✓ Asignaciones por estado:\n";
    foreach ($porEstado as $row) {
        echo "  - {$row->estado}: {$row->total}\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
