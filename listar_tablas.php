#!/usr/bin/env php
<?php
$lines = file(__DIR__ . '/.env');
foreach ($lines as $line) {
    if (strpos($line, 'DB_') === 0) {
        putenv(trim($line));
    }
}

try {
    $conn = pg_connect("host=127.0.0.1 port=5432 dbname=appwebcargahoraria user=postgres password=CAMPEON options='-c search_path=carga_horaria'");
    
    if (!$conn) {
        die("Error de conexión\n");
    }
    
    // Listar todas las tablas
    echo "=== TABLAS EN LA BASE DE DATOS ===\n";
    $query = "SELECT table_name FROM information_schema.tables WHERE table_schema='carga_horaria' ORDER BY table_name";
    $result = pg_query($conn, $query);
    
    while ($row = pg_fetch_assoc($result)) {
        echo "- " . $row['table_name'] . "\n";
    }
    
    pg_close($conn);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
