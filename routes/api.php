<?php

use App\Models\Payment;
use App\Services\Finances\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/stripe/webhook', function (Request $request) {
    #dd([$request->input('data.object.id'), $request->input('data.object.status')]);
    $payment = Payment::where('intent_secret', '=', $request->input('data.object.id'))->first();
    $status = $request->input('data.object.status');
    (new PaymentService())->handle($payment, $status);
    return 'Done!';
});
