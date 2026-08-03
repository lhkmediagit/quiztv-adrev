<?php

namespace App\Controllers;

use App\Models\AdSettingModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Controller: AdsController
 * Dynamically serves the ads.txt content if enabled, otherwise returns a 404.
 */
class AdsController extends BaseController
{
    /**
     * Serves the dynamic ads.txt contents.
     */
    public function index()
    {
        $model = new AdSettingModel();
        
        $enabled = $model->getSetting('ads_txt_enabled', '0');
        $content = $model->getSetting('ads_txt_content', '');

        log_message('error', "DEBUG ads.txt check: enabled = " . json_encode($enabled) . ", content = " . json_encode($content));

        if ($enabled !== '1') {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setContentType('text/plain')
            ->setBody($content);
    }
}
