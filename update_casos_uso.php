<?php
require __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Actualizar Gestión de Roles
$casoDeUso = \App\Models\CasoDeUso::where('nombre', 'Gestión de Roles')->first();
if ($casoDeUso) {
    $casoDeUso->estado = 'activo';
    $casoDeUso->save();
    echo "✓ Caso de Uso 'Gestión de Roles' actualizado a ACTIVO\n";
} else {
    echo "✗ No se encontró el caso de uso 'Gestión de Roles'\n";
}

// Mostrar todos los casos de uso
echo "\n=== CASOS DE USO ACTUALES ===\n";
$casos = \App\Models\CasoDeUso::all();
foreach ($casos as $caso) {
    echo "- {$caso->nombre}: {$caso->estado}\n";
}
