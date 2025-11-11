<?php

// Información del servidor
echo "<h2>Información de PHP</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Loaded Extensions:<br>";
$extensions = get_loaded_extensions();
echo "<pre>";
echo implode(", ", $extensions);
echo "</pre>";

// Test PDO PostgreSQL
echo "<h2>Test PDO PostgreSQL</h2>";
try {
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=appwebcargahoraria', 'postgres', 'CAMPEON');
    echo "✓ Conexión exitosa a PostgreSQL<br>";
    
    // Probar consulta simple
    $result = $pdo->query("SELECT COUNT(*) as count FROM usuario");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    echo "✓ Usuarios en BD: " . $row['count'] . "<br>";
    
} catch (PDOException $e) {
    echo "✗ Error de conexión: " . $e->getMessage() . "<br>";
}

// Test Laravel
echo "<h2>Test Laravel</h2>";
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    
    // Test conexión directa
    $usuarios = \App\Models\Usuario::take(5)->get();
    echo "✓ Usuarios desde Laravel: " . count($usuarios) . "<br>";
    
} catch (Exception $e) {
    echo "✗ Error de Laravel: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
