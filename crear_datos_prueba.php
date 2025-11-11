<?php

require_once __DIR__ . '/bootstrap/app.php';

use App\Models\Infraestructura;
use App\Models\Aula;

// Crear infraestructura de prueba
$infraestructura = Infraestructura::firstOrCreate(
    ['nombre_infr' => 'Edificio Principal'],
    [
        'ubicacion' => 'Piso 1',
        'estado' => 'Activo'
    ]
);

echo "✓ Infraestructura creada/encontrada: ID = {$infraestructura->id_infraestructura}\n";

// Crear aulas de prueba
$aulas_datos = [
    ['nro_aula' => 'A-101', 'tipo' => 'Teórica', 'capacidad' => 40, 'estado' => 'Activo'],
    ['nro_aula' => 'A-102', 'tipo' => 'Práctica', 'capacidad' => 30, 'estado' => 'Activo'],
    ['nro_aula' => 'A-103', 'tipo' => 'Laboratorio', 'capacidad' => 25, 'estado' => 'Activo'],
];

foreach ($aulas_datos as $aula_data) {
    $aula_data['id_infraestructura'] = $infraestructura->id_infraestructura;
    
    $aula = Aula::firstOrCreate(
        ['nro_aula' => $aula_data['nro_aula']],
        $aula_data
    );
    
    echo "✓ Aula creada/encontrada: {$aula->nro_aula}\n";
}

echo "\n✓ Datos de prueba creados exitosamente\n";
?>
