<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayOSService
{
    protected string $clientId;
    protected string $apiKey;
    protected string $checksumKey;
    protected string $baseUrl = 'https://api-merchant.payos.vn';

    public function __construct()
    {
        $this->clientId = config('services.payos.client_id', '');
        $this->apiKey = config('services.payos.api_key', '');
        $this->checksumKey = config('services.payos.checksum_key', '');
    }

    /**
     * Tạo chữ ký (signature) cho PayOS
     */
    public function generateSignature(array $data): string
    {
        ksort($data);
        $queryString = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                continue; // Bỏ qua mảng lồng nhau (chỉ ký các trường phẳng)
            }
            $queryString[] = "{$key}={$value}";
        }
        $signData = implode('&', $queryString);
        return hash_hmac('sha256', $signData, $this->checksumKey);
    }

    /**
     * Tạo Link Thanh Toán PayOS
     * 
     * @param array $payload chứa: orderCode, amount, description, cancelUrl, returnUrl, items (optional)
     * @return array|null
     */
    public function createPaymentLink(array $payload): ?array
    {
        // MOCK CHẾ ĐỘ TEST (dùng cho đồ án khi chưa có tài khoản PayOS thật)
        if ($this->clientId === 'your_client_id_here' || empty($this->clientId)) {
            Log::info('Sử dụng MOCK PayOS checkout url do chưa cấu hình API key thật');
            return [
                'checkoutUrl' => $payload['returnUrl'] ?? url('/')
            ];
        }

        try {
            // Chỉ ký 5 trường bắt buộc
            $signData = [
                'amount' => $payload['amount'],
                'cancelUrl' => $payload['cancelUrl'],
                'description' => $payload['description'],
                'orderCode' => $payload['orderCode'],
                'returnUrl' => $payload['returnUrl'],
            ];

            $signature = $this->generateSignature($signData);
            $payload['signature'] = $signature;

            Log::info('PayOS API Request', [
                'url' => "{$this->baseUrl}/v2/payment-requests",
                'orderCode' => $payload['orderCode'],
                'amount' => $payload['amount']
            ]);

            $response = Http::withoutVerifying()->withHeaders([
                'x-client-id' => $this->clientId,
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/v2/payment-requests", $payload);

            if ($response->failed()) {
                Log::error('PayOS API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $resBody = $response->json();
            if ((isset($resBody['code']) && $resBody['code'] === '00') || (isset($resBody['error']) && $resBody['error'] === 0)) {
                return $resBody['data'];
            }

            Log::error('PayOS Response error', $resBody);
            return null;
        } catch (\Throwable $e) {
            Log::error('PayOS createPaymentLink Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Lấy thông tin link thanh toán PayOS hiện tại
     * 
     * @param int $orderCode
     * @return array|null
     */
    public function getPaymentLinkInformation(int|string $orderCode): ?array
    {
        $orderCode = (int)$orderCode;
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'x-client-id' => $this->clientId,
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get("{$this->baseUrl}/v2/payment-requests/{$orderCode}");

            if ($response->failed()) {
                Log::error('PayOS getPaymentLinkInformation failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $resBody = $response->json();
            if ((isset($resBody['code']) && $resBody['code'] === '00') || (isset($resBody['error']) && $resBody['error'] === 0)) {
                return $resBody['data'];
            }

            Log::error('PayOS getPaymentLinkInformation Response error', $resBody);
            return null;
        } catch (\Throwable $e) {
            Log::error('PayOS getPaymentLinkInformation Exception', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Hủy Link Thanh Toán PayOS
     * 
     * @param int $orderCode
     * @param string $cancellationReason
     * @return bool
     */
    public function cancelPaymentLink(int|string $orderCode, string $cancellationReason = 'Đơn hàng đã bị hủy'): bool
    {
        $orderCode = (int)$orderCode;
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'x-client-id' => $this->clientId,
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/v2/payment-requests/{$orderCode}/cancel", [
                'cancellationReason' => mb_substr($cancellationReason, 0, 250)
            ]);

            if ($response->failed()) {
                Log::error('PayOS cancelPaymentLink failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }

            $resBody = $response->json();
            if ((isset($resBody['code']) && $resBody['code'] === '00') || (isset($resBody['error']) && $resBody['error'] === 0)) {
                return true;
            }

            Log::error('PayOS cancelPaymentLink Response error', $resBody);
            return false;
        } catch (\Throwable $e) {
            Log::error('PayOS cancelPaymentLink Exception', [
                'message' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Xác thực Webhook của PayOS
     * 
     * @param array $webhookPayload
     * @return bool
     */
    public function verifyWebhook(array $webhookPayload): bool
    {
        if (!isset($webhookPayload['data']) || !isset($webhookPayload['signature'])) {
            return false;
        }

        $data = $webhookPayload['data'];
        $signature = $webhookPayload['signature'];

        $calculatedSignature = $this->generateSignature($data);

        if ($calculatedSignature !== $signature) {
            Log::warning('PayOS Webhook Signature Mismatch', [
                'received' => $signature,
                'calculated' => $calculatedSignature,
                'data' => $data
            ]);
            return false;
        }

        return true;
    }
}
