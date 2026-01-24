<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\Api\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * معالجة Stripe Webhook
     * POST /api/stripe/webhook
     */
    public function handleStripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature') ?? 'test_signature';

        // ✅ تسجيل البيانات للتشخيص
        Log::info('🔔 Webhook Received', [
            'signature' => $signature,
            'payload_length' => strlen($payload),
            'webhook_secret' => config('services.stripe.webhook_secret') ? 'EXISTS' : 'MISSING',
        ]);

        try {
            $this->paymentService->handleWebhook(
                json_decode($payload, true),
                $signature
            );

            Log::info('✅ Webhook processed successfully');
            return response()->json(['status' => 'success'], 200);

        } catch (\UnexpectedValueException $e) {
            // ❌ خطأ في التحقق من Signature
            Log::error('❌ Invalid Signature', [
                'error' => $e->getMessage(),
                'signature' => $signature,
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);

        } catch (\Exception $e) {
            // ❌ خطأ عام
            Log::error('❌ Webhook Error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
