<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Usuarios;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('insertarusuario','controladores\Usuarios@insertarUser');
Route::post('iniciarsesion','controladores\Usuarios@LogIn');
//////////////////////////////////////////////////////////////////////////////////
Route::middleware(['auth:sanctum'])->put('actualizarusuario/{id}','controladores\Usuarios@actualizarpersona')->where (['id'=>'[0-9]+']);
/////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
Route::middleware('auth:sanctum')->delete('cerrarsesion','controladores\Usuarios@LogOut')->where (['id'=>'[0-9]+']);
