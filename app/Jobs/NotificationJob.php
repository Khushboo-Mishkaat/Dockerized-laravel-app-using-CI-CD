<?php

namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;
use Google_Client;

class NotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $title;
    protected $message;
    protected $fcm_tokens;

    /**
     * Create a new job instance.
     *
     * @param string $title
     * @param string $message
     * @param array $fcm_tokens
     */
    public function __construct($title, $message, $fcm_tokens)
    {
        $this->title = $title;
        $this->message = $message;
        $this->fcm_tokens = $fcm_tokens;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Notification handle called.');
        $senderId = env('SENDER_ID');
        $url = 'https://fcm.googleapis.com/v1/projects/' . $senderId . '/messages:send';
        $SERVER_API_KEY = $this->getAccessToken();

        $payload = [
            'message' => [
                'notification' => [
                    'title' => $this->title,
                    'body' => $this->message,
                ],
                'android' => [
                    'notification' => [
                        'sound' => 'alert.mp3',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'alert.caf',
                        ],
                    ],
                ],
            ],
        ];

        $headers = [
            'Authorization: Bearer ' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $successCount = 0;
        $failureCount = 0;
        $errors = [];
        $results = [];

        foreach ($this->fcm_tokens as $token) {
            $payload['message']['token'] = $token;
            $encodedPayload = json_encode($payload);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedPayload);

            $result = curl_exec($ch);

            if ($result === false) {
                $errors[] = 'Curl failed: ' . curl_error($ch);
            } else {
                $response = json_decode($result, true);
                if (isset($response['error'])) {
                    $failureCount++;
                    $errors[] = "Notification failed: " . json_encode($response['error']);
                } else {
                    $successCount++;
                    $results[] = "Notification sent successfully to token: " . $token;
                }
            }

            curl_close($ch);
        }

        Log::info("Notification results: ", [
            'successCount' => $successCount,
            'failureCount' => $failureCount
        ]);
        if (!empty($errors)) {
            Log::error("Notification errors: ", $errors);
        }
        if (!empty($results)) {
            Log::info("Notification successes: ", $results);
        }
    }

    private function getAccessToken()
    {
        $client = new Google_Client();
        $client->setAuthConfig(base_path(env('FIREBASE_CREDENTIALS')));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $token = $client->fetchAccessTokenWithAssertion();
        return $token['access_token'];
    }
}
