<?php

// Script para depurar permisos - Ejecutar en Laravel Tinker
// php artisan tinker
// > include('debug_permisos.php');

use App\Models\Rol;
use Illuminate\Support\Facades\DB;

echo "==== DEPURACIÓN DE PERMISOS ====\n\n";

// 1. Ver todos los permisos
echo "1. TODOS LOS PERMISOS EN BD:\n";
$todosPermisos = DB::table('carga_horaria.permiso')->get();
foreach ($todosPermisos as $p) {
    echo "   - {$p->id_permiso}: {$p->nombre}\n";
}
echo "Total: " . count($todosPermisos) . " permisos\n\n";

// 2. Ver permisos específicos
echo "2. LOS 5 PERMISOS NECESARIOS:\n";
$permisosNecesarios = DB::table('carga_horaria.permiso')
    ->whereIn('nombre', [
        'gestionar_materias',
        'gestionar_grupos',
        'configurar_malla_horaria',
        'asignar_carga_horaria',
        'gestionar_conflictos_horario'
    ])
    ->get();
foreach ($permisosNecesarios as $p) {
    echo "   - {$p->nombre} (ID: {$p->id_permiso})\n";
}
echo "Total encontrados: " . count($permisosNecesarios) . "\n\n";

// 3. Ver permisos de Administrador
echo "3. PERMISOS DE ADMINISTRADOR (id_rol=1):\n";
$admin = Rol::where('id_rol', 1)->first();
if ($admin) {
    echo "   Rol encontrado: {$admin->nombre}\n";
    
    // Método 1: Por relación
    echo "\n   Método 1 (Relación Eloquent):\n";
    $permisosAdmin1 = $admin->permisos()->get();
    foreach ($permisosAdmin1 as $p) {
        echo "      - {$p->nombre}\n";
    }
    echo "   Total: " . count($permisosAdmin1) . "\n";
    
    // Método 2: Por tienePermiso
    echo "\n   Método 2 (Query directo tienePermiso):\n";
    foreach (['gestionar_materias', 'gestionar_grupos', 'configurar_malla_horaria', 'asignar_carga_horaria', 'gestionar_conflictos_horario'] as $perm) {
        $tiene = $admin->tienePermiso($perm) ? '✓' : '✗';
        echo "      {$tiene} {$perm}\n";
    }
    
    // Método 3: Query manual
    echo "\n   Método 3 (Query manual de rol_permiso):\n";
    $permisosAdmin3 = DB::table('carga_horaria.rol_permiso as rp')
        ->join('carga_horaria.permiso as p', 'rp.id_permiso', '=', 'p.id_permiso')
        ->where('rp.id_rol', 1)
        ->get();
    foreach ($permisosAdmin3 as $p) {
        echo "      - {$p->nombre}\n";
    }
    echo "   Total: " . count($permisosAdmin3) . "\n";
} else {
    echo "   ✗ Rol Administrador NO encontrado\n";
}

// 4. Ver permisos de Coordinador
echo "\n4. PERMISOS DE COORDINADOR ACADÉMICO (id_rol=4):\n";
$coordinador = Rol::where('id_rol', 4)->first();
if ($coordinador) {
    echo "   Rol encontrado: {$coordinador->nombre}\n";
    
    // Método 1: Por relación
    echo "\n   Método 1 (Relación Eloquent):\n";
    $permisosCoord1 = $coordinador->permisos()->get();
    foreach ($permisosCoord1 as $p) {
        echo "      - {$p->nombre}\n";
    }
    echo "   Total: " . count($permisosCoord1) . "\n";
    
    // Método 2: Por tienePermiso
    echo "\n   Método 2 (Query directo tienePermiso):\n";
    foreach (['gestionar_materias', 'gestionar_grupos', 'configurar_malla_horaria', 'asignar_carga_horaria', 'gestionar_conflictos_horario'] as $perm) {
        $tiene = $coordinador->tienePermiso($perm) ? '✓' : '✗';
        echo "      {$tiene} {$perm}\n";
    }
} else {
    echo "   ✗ Rol Coordinador Académico NO encontrado\n";
}

echo "\n==== FIN DEPURACIÓN ====\n";
