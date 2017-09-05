<?php

namespace ADiesel82\GeoService;

use Composer\Script\Event;

class ComposerScripts
{
    protected static $event;

    /**
     * Handle the post-install Composer event.
     *
     * @param  \Composer\Script\Event $event
     * @return void
     */
    public static function postInstall(Event $event)
    {
        self::$event = $event;
        self::downloadDatabases();
    }

    /**
     * Handle the post-update Composer event.
     *
     * @param  \Composer\Script\Event $event
     * @return void
     */
    public static function postUpdate(Event $event)
    {
        self::$event = $event;
        self::downloadDatabases();
    }

    protected static function downloadDatabases()
    {

        $io = self::$event->getIO();

        $app = new \Illuminate\Foundation\Application();
        $config = require implode(DIRECTORY_SEPARATOR, [getcwd(), 'config', 'geo.php']);
        $driver = $config['driver'];

        if (isset($config[$driver]['source'])) {
            try {
                $io->write("<info>" . $driver . " database update</info>");
                static::download($config[$driver]['source'], $config['store_path'], $config[$driver]['filename']);
                $io->write("<info>SxGeo database update finished</info>");
            } catch (\Exception $e) {
                $io->write("<warning>" . $e->getMessage() . "</warning>");
            }
        }


    }

    protected static function download($downloadFrom, $destinationPath, $destinationFilename)
    {
        $io = self::$event->getIO();
        $io->write(sprintf("Database update url is `%s`...", $downloadFrom));
        $io->write("Starting download...");
        $tmpFilename = implode(DIRECTORY_SEPARATOR, [getcwd(), $destinationPath, 'tmp_' . basename($downloadFrom)]);

        $downloadFile = fopen($tmpFilename, "w");
        $last = null;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $downloadFrom);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_NOPROGRESS, 0);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function ($clientp, $dltotal, $dlnow) use ($io, &$last) {
            if ($dltotal != 0) {
                $now = number_format($dlnow / (1024 * 1024), 2);
                $total = number_format($dltotal / (1024 * 1024), 2);
                if ($last != $now) {
                    $percent = $now / ($total / 100);
                    $io->overwrite($now . "MB/" . $total . "MB" . ", " . number_format($percent, 2) . "%", false);
                    $last = $now;
                }
            }
        });
        curl_setopt($ch, CURLOPT_FILE, $downloadFile);
        $result = curl_exec($ch);
        if (!$result) {
            throw new \Exception(curl_error($ch));
        }
        curl_close($ch);
        $io->write(sprintf("Downloaded to `%s`.", $tmpFilename));
        $io->write("Download completed");

        $destinationFile = implode(DIRECTORY_SEPARATOR, [
            getcwd(),
            $destinationPath,
            $destinationFilename
        ]);

        if (stripos($tmpFilename, '.zip') !== false) {
            $io->write("Starting extraction...");
            $zip = new \ZipArchive();

            $zipResult = $zip->open($tmpFilename);
            if ($zipResult != true) {
                throw new \Exception("<error>Extraction failed: error code %s</error>", $zipResult);
            }
            $defaultFileName = $zip->getNameIndex(0);
            /* Extract Zip File */
            $zip->extractTo($destinationFile);
            $zip->close();
            unlink($tmpFilename);
        } else {
            if (!rename($tmpFilename, $destinationFile)) {
                throw new \Exception("Can't rename temporary file '$downloadFile' to '$destinationFile'");
            }
        }
        $io->write(sprintf("Fresh database you can find here `%s`.", $destinationFile));
    }
}