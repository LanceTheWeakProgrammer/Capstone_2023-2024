<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|integer',
            'currency' => 'required|string|max:3',
            'payment_method_type' => 'required|string|in:gcash,card',
            'booking_id' => 'required|integer|exists:bookings,id',
        ]);
    
        try {
            $booking = Booking::find($validated['booking_id']);
            if (in_array($booking->status, ['Pending', 'No Show', 'Cancellation Requested', 'Cancelled', 'Completed'])) {
                return response()->json([
                    'error' => "Cannot proceed with payment. Booking status is {$booking->status}.",
                ], 400);
            }

            $existingPayment = Payment::where('booking_id', $validated['booking_id'])
                ->whereNotNull('transaction_id')
                ->latest()
                ->first();
    
            if ($existingPayment) {
                if ($existingPayment->payment_method !== $validated['payment_method_type']) {
                    $response = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
                        ->post('https://api.paymongo.com/v1/payment_intents', [
                            'data' => [
                                'attributes' => [
                                    'amount' => $validated['amount'] * 100,
                                    'currency' => $validated['currency'],
                                    'payment_method_allowed' => [$validated['payment_method_type']],
                                    'description' => "Payment for Booking ID: {$validated['booking_id']}",
                                ],
                            ],
                        ]);
    
                    $paymentIntent = $response->json();
    
                    if ($response->failed()) {
                        throw new Exception("Error creating PaymentIntent: " . $response->body());
                    }

                    $existingPayment->update([
                        'transaction_id' => $paymentIntent['data']['id'],
                        'amount' => $validated['amount'],
                        'currency' => $validated['currency'],
                        'payment_method' => $validated['payment_method_type'],
                        'status' => 'pending',
                        'notes' => 'Payment intent replaced with new method',
                    ]);
    
                    return response()->json([
                        'client_key' => $paymentIntent['data']['attributes']['client_key'],
                        'payment_intent_id' => $paymentIntent['data']['id'],
                    ]);
                }

                $existingPayment->update([
                    'amount' => $validated['amount'],
                    'currency' => $validated['currency'],
                    'status' => 'pending',
                    'payment_method' => $validated['payment_method_type'],
                    'notes' => 'Payment intent updated',
                ]);
    
                return response()->json([
                    'client_key' => $existingPayment->client_key,
                    'payment_intent_id' => $existingPayment->transaction_id,
                ]);
            }

            $response = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
                ->post('https://api.paymongo.com/v1/payment_intents', [
                    'data' => [
                        'attributes' => [
                            'amount' => $validated['amount'] * 100,
                            'currency' => $validated['currency'],
                            'payment_method_allowed' => [$validated['payment_method_type']],
                            'description' => "Payment for Booking ID: {$validated['booking_id']}",
                        ],
                    ],
                ]);
    
            $paymentIntent = $response->json();
    
            if ($response->failed()) {
                throw new Exception("Error creating PaymentIntent: " . $response->body());
            }

            Payment::updateOrCreate(
                ['booking_id' => $validated['booking_id']],
                [
                    'user_id' => auth()->id(),
                    'amount' => $validated['amount'],
                    'currency' => $validated['currency'],
                    'status' => 'pending',
                    'payment_method' => $validated['payment_method_type'],
                    'transaction_id' => $paymentIntent['data']['id'],
                    'payment_date' => now(),
                    'notes' => 'Payment intent created',
                ]
            );
    
            return response()->json([
                'client_key' => $paymentIntent['data']['attributes']['client_key'],
                'payment_intent_id' => $paymentIntent['data']['id'],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }    
    
    public function attachPaymentIntent(Request $request)
    {
        $validated = $request->validate([
            'payment_intent_id' => 'required|string',
            'payment_method_type' => 'required|string|in:gcash,card',
            'billing_name' => 'required|string|max:255',
            'billing_email' => 'required|email|max:255',
            'billing_phone' => 'required|string|max:20',
        ]);
    
        try {
            $response = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
                ->get("https://api.paymongo.com/v1/payment_intents/{$validated['payment_intent_id']}");
    
            if ($response->failed()) {
                throw new Exception("Error retrieving PaymentIntent: " . $response->body());
            }
    
            $paymentIntent = $response->json();
            Log::debug('PayMongo retrievePaymentIntent response', ['response' => $paymentIntent]);
            $allowedMethods = $paymentIntent['data']['attributes']['payment_method_allowed'];
    
            if (!in_array($validated['payment_method_type'], $allowedMethods)) {
                throw new Exception("The selected payment method ({$validated['payment_method_type']}) is not allowed for this PaymentIntent.");
            }
    
            $billingDetails = [
                'name' => $validated['billing_name'],
                'email' => $validated['billing_email'],
                'phone' => $validated['billing_phone'],
            ];
    
            if ($validated['payment_method_type'] === 'card') {
                $cardDetails = $request->validate([
                    'card_number' => 'required|string',
                    'exp_month' => 'required|numeric|min:1|max:12',
                    'exp_year' => 'required|numeric|min:' . date('Y'),
                    'cvc' => 'required|string',
                ]);
    
                $cardDetails['exp_month'] = (int) $cardDetails['exp_month'];
                $cardDetails['exp_year'] = (int) $cardDetails['exp_year'];
    
                $paymentMethodResponse = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
                    ->post('https://api.paymongo.com/v1/payment_methods', [
                        'data' => [
                            'attributes' => [
                                'type' => 'card',
                                'details' => [
                                    'card_number' => $cardDetails['card_number'],
                                    'exp_month' => $cardDetails['exp_month'],
                                    'exp_year' => $cardDetails['exp_year'],
                                    'cvc' => $cardDetails['cvc'],
                                ],
                                'billing' => $billingDetails,
                            ],
                        ],
                    ]);
            } else {
                $paymentMethodResponse = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
                    ->post('https://api.paymongo.com/v1/payment_methods', [
                        'data' => [
                            'attributes' => [
                                'type' => 'gcash',
                                'billing' => $billingDetails,
                            ],
                        ],
                    ]);
            }
    
            $paymentMethod = $paymentMethodResponse->json();
    
            if ($paymentMethodResponse->failed()) {
                throw new Exception("Error creating PaymentMethod: " . $paymentMethodResponse->body());
            }
    
            $attachResponse = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
            ->post("https://api.paymongo.com/v1/payment_intents/{$validated['payment_intent_id']}/attach", [
                'data' => [
                    'attributes' => [
                        'payment_method' => $paymentMethod['data']['id'],
                        'return_url' => env('FRONTEND_URL') . '/payment-status' 
                            . ($validated['payment_method_type'] === 'card' ? '?payment_intent_id=' . $validated['payment_intent_id'] : ''),
                    ],
                ],
            ]);        
    
            $attachedPaymentIntent = $attachResponse->json();
    
            if ($attachResponse->failed()) {
                throw new Exception("Error attaching PaymentMethod to PaymentIntent: " . $attachResponse->body());
            }
    
            return response()->json([
                'next_action_url' => $attachedPaymentIntent['data']['attributes']['next_action']['redirect']['url'] ?? null,
                'status' => $attachedPaymentIntent['data']['attributes']['status'],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

    public function retrievePaymentIntent(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent_id');
        
        if (!$paymentIntentId) {
            return response()->json([
                'error' => 'The payment intent id field is required.',
            ], 400);
        }
 
        $payment = Payment::where('transaction_id', $paymentIntentId)->first();
    
        if (!$payment) {
            return response()->json([
                'error' => 'No payment record found with the provided transaction ID.',
            ], 404);
        }
    
        try {
            $response = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
                ->get("https://api.paymongo.com/v1/payment_intents/{$paymentIntentId}");
    
            if ($response->failed()) {
                throw new Exception("Error retrieving PaymentIntent: " . $response->body());
            }
    
            $paymentIntent = $response->json();
            $paymongoStatus = $paymentIntent['data']['attributes']['status'];

            $appStatus = $this->mapStatus($paymongoStatus);

            $payment->update([
                'status' => $appStatus,
                'notes' => 'Status updated from PayMongo',
            ]);
    
            return response()->json([
                'transaction_id' => $payment->transaction_id,
                'updated_status' => $payment->status,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function mapStatus($paymongoStatus)
    {
        switch ($paymongoStatus) {
            case 'succeeded':
                return 'Paid';
            case 'awaiting_next_action':
                return 'Paused';
            case 'processing':
                return 'Processing';
            case 'failed':
                return 'Failed';
            case 'awaiting_payment_method':
                return 'Expired';
            default:
                return ucfirst(str_replace('_', ' ', $paymongoStatus));
        }
    }
    
}
