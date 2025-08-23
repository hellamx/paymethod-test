<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Str;

class PaygineService
{
    protected Client $client;
    protected string $sector;
    protected string $password;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('paygine.url'),
            'verify' => false, // отключаем SSL check в тесте
        ]);

        $this->sector = config('paygine.sector');
        $this->password = config('paygine.password');
    }

    protected function generateSignature(int $amount, int $currency): string
    {
        $sector   = (string) $this->sector;
        $amount   = (string) $amount;
        $currency = (string) $currency;
        $password = (string) $this->password;

        $str = $sector . $amount . $currency . $password;

        $sha256Hex = hash('sha256', $str);

        return base64_encode($sha256Hex);
    }

    public function generateSignatureToPay($payMethodOrderId)
    {
        $sector   = (string) $this->sector;
        $password = (string) $this->password;

        $str = $sector . $payMethodOrderId . $password;

        $sha256Hex = hash('sha256', $str);

        return base64_encode($sha256Hex);
    }


    /**
     * Регистрируем заказ и получаем ссылку для редиректа
     */
    public function registerOrder(
        int $amount,
        int $currency = 643,
        string $description = '',
    )
    {
        $signature = $this->generateSignature($amount, $currency);

        $reference = mb_strtoupper(Str::random()) . rand(1, 100000000);

        $data = [
            'sector'      => (int) $this->sector,
            'amount'      => $amount, // копейки (10000 = 100 руб)
            'currency'    => $currency,
            'description' => $description,
            'reference'   => $reference,
            'signature'   => $signature,
            'url'         => config('paygine.success_url'),
            'failurl'     => config('paygine.fail_url'),
            'notify_url'  => config('paygine.notify_url'),
        ];

        $response = $this->client->post('Register', [
            'form_params' => $data
        ]);

        $responseDecoded = simplexml_load_string($response->getBody());

        $id = (int) $responseDecoded->id ?? null;
        $urlToRedirect = null;

        if (null !== $id) {
            $urlToRedirect = sprintf(
                'https://test.paygine.com/webapi/Purchase?sector=%s&id=%s&signature=%s',
                $this->sector,
                $id,
                $this->generateSignatureToPay($id)
            );
        }

       // dd($urlToRedirect);
       // dd($responseDecoded);

        return [simplexml_load_string((string) $response->getBody()), $urlToRedirect];
    }
}
