<?php

namespace JeremyWorboys\SonarrPutIO\Service\PutIO;

use PutIO\Helpers\PutIO\PutIOHelper;

class LinkFinder extends PutIOHelper
{
    /**
     * @param int  $fileID
     * @param bool $mediaOnly
     * @return array
     */
    public function getDownloadLinks($fileID, $mediaOnly = \false)
    {
        $params = [
            'file_ids' => $fileID,
        ];

        $key = $mediaOnly ? 'media_links' : '';

        return $this->get('files/get-download-links', $params, \false, $key);
    }
}
