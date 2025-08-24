<?php

namespace App\Http\Controllers;

use App\Services\PaygineService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

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

    public function payBySbp(Request $request, PaygineService $paygineService)
    {
        [$result, $url] = $paygineService->registerOrderViaSbp(
            10000, // 100.00 руб
            643, // RUB
            'Оплата заказа #1001'
        );

        if ($url) {
            // редирект на платёжную страницу
            return redirect($url);
        }

        return response()->json(['error' => 'Не удалось зарегистрировать заказ']);
    }

    public function payHook(Request $request)
    {
        // Получаем содержимое запроса
        $xmlContent = $request->getContent();

        try {
            // Парсим XML
            $xml = new SimpleXMLElement($xmlContent);

            // Конвертируем в массив
            $data = $this->simpleXmlToArray($xml);

            // Сохраняем в JSON файл
            $filename = 'hook_data_' . now()->format('Y-m-d_H-i-s') . '.json';
            $filePath = storage_path('app/xml_parsed/' . $filename);

            // Создаем директорию если не существует
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }

            // Сохраняем как JSON
            file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return response()->json([
                'success' => true,
                'message' => 'XML successfully parsed and saved as JSON',
                'data' => $data,
                'file_path' => $filePath
            ]);

        } catch (\Exception $e) {
            Log::error('XML parsing error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Invalid XML format',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Конвертирует SimpleXML объект в массив
     */
    private function simpleXmlToArray($xml): array
    {
        $array = json_decode(json_encode((array) $xml), true);

        // Рекурсивно очищаем массив от пустых значений
        return array_map(function ($value) {
            if (is_array($value)) {
                return $this->cleanArray($value);
            }
            return $value;
        }, $array);
    }

    /**
     * Очищает массив от пустых значений
     */
    private function cleanArray(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->cleanArray($value);
            }

            if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    public function payByPlate(Request $request, PaygineService $paygineService)
    {
        [$result, $url] = $paygineService->registerOrderViaPlate(
            10000, // 100.00 руб
            643, // RUB
            'Оплата заказа #1001'
        );

        if ($url) {
            // редирект на платёжную страницу
            return redirect($url);
        }

        return response()->json(['error' => 'Не удалось зарегистрировать заказ']);
    }

    public function testPaySbp(int $sbpId, int $orderId)
    {

       /* $client = new Client([
            'base_uri' => 'https://test.paygine.com',
            'verify' => false, // отключаем SSL check в тесте
        ]);

        $str = config('paygine.sector') . 150 . $sbpId . $orderId . config('paygine.password');

        $sha256Hex = hash('sha256', $str);

        $signature = base64_encode($sha256Hex);

        $response = $client->post('test/SBPTestCase', [
            'form_params' => [
                'sector' => config('paygine.sector'),
                'case_id' => 150,
                'qrc_id' => $sbpId,
                'order_id' => $orderId,
                'signature' => $signature
            ]
        ]);

        $responseDecoded = simplexml_load_string($response->getBody());

        dd($responseDecoded);*/

    }
}
