<?php

namespace JeremyWorboys\SonarrPutIO;

use PutIO\Helpers\PutIO\PutIOHelper;

class TorrentUploader extends PutIOHelper
{
    /**
     * @param string $file
     * @param int    $parentID
     * @return array
     */
    public function uploadTorrentFile($file, $parentID = 0)
    {
        if (!$file = realpath($file)) {
            throw new \Exception('File not found');
        }

        $params = [
            'parent_id' => $parentID,
            'file'      => "@{$file}",
        ];

        return $this->request('POST', 'files/upload', $params, '', \false, 'transfer');
    }
}
