<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Google\Auth\Credentials\ServiceAccountCredentials;

class FCMService
{
    private $projectId;
    private $credentialsPath;
    private const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

    public function __construct()
    {
        $this->projectId       = config('services.firebase.project_id');
        $this->credentialsPath = storage_path('app/firebase-credentials.json');
    }

    // ── Send to a single FCM token ─────────────────────────
    public function send(
        string $token,
        string $title,
        string $body,
        array  $data  = [],
        string $sound = 'default'
    ): bool {
        try {
            $accessToken = $this->getAccessToken();

            $payload = [
                'message' => [
                    'token'        => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data'     => array_map('strval', $data),
                    'android'  => [
                        'priority'     => 'high',
                        'notification' => [
                            'channel_id' => $data['type'] ?? 'general',
                            'sound'      => $sound,
                        ],
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => $sound,
                                'badge' => 1,
                            ],
                        ],
                    ],
                ],
            ];

            $response = Http::withToken($accessToken)
                ->post(
                    "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send",
                    $payload
                );

            if ($response->failed()) {
                Log::warning('FCM send failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'token'  => substr($token, 0, 20) . '...',
                ]);
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('FCMService error: ' . $e->getMessage());
            return false;
        }
    }

    // ── Send to multiple tokens (batch) ───────────────────
    public function sendBatch(
        array  $tokens,
        string $title,
        string $body,
        array  $data = []
    ): array {
        $sent   = 0;
        $failed = 0;

        foreach (array_filter(array_unique($tokens)) as $token) {
            $this->send($token, $title, $body, $data)
                ? $sent++
                : $failed++;
        }

        return ['sent' => $sent, 'failed' => $failed];
    }

    // ── OAuth2 access token (cached 55 min) ────────────────
    private function getAccessToken(): string
    {
        return Cache::remember('fcm_access_token', 3300, function () {
            $creds = new ServiceAccountCredentials(
                self::SCOPE,
                json_decode(file_get_contents($this->credentialsPath), true)
            );
            $token = $creds->fetchAuthToken();
            return $token['access_token'];
        });
    }
}
