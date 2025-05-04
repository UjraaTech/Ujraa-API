<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Annotations as OA;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Webhooks",
 *     description="API Endpoints for payment webhooks"
 * )
 */
class WebhookController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/v1/webhooks/paymob",
     *     summary="Handle Paymob payment webhook",
     *     tags={"Webhooks"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="type", type="string"),
     *             @OA\Property(property="obj", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Webhook processed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success")
     *         )
     *     )
     * )
     */
    public function handlePaymob(Request $request): JsonResponse
    {
        try {
            // التحقق من صحة التوقيع
            $hmac = $request->header('HMAC');
            $calculatedHmac = hash_hmac('sha512', json_encode($request->all()), config('services.paymob.hmac_secret'));

            if ($hmac !== $calculatedHmac) {
                \Log::error('Invalid Paymob webhook signature');
                return response()->json(['status' => 'error', 'message' => 'توقيع غير صالح'], 400);
            }

            // معالجة الدفع
            $transaction = $request->input('obj');
            
            if ($transaction['success'] === true) {
                // تحديث حالة المعاملة
                $payment = Payment::where('transaction_id', $transaction['id'])->first();
                if ($payment) {
                    $payment->update(['status' => 'completed']);
                    // إضافة الرصيد للمستخدم
                    $this->creditService->addCredits($payment->user, $payment->amount);
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            \Log::error('Paymob webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'خطأ في معالجة الطلب'], 500);
        }
    }
}