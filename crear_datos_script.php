#!/usr/bin/env php
<?php

// Script para crear datos de prueba interactivamente en Tinker

echo "Iniciando Tinker para crear datos de prueba...\n";

$commands = [
    "App\\Models\\Infraestructura::create(['nombre_infr' => 'Edificio Principal', 'ubicacion' => 'Piso 1', 'estado' => 'Activo']);",
    "\$infra = App\\Models\\Infraestructura::first();",
    "App\\Models\\Aula::create(['nro_aula' => 'A-101', 'tipo' => 'Teórica', 'capacidad' => 40, 'estado' => 'Activo', 'id_infraestructura' => \$infra->id_infraestructura]);",
    "App\\Models\\Aula::create(['nro_aula' => 'A-102', 'tipo' => 'Práctica', 'capacidad' => 30, 'estado' => 'Activo', 'id_infraestructura' => \$infra->id_infraestructura]);",
    "App\\Models\\Aula::all();",
];

foreach ($commands as $command) {
    echo "\nEjecutando: $command\n";
}

?>
