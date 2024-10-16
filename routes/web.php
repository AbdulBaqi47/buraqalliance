<?php

use App\Http\Controllers\Central\AjaxController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Central\Auth\LoginController;
use App\Http\Controllers\Central\Auth\RegisterController;
use App\Http\Controllers\Central\HomeController;
use App\Http\Controllers\Central\TenantController;
use App\Http\Controllers\Central\TestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return redirect(route('central.admin.dashboard'));
});




Route::group([
    'namespace' => 'Central'
], function () {

    Route::get('test-route', [TestController::class, 'index']);


    // Authentication Routes...
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('central.admin.login');
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [LoginController::class, 'logout'])->name('central.admin.logout');

    // Registration Routes...
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('central.admin.register');
    Route::post('register', [RegisterController::class, 'register']);


    Route::get('/', [HomeController::class, 'index'])->name('central.admin.dashboard');

    
    // Profile
    Route::get('/profile', [HomeController::class, 'showProfileForm'])->name('central.admin.profile');
    Route::post('/profile', [HomeController::class, 'save_profile']);

    # Generate blob against file path
    Route::post('files/generate', [HomeController::class, 'getFile']);

    # Tenants routes
    Route::prefix('tenants')->group(function () {
        Route::get('/', [TenantController::class, 'view'])->name('central.admin.tenants.view');
        Route::get('/data', [AjaxController::class, 'getTenants'])->name('central.admin.tenants.data');

        Route::get('/add', [TenantController::class, 'showTenantForm'])->name('central.admin.tenants.add');
        Route::POST('/add', [TenantController::class, 'create']);

        Route::GET('/edit', [TenantController::class, 'showEditForm'])->name('central.admin.tenants.edit');
        Route::POST('/edit', [TenantController::class, 'edit']);

        Route::DELETE('{id}/delete', [TenantController::class, 'delete'])->name('central.admin.tenants.delete');
    });

});

