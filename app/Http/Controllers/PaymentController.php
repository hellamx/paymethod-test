<?php

namespace App\Http\Controllers;

use App\Services\PaygineService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        return view('payment');
    }

    public function payByCard(Request $request, PaygineService $paygineService)
    {
        [$result, $url] = $paygineService->registerOrder(
            10000, // 100.00 руб
            643, // RUB
            'Оплата заказа #1001'
        );

        file_put_contents('test_' . time() . '.json', json_encode($result));

        if ($url) {
            // редирект на платёжную страницу
            return redirect($url);
        }

        return response()->json(['error' => 'Не удалось зарегистрировать заказ']);
    }

    public function payHook(Request $request) {
        file_put_contents('hook_data' , time() . '.json', json_encode($request->all()));
        file_put_contents('hook_headers' , time() . '.json', json_encode($request->headers));
    }
}
