<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\PaymentRequestController;
use App\Http\Controllers\api\authController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::post('/auth/login', [authController::class, 'loginUser']);

Route::group(['prefix' => 'auth'], function () {
        Route::post('login', [authController::class, 'loginUser']);
        Route::post('register', [authController::class, 'register']);
    
        Route::group(['middleware' => 'auth:sanctum'], function() {
          Route::get('logout', [authController::class, 'logout']);
          Route::get('user', [authController::class, 'user']);
        });
});

Route::middleware('auth:sanctum')->group(function() {

        Route::post('/tigopayment/MNOcallback', 
                [PaymentRequestController::class, 'pesaportTigoCallBack']);

        Route::post('/tigopayment/store', 
                [PaymentRequestController::class, 'store']);

        Route::post('/tigopayment/pesaportTigoB2C', 
                [PaymentRequestController::class, 'pesaportTigoB2C']);

        Route::post('/tigopayment/pesaportTigoC2B', 
                [PaymentRequestController::class, 'pesaportTigoC2B']);

});



// Route::post('/tigopayment/store', [authController::class, 'store']);




// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

