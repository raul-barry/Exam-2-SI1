<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Obtener usuarios
$usuarios = \App\Models\Usuario::all();

echo "=== VERIFICANDO CONTRASEÑAS ===\n";

// Probando contraseña "12345678" con ambos usuarios
$testPassword = "12345678";

foreach ($usuarios as $usuario) {
    echo "\nUsuario CI: {$usuario->ci_persona}\n";
    
    if (\Illuminate\Support\Facades\Hash::check($testPassword, $usuario->contrasena)) {
        echo "✓ Contraseña '$testPassword' es correcta!\n";
    } else {
        echo "✗ Contraseña '$testPassword' NO es correcta\n";
    }
}

// Opción: crear un usuario de prueba con contraseña conocida
echo "\n=== CREANDO USUARIO DE PRUEBA ===\n";

$exists = \App\Models\Usuario::where('ci_persona', '99999999')->first();
if (!$exists) {
    $usuario = \App\Models\Usuario::create([
        'ci_persona' => '99999999',
        'contrasena' => \Illuminate\Support\Facades\Hash::make('password123'),
        'id_rol' => 1,
        'estado' => true,
    ]);
    echo "Usuario de prueba creado:\n";
    echo "CI: 99999999\n";
    echo "Contraseña: password123\n";
} else {
    echo "Usuario 99999999 ya existe\n";
}
