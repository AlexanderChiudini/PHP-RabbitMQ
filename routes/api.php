<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use PhpAmqpLib\Connection\AMQPStreamConnection;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/add-queue-fanout', function (Request $request) {
    Artisan::call('rabbitmq:publish-fanout', [
        'message' => $request->input('message'),
    ]);

    return response()->json(['status' => 'Mensagem adicionada a fila FANOUT rabbitmq']);
});

Route::post('/add-queue-direct', function (Request $request) {
    Artisan::call('rabbitmq:publish-direct', [
        'routingKey' => $request->input('routingKey'),
        'message' => $request->input('message'),
    ]);

    return response()->json(['status' => 'Mensagem adicionada a fila DIRECT rabbitmq']);
});

Route::post('/add-queue-topic', function (Request $request) {
    Artisan::call('rabbitmq:publish-topic', [
        'routingKey' => $request->input('routingKey'),
        'message' => $request->input('message'),
    ]);

    return response()->json(['status' => 'Mensagem adicionada a fila TOPIC rabbitmq']);
});
