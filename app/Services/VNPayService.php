<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class VNPayService  
{
    protected string $appId;
    protected string $key1;
    protected string $key2;
    protected string $endpoint;
    protected Client $http;

    public function __construct()
    {
        // Đọc từ .env hoặc config/services.php
        $this->appId    = trim((string)config('services.vnpay.app_id', env('VNP_APP_ID')));
        $this->key1     = trim((string)config('services.vnpay.key1', env('VNP_APP_KEY1')));
        $this->key2     = trim((string)config('services.vnpay.key2', env('VNP_APP_KEY2')));
        $this->endpoint = rtrim((string)env('VNP_ENDPOINT', 'https://sandbox.vnpayment.vn/v2'), '/');  // Endpoint VNPay
        $this->http     = new Client(['timeout' => 20]);
    }

    protected function jencode($v): string
    {
        // JSON không escape dấu '/'
        return json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function millis(): int
    {
        return (int) floor(microtime(true) * 1000);
    }

    protected function callbackDomain(): string
    {
        return rtrim((string)env('VNP_CALLBACK_DOMAIN', config('app.url')), '/');
    }

    /** Tạo đơn thanh toán VNPay */
    public function create(array $input): array
    {
        $appTransId = (string)$input['app_trans_id'];

        $embedData = $this->jencode([
            'redirecturl' => $this->callbackDomain() . '/payment/vnpay/return',
        ]);
        $items = $this->jencode($input['items'] ?? []);

        $data = [
            'app_id'       => (int)$this->appId,
            'app_trans_id' => $appTransId,
            'app_user'     => (string)($input['app_user'] ?? 'guest'),
            'app_time'     => $this->millis(),
            'amount'       => (int)$input['amount'],
            'description'  => (string)($input['description'] ?? 'Payment'),
            'bank_code'    => (string)($input['bank_code'] ?? ''),
            'item'         => $items,
            'embed_data'   => $embedData,
            'callback_url' => $this->callbackDomain() . '/payment/vnpay/callback',
        ];

        // Chuỗi ký: app_id|app_trans_id|app_user|amount|app_time|embed_data|item
        $raw = $data['app_id'] . '|' . $data['app_trans_id'] . '|' . $data['app_user'] . '|' .
            $data['amount'] . '|' . $data['app_time'] . '|' . $data['embed_data'] . '|' . $data['item'];

        $data['mac'] = hash_hmac('sha256', $raw, $this->key1);

        Log::info('VNP_CREATE_DEBUG', [
            'key1_len' => strlen($this->key1),
            'raw'      => $raw,
            'mac'      => $data['mac'],
        ]);

        try {
            $res  = $this->http->post($this->endpoint . '/create', ['form_params' => $data]);  // Thay đổi URL của VNPay
            $body = json_decode((string)$res->getBody(), true) ?: [];
            Log::info('VNP_CREATE', ['request' => $data, 'response' => $body]);
            return [$data, $body];
        } catch (\Throwable $e) {
            Log::error('VNP_CREATE_ERR', ['message' => $e->getMessage(), 'request' => $data]);
            return [$data, ['return_code' => 0, 'return_message' => $e->getMessage()]];
        }
    }

    /** Query giao dịch VNPay */
    public function query(string $appTransId): array
    {
        $raw = $this->appId . '|' . $appTransId . '|query';
        $mac = hash_hmac('sha256', $raw, $this->key1);

        $params = [
            'app_id'       => (int)$this->appId,
            'app_trans_id' => $appTransId,
            'mac'          => $mac,
        ];

        try {
            $res  = $this->http->post($this->endpoint . '/query', ['form_params' => $params]);  // Endpoint query của VNPay
            $body = json_decode((string)$res->getBody(), true) ?: [];
            return $body;
        } catch (\Throwable $e) {
            return ['return_code' => 0, 'return_message' => $e->getMessage()];
        }
    }

    /** Verify callback VNPay */
    public function verifyCallback(?string $data, ?string $mac): bool
    {
        if (!$data || !$mac) return false;
        $myMac = hash_hmac('sha256', $data, $this->key2);
        return hash_equals($myMac, $mac);
    }
}
