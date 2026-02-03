<?php

class FacebookCAPI
{
    private $pixelId;
    private $accessToken;
    private $apiVersion = 'v19.0';

    public function __construct()
    {
        $this->pixelId = defined('FB_PIXEL_ID') ? FB_PIXEL_ID : '';
        $this->accessToken = defined('FB_ACCESS_TOKEN') ? FB_ACCESS_TOKEN : '';
    }

    /**
     * Send an event to Facebook CAPI
     * 
     * @param string $eventName Standard event name (e.g., 'PageView', 'Purchase')
     * @param array $customData Event specific data (value, currency, content_ids, etc.)
     * @param array $userData User data to be hashed (email, phone, etc.)
     * @param string $eventSourceUrl The URL where the event occurred
     * @return array|bool Response from Facebook or false on failure
     */
    public function sendEvent($eventName, $customData = [], $userData = [], $eventSourceUrl = null)
    {
        if (empty($this->pixelId) || empty($this->accessToken) || $this->pixelId === 'YOUR_PIXEL_ID') {
            return false; // Not configured
        }

        // 1. Prepare User Data (Hash PII)
        $hashedUserData = $this->processUserData($userData);

        // Add standard user data fields
        $hashedUserData['client_user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $hashedUserData['client_ip_address'] = $this->getClientIP();

        // Handle FBC/FBP cookies if available
        if (isset($_COOKIE['_fbc']))
            $hashedUserData['fbc'] = $_COOKIE['_fbc'];
        if (isset($_COOKIE['_fbp']))
            $hashedUserData['fbp'] = $_COOKIE['_fbp'];

        // 2. Prepare Event Data
        $event = [
            'event_name' => $eventName,
            'event_time' => time(),
            'event_source_url' => $eventSourceUrl ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
            'user_data' => $hashedUserData,
            'action_source' => 'website',
        ];

        if (!empty($customData)) {
            $event['custom_data'] = $customData;
        }

        // 3. Construct Payload
        $payload = [
            'data' => [$event],
            // 'test_event_code' => 'TEST62356', // Uncomment for testing in Events Manager
        ];

        // 4. Send Request
        return $this->sendRequest($payload);
    }

    private function processUserData($userData)
    {
        $processed = [];

        // Fields that need hashing
        $hashFields = ['em' => 'email', 'ph' => 'phone'];

        foreach ($hashFields as $fbKey => $inputKey) {
            if (!empty($userData[$inputKey])) {
                // Normalize and hash
                $value = strtolower(trim($userData[$inputKey]));
                $processed[$fbKey] = hash('sha256', $value);
            }
        }

        // Pass through non-hashed fields if needed (e.g., country, city if implemented)
        // ...

        return $processed;
    }

    private function getClientIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        return $_SERVER['REMOTE_ADDR'];
    }

    private function sendRequest($payload)
    {
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->pixelId}/events?access_token={$this->accessToken}";

        // Log Payload
        if (!is_dir(__DIR__ . '/../../logs')) {
            mkdir(__DIR__ . '/../../logs', 0777, true);
        }
        file_put_contents(__DIR__ . '/../../logs/fb_capi_payload.log', date('Y-m-d H:i:s') . " - " . json_encode($payload) . PHP_EOL, FILE_APPEND);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Debug logging
        $logFile = __DIR__ . '/../../logs/fb_capi.log';
        $logEntry = date('Y-m-d H:i:s') . " - Code: $httpCode - Event: " . ($payload['data'][0]['event_name'] ?? 'Unknown') . " - Response: " . $response . PHP_EOL;
        file_put_contents($logFile, $logEntry, FILE_APPEND);

        if ($httpCode !== 200) {
            error_log("FB CAPI Error [$httpCode]: " . $response);
        }

        return $httpCode === 200 ? json_decode($response, true) : false;
    }
}
