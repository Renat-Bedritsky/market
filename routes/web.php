<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\BasketController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ControlController;
use App\Http\Controllers\AddController;
use App\Http\Controllers\OrdersController;

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

// Route::get('/{page}', function ($page) {
//     return view('base');
// });

Route::match(['get', 'post'], '/orders', [OrdersController::class, 'orders']);

Route::match(['get', 'post'], '/add', [AddController::class, 'add']);

Route::match(['get', 'post'], '/control/{user}', [ControlController::class, 'control']);

Route::match(['get', 'post'], '/profile/{user}', [ProfileController::class, 'profile']);

Route::match(['get', 'post'], '/detail/{code}', [DetailController::class, 'getDetail']);

Route::match(['get', 'post'], '/order', [OrderController::class, 'order']);

Route::match(['get', 'post'], '/registration', [RegistrationController::class, 'registration']);

Route::get('/logout', [BaseController::class, 'Logout']);

Route::match(['get', 'post'], '/mobile/{page}', [CategoryController::class, 'getCategory']);
Route::match(['get', 'post'], '/portable/{page}', [CategoryController::class, 'getCategory']);
Route::match(['get', 'post'], '/appliances/{page}', [CategoryController::class, 'getCategory']);
Route::match(['get', 'post'], '/other/{page}', [CategoryController::class, 'getCategory']);

Route::match(['get', 'post'], '/auth', [AuthController::class, 'auth']);

Route::get('/basket/clear', [BasketController::class, 'clearBasket']);
Route::match(['get', 'post'], '/basket', [BasketController::class, 'getBasket']);

Route::get('/categories', [CategoriesController::class, 'getCategories']);

Route::match(['get', 'post'], '/', [BaseController::class, 'get']);

Route::match(['get', 'post'], '/{page}', [BaseController::class, 'get']);

