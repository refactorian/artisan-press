<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchWebhookJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    /**
     * Create a new job instance.
     *
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public string $url,
        public string $event,
        public array $payload,
        public ?string $secret = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $body = [
            'event' => $this->event,
            'timestamp' => now()->toIso8601String(),
            'data' => $this->payload,
        ];

        $jsonPayload = json_encode($body, JSON_THROW_ON_ERROR);

        $headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => 'Laravel-Blog-Webhooks/1.0',
            'X-Blog-Event' => $this->event,
        ];

        if ($this->secret) {
            $headers['X-Blog-Signature'] = hash_hmac('sha256', $jsonPayload, $this->secret);
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->withBody($jsonPayload, 'application/json')
                ->post($this->url);

            if (! $response->successful()) {
                Log::warning("Webhook delivery to {$this->url} returned HTTP {$response->status()}");
                $this->fail(new \RuntimeException("Webhook failed with status: {$response->status()}"));
            }
        } catch (\Throwable $e) {
            Log::error("Webhook error delivering to {$this->url}: {$e->getMessage()}");
            throw $e;
        }
    }
}
