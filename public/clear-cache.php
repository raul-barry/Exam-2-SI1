<?php
// Archivo para forzar limpieza de cache
$base_path = __DIR__ . '/..';
passthru("php $base_path/artisan cache:clear");
passthru("php $base_path/artisan config:clear");
passthru("php $base_path/artisan route:clear");
echo "Cache limpiado";
?>
