<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return response()->json([
        'status' => 'API funcionando',
        'version' => '1.0'
    ]);
});

Route::get('/test-aulas', function () {
    return response()->json(App\Models\Aula::with('infraestructura')->get());
});

// ==========================================
// RUTAS PÚBLICAS
// ==========================================
Route::post('/logout', function () {
    auth('sanctum')->logout();
    return redirect('/login');
})->middleware('auth:sanctum');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// ==========================================
// RUTAS PROTEGIDAS - DASHBOARD BLADE (Servidor)
// ==========================================
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/roles', [DashboardController::class, 'roles'])->name('dashboard.roles');
Route::get('/dashboard/materias', [DashboardController::class, 'materias'])->name('dashboard.materias');
Route::get('/dashboard/grupos', [DashboardController::class, 'grupos'])->name('dashboard.grupos');
Route::get('/dashboard/aulas', [DashboardController::class, 'aulas'])->name('dashboard.aulas');
Route::get('/dashboard/infraestructura', [DashboardController::class, 'infraestructura'])->name('dashboard.infraestructura');
Route::get('/dashboard/horarios', [DashboardController::class, 'horarios'])->name('dashboard.horarios');

// Rutas cortas del dashboard (sin /dashboard prefix)
Route::get('/usuarios', [DashboardController::class, 'usuarios'])->name('usuarios');
Route::get('/docentes', [DashboardController::class, 'docentes'])->name('docentes');
Route::get('/roles', [DashboardController::class, 'roles'])->name('roles');
Route::get('/materias', [DashboardController::class, 'materias'])->name('materias');
Route::get('/grupos', [DashboardController::class, 'grupos'])->name('grupos');
Route::get('/aulas', [DashboardController::class, 'aulas'])->name('aulas');
Route::get('/infraestructura', [DashboardController::class, 'infraestructura'])->name('infraestructura');
Route::get('/horarios', [DashboardController::class, 'horarios'])->name('horarios');

// ==========================================
// RUTAS SPA - REACT (Todas las demás)
// ==========================================
// Todas las rutas que NO sean /api van a React
Route::get('/{path}', function () {
    return view('app');
})->where('path', '(?!api).*')->name('app');


