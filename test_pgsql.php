<?php
try {
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=appwebcargahoraria', 'postgres', 'CAMPEON');
    echo "Conexión exitosa a PostgreSQL\n";
    
    // Probar una consulta simple
    $result = $pdo->query("SELECT 1");
    echo "Consulta exitosa\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
