<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Hacer un test directo
echo "=== TEST DE LOGIN ===\n\n";

$usuario = \App\Models\Usuario::where('ci_persona', '12345678')->first();

if (!$usuario) {
    echo "Usuario NO encontrado\n";
    exit;
}

echo "Usuario encontrado: {$usuario->ci_persona}\n";
echo "Estado: " . ($usuario->estado ? 'Activo' : 'Inactivo') . "\n";

// Verificar contraseña
$password = '12345678';
if (\Illuminate\Support\Facades\Hash::check($password, $usuario->contrasena)) {
    echo "✓ Contraseña correcta\n";
    
    // Crear token
    $token = $usuario->createToken('auth-token')->plainTextToken;
    echo "✓ Token creado: " . substr($token, 0, 20) . "...\n";
} else {
    echo "✗ Contraseña incorrecta\n";
}
