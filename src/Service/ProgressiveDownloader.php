<?php

namespace JeremyWorboys\SonarrPutIO\Service;

class ProgressiveDownloader
{
    /**
     * Port range to be used to find running application.
     */
    public const MIN_APP_PORT = 25300;
    public const MAX_APP_PORT = 25305;

    /**
     * URL to open to launch application.
     */
    public const APP_LAUNCH_URL = 'psplugin://app.launch';

    /**
     * Last port used to send messages from browser to Progressive Downloader.
     *
     * @var int
     */
    public $appPort = 0;

    /**
     * Version of last used application.
     *
     * @var int
     */
    public $appVer = 0;

    /**
     * Launch Progressive Downloader if it's not yet running.
     */
    public function launchApp()
    {
        if ($this->findAppPort()) {
            return;
        }

        exec('open ' . self::APP_LAUNCH_URL);

        while (!$this->findAppPort()) {
            sleep(2);
        }
    }

    /**
     * Get application version using TCP/IP port.
     *
     * @return string Returns application version on recent versions, 1.10 on versions older than 1.10.12 and 0 when application not found running.
     */
    public function getAppVersion(): string
    {
        $this->findAppPort();

        return $this->appVer;
    }

    /**
     * Add a download task.
     *
     * @param string $url     Address to download.
     * @param string $referer Referrer address.
     * @param string $cookies List of cookies.
     * @return bool Returns true when task request successfully sent and false otherwise.
     */
    public function addTask($url, $referer = '', $cookies = ''): bool
    {
        $result = false;

        if ($this->appPort !== 0) {
            $result = $this->addTaskUsingPort($url, $referer, $cookies);
            if (!$result) {
                $this->appPort = 0;
            }
        }

        if (!$result) {
            $result = $this->addTaskUsingPort($url, $referer, $cookies);
        }

        return $result;
    }

    /**
     * Send custom command.
     *
     * @param string $command Command test constructed with getCommandString.
     * @return object
     */
    public function sendCommand($command)
    {
        try {
            $url = 'http://127.0.0.1:' . $this->appPort;
            return $this->sendHttpPostRequest($url, $command);
        } catch (\Throwable $e) {
            echo 'Exception: ' . $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * Get command string suitable for PS notification.
     *
     * @param string $name Notification name.
     * @param array  $args Array of arguments where "key" is a parameter key and "value" - it's value.
     * @return string Returns encoded command string.
     */
    private function getCommandString($name, $args = [])
    {
        $result = "notificationName;" . strlen($name) . ";" . $name . ";";

        foreach ($args as $key => $value) {
            $result .= $key . ";" . strlen($value) . ";" . $value . ";";
        }

        return $result;
    }

    /**
     * Look for open application port from range [25300 ... 25305].
     *
     * @return int Returns open port or zero.
     */
    private function findAppPort(): int
    {
        $result = $this->appPort;

        if (!$result) {
            for ($i = self::MIN_APP_PORT; $i <= self::MAX_APP_PORT; $i++) {
                try {
                    $url = 'http://127.0.0.1:' . $i;
                    $body = $this->getCommandString('psTest');
                    $response = $this->sendHttpPostRequest($url, $body);
                    if ($response->status === 202) {
                        $result = $i;
                        $this->appPort = $result;
                        $this->appVer = '1.10';
                        try {
                            $this->appVer = $response->responseJson['version'];
                        } catch (\Throwable $e) {
                            echo 'Exception: ' . $e->getMessage() . PHP_EOL;
                        }
                        break;
                    }
                } catch (\Throwable $e) {
                    echo 'Exception: ' . $e->getMessage() . PHP_EOL;
                }
            }
        } else {
            try {
                $url = 'http://127.0.0.1:' . $result;
                $body = $this->getCommandString('psTest');
                $response = $this->sendHttpPostRequest($url, $body);
                if ($response->status === 202) {
                    $this->appVer = '1.10';
                    try {
                        $this->appVer = $response->responseJson['version'];
                    } catch (\Throwable $e) {
                        echo 'Exception: ' . $e->getMessage() . PHP_EOL;
                    }
                } else {
                    $this->appPort = 0;
                    $this->appVer = 0;
                    $result = $this->findAppPort();
                }
            } catch (\Throwable $e) {
                echo 'Exception: ' . $e->getMessage() . PHP_EOL;
                $this->appPort = 0;
                $this->appVer = 0;
                $result = $this->findAppPort();
            }
        }

        return $result;
    }

    /**
     * Add download task using TCP/IP port.
     *
     * @param string $link    Address to download.
     * @param string $referer Referrer address.
     * @param string $cookies List of cookies.
     * @return bool Returns true when task request successfully sent and false otherwise.
     */
    private function addTaskUsingPort($link, $referer = '', $cookies = ''): bool
    {
        $port = $this->appPort;
        if ($port === 0) {
            $port = $this->findAppPort();
        }

        $result = false;

        if ($port !== 0) {
            try {
                $url = 'http://127.0.0.1:' . $port;
                $body = $this->getCommandString('psDownload', [
                    'url'     => $link,
                    'referer' => $referer,
                    'cookie'  => $cookies,
                ]);
                $response = $this->sendHttpPostRequest($url, $body);
                if ($response->status === 202) {
                    $result = true;
                }
            } catch (\Throwable $e) {
                echo 'Exception: ' . $e->getMessage() . PHP_EOL;
            }
        }

        return $result;
    }

    /**
     * @param string $url
     * @param string $body
     * @return object
     */
    private function sendHttpPostRequest(string $url, string $body = null)
    {
        $options = [
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER     => ['Content-type: text/plain'],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \RuntimeException(curl_error($ch), curl_errno($ch));
        }

        return (object) [
            'status'       => curl_getinfo($ch, CURLINFO_HTTP_CODE),
            'responseText' => $response,
            'responseJson' => json_decode($response, true),
        ];
    }
}
