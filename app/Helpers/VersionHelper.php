<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class VersionHelper
{
    public static function getVersion()
    {
        return Cache::remember(__METHOD__, 1440, function () {
            $versionFile = base_path('version.txt');
            return file_exists($versionFile) ? trim(file_get_contents($versionFile)) : 'unknown';
        });
    }
}
