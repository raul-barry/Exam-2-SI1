<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Inasistencia;
use Carbon\Carbon;

// Crear algunas inasistencias de prueba
$inasistencias = [
    [
        'codigo_doc' => 'DOC001',
        'fecha' => Carbon::now()->subDays(5)->toDateString(),
        'motivo_aparente' => 'Problema familiar',
        'estado' => 'PENDIENTE',
        'tipo_inasistencia' => 'INJUSTIFICADA'
    ],
    [
        'codigo_doc' => 'DOC002',
        'fecha' => Carbon::now()->subDays(3)->toDateString(),
        'motivo_aparente' => 'Enfermedad',
        'estado' => 'EN_REVISION',
        'tipo_inasistencia' => 'PENDIENTE_JUSTIFICACION'
    ],
    [
        'codigo_doc' => 'DOC003',
        'fecha' => Carbon::now()->subDays(1)->toDateString(),
        'motivo_aparente' => 'Compromiso institucional',
        'estado' => 'PENDIENTE',
        'tipo_inasistencia' => 'INJUSTIFICADA'
    ],
    [
        'codigo_doc' => 'DOC001',
        'fecha' => Carbon::now()->subDays(2)->toDateString(),
        'motivo_aparente' => 'Asunto personal',
        'estado' => 'RESUELTA',
        'tipo_inasistencia' => 'JUSTIFICADA'
    ]
];

foreach ($inasistencias as $data) {
    $inasistencia = Inasistencia::create($data);
    echo "✅ Inasistencia creada: " . $inasistencia->id_inasistencia . "\n";
}

echo "\n✅ Se crearon " . count($inasistencias) . " inasistencias de prueba\n";
