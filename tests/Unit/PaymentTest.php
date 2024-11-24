<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a test user
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_initiate_payment()
    {
        // Simulate login
        $this->actingAs($this->user);

        // Mock HTTP response from PayMongo API
        Http::fake([
            'https://api.paymongo.com/v1/payment_intents' => Http::response([
                'data' => [
                    'id' => 'pi_test_123',
                    'attributes' => [
                        'client_key' => 'client_key_test_123',
                    ],
                ]
            ], 200),
        ]);

        $response = $this->postJson('/v1/payments/initiate', [
            'technician_id' => 1,
            'booking_date' => '2024-12-01',
            'vehicle_id' => 1,
            'service_ids' => json_encode([1, 2]),
            'total_fee' => 1000,
            'payment_method' => 'gcash'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['client_key', 'intent_id']);
    }

    /** @test */
    public function it_can_attach_payment_method()
    {
        // Simulate login
        $this->actingAs($this->user);

        // Mock HTTP response for attaching payment method
        Http::fake([
            'https://api.paymongo.com/v1/payment_intents/pi_test_123/attach' => Http::response([
                'data' => [
                    'attributes' => [
                        'next_action' => ['redirect' => ['url' => 'https://example.com/next-step']]
                    ]
                ]
            ], 200),
        ]);

        $response = $this->postJson('/v1/payments/attach', [
            'payment_intent_id' => 'pi_test_123',
            'payment_method_id' => 'pm_test_123'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'next_action']);
    }

    /** @test */
    public function it_can_confirm_payment()
    {
        // Simulate login
        $this->actingAs($this->user);

        // Insert a payment record for testing
        $payment = Payment::create([
            'user_id' => $this->user->id,
            'amount' => 1000,
            'currency' => 'PHP',
            'status' => 'Pending',
            'payment_method' => 'gcash',
            'transaction_id' => 'pi_test_123',
            'payment_date' => now(),
            'notes' => 'Payment test.'
        ]);

        // Mock HTTP response from PayMongo API for confirming payment
        Http::fake([
            'https://api.paymongo.com/v1/payment_intents/pi_test_123' => Http::response([
                'data' => [
                    'attributes' => [
                        'status' => 'succeeded'
                    ]
                ]
            ], 200),
        ]);

        $response = $this->getJson('/v1/payments/confirm?intent_id=pi_test_123');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Payment confirmed and booking created']);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'Paid',
        ]);
    }
}
