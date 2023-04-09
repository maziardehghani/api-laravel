<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['middleware' => ['auth:sanctum']] , function ()
{
    Route::get('/brands' , [BrandController::class , 'index']);
    Route::post('/brand' , [BrandController::class , 'store']);
    Route::get('/brand/{brand}' , [BrandController::class , 'show']);
    Route::put('/brand/{brand}' , [BrandController::class , 'update']);
    Route::delete('/brand/{brand}' , [BrandController::class , 'destroy']);
    Route::get('/brand/{brand}/products' , [BrandController::class , 'products']);



    Route::get('/categories' , [CategoryController::class , 'index']);
    Route::post('/category' , [CategoryController::class , 'store']);
    Route::get('/category/{category}' , [CategoryController::class , 'show'])->name('categories.show');
    Route::put('/category/{category}' , [CategoryController::class , 'update'])->name('categories.update');
    Route::delete('/category/{category}' , [CategoryController::class , 'destroy'])->name('categories.destroy');
    Route::get('/category/{category}/children' , [CategoryController::class , 'children'])->name('categories.showChildren');
    Route::get('/category/{category}/products' , [CategoryController::class , 'products'])->name('categories.products');




    Route::get('/products' , [ProductController::class , 'index'])->name('products');
    Route::post('/product' , [ProductController::class , 'store'])->name('products.store');
    Route::get('/product/{product}' , [ProductController::class , 'show'])->name('products.show');
    Route::put('/product/{product}' , [ProductController::class , 'update'])->name('products.update');
    Route::delete('/product/{product}' , [ProductController::class , 'destroy'])->name('products.destroy');

    Route::post('/payment/send' , [PaymentController::class , 'send']);
    Route::post('/payment/verify' , [PaymentController::class , 'verify']);

    Route::post('logout' , [AuthController::class , 'logout']);

});




Route::post('register' , [AuthController::class , 'register']);
Route::post('login' , [AuthController::class , 'login']);
