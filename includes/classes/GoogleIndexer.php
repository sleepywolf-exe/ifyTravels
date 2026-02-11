<?php
require_once __DIR__ . '/../../vendor/autoload.php';

class GoogleIndexer
{
    private $client;
    private $service;
    private $keyFile;
    private $isEnabled = false;

    public function __construct()
    {
        $this->keyFile = __DIR__ . '/../config/service_account.json';

        if (file_exists($this->keyFile)) {
            try {
                $this->client = new Google_Client();
                $this->client->setAuthConfig($this->keyFile);
                $this->client->addScope('https://www.googleapis.com/auth/indexing');
                $this->service = new Google_Service_Indexing($this->client);
                $this->isEnabled = true;
            } catch (Exception $e) {
                // Log error but don't crash app
                if (function_exists('error_log')) {
                    error_log("Google Indexer Init Error: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Publish URL Notification to Google
     * 
     * @param string $url The URL to index
     * @param string $type URL_UPDATED or URL_DELETED
     * @return array result
     */
    public function indexUrl($url, $type = 'URL_UPDATED')
    {
        if (!$this->isEnabled) {
            return [
                'status' => 'skipped',
                'message' => 'Service account key missing. Upload includes/config/service_account.json to enable.'
            ];
        }

        try {
            $content = new Google_Service_Indexing_UrlNotification();
            $content->setUrl($url);
            $content->setType($type);

            $result = $this->service->urlNotifications->publish($content);

            if ($result) {
                return ['status' => 'success', 'data' => $result->toSimpleObject()];
            } else {
                return ['status' => 'error', 'message' => 'Empty response from Google API'];
            }

        } catch (Exception $e) {
            // Check for 403 or 429
            $msg = $e->getMessage();
            if (strpos($msg, '403') !== false) {
                return ['status' => 'skipped', 'message' => 'Permission Denied (Check Service Account)'];
            }
            return ['status' => 'error', 'message' => $msg];
        }
    }

    /**
     * Get current indexing status of a URL
     */
    public function getStatus($url)
    {
        if (!$this->isEnabled)
            return null;

        try {
            return $this->service->urlNotifications->getMetadata(['url' => $url]);
        } catch (Exception $e) {
            return null;
        }
    }
}
