<?php

if (!function_exists('upload_to_docservice')) {
    /**
     * Upload a CodeIgniter 4 UploadedFile object to LHK Media Document Service.
     *
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file The uploaded file instance
     * @param string $filepath The target subdirectory path (e.g. 'quizhive/avatars')
     * @return string|null The public inline view URL on success, or null on failure
     */
    function upload_to_docservice(\CodeIgniter\HTTP\Files\UploadedFile $file, string $filepath): ?string
    {
        $baseUrl = env('DOCSERVICE_BASE_URL', 'https://docservice.lhkmedia.io/');
        $apiKey = env('DOCSERVICE_API_KEY', '');
        $senderDomain = env('DOCSERVICE_SENDER_DOMAIN', '');

        if (empty($apiKey) || empty($senderDomain)) {
            log_message('error', '[DocService] API Key or Sender Domain is missing in .env config.');
            return null;
        }

        $uploadUrl = rtrim($baseUrl, '/') . '/api/upload';
        $tempPath = $file->getTempName();
        $mimeType = $file->getClientMimeType();
        $originalName = $file->getClientName();
        $filename = $file->getRandomName();

        $postFields = [
            'file'          => new \CURLFile($tempPath, $mimeType, $originalName),
            'filepath'      => $filepath,
            'filename'      => $filename,
            'sender_domain' => $senderDomain,
        ];

        $ch = curl_init($uploadUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ["X-API-Key: {$apiKey}"],
            CURLOPT_POSTFIELDS     => $postFields,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 201) {
            $data = json_decode($response, true);
            if (isset($data['success']) && $data['success'] && isset($data['data']['url'])) {
                // Return 'url' (the inline preview URL), NOT 'download_url'
                return $data['data']['url'];
            }
        }

        log_message('error', "[DocService] Upload failed. HTTP Code: {$httpCode}. Response: {$response}");
        return null;
    }
}
