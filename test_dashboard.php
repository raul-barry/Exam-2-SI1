<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use App\Models\AsignacionHorario;
use App\Models\User;

try {
    // Verificar conexión a base de datos
    DB::connection()->getPdo();
    echo "✓ Conexión a base de datos exitosa\n";
    
    // Contar asignaciones
    $totalAsignaciones = AsignacionHorario::count();
    echo "✓ Total de asignaciones: " . $totalAsignaciones . "\n";
    
    // Contar usuarios
    $totalUsuarios = User::count();
    echo "✓ Total de usuarios: " . $totalUsuarios . "\n";
    
    // Verificar si existen períodos
    $periodos = AsignacionHorario::select('periodo_academico')
        ->distinct()
        ->pluck('periodo_academico');
    echo "✓ Períodos disponibles: " . $periodos->count() . "\n";
    if ($periodos->count() > 0) {
        echo "  Períodos: " . $periodos->implode(', ') . "\n";
    }
    
    echo "\nTodo luce bien. El problema podría estar en el autenticación de la API o rutas.\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
