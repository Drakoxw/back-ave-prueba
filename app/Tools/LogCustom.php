<?php

namespace App\Tools;

use Illuminate\Support\Str;

class LogCustom
{
    private static function getRouteLogs(string $fileName): string
    {
        $fileName = Str::replace('.log', '', $fileName);

        return  __DIR__."/../../storage/logs/$fileName.log";
    }
    
    /**
     * Method searchFilesLogs
     *
     * @return array<string>
     */
    private static function searchFilesLogs(): array
    {
        $route = Str::replace('/void.log', '', self::getRouteLogs('void'));
        $files = [];
        if ($handler = opendir($route)) {
            while (false !== ($file = readdir($handler))) {
                if (! in_array($file, ['.', '..', '.gitignore'])) {
                    $files[] = $file;
                }
            }
            closedir($handler);
        }

        return $files;
    }

    public static function main(\Throwable $e, string $tittle, string $filename = '',string $level = 'ERROR'): void
    {
        $fileName = $filename == '' ? $filename : 'logErros';
        $date = now();
        $env = env('APP_ENV').".$level:";
        $msj = $e->getMessage();
        $code = $e->getCode();
        $save = "[$date] $env $tittle { message: $msj } \n";
        if ($code > 0) {
            $save = "[$date] $env $tittle { message: $msj, code: $code } \n";
        }
        $route = self::getRouteLogs($fileName);
        if ($fp = fopen($route, 'a')) {
            fwrite($fp, $save);
            fclose($fp);
        }
    }

    private static function getLog(string $fileName): string
    {
        $route = self::getRouteLogs($fileName);

        return (string)file_get_contents($route);
    }
    
    /**
     * Method getAllApiLogs
     *
     * @return array<array<string, string>>
     */
    public static function getAllApiLogs(): array
    {
        $allLogs = self::searchFilesLogs();
        $dataLogs = [];
        foreach ($allLogs as $log) {
            if ($value = self::getLog($log)) {
                $data = [
                    'value' => $value,
                    'fileName' => $log,
                ];
                $dataLogs[] = $data;
            }
        }

        return $dataLogs;
    }
}
